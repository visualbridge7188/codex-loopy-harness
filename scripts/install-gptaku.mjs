import { execSync } from 'child_process';
import fs from 'fs/promises';
import os from 'os';
import path from 'path';

const repoUrl = process.argv[2];
if (!repoUrl) {
  console.error('Error: Please provide a git repository URL.');
  console.error('Usage: node scripts/install-gptaku.mjs <repo-url>');
  process.exit(1);
}

const homedir = os.homedir();
const pluginsDir = path.join(homedir, '.gemini/config/plugins');
const tempDir = path.join(process.cwd(), 'scratch', `tmp_clone_${Date.now()}`);

async function copyDir(src, dest) {
  await fs.mkdir(dest, { recursive: true });
  const entries = await fs.readdir(src, { withFileTypes: true });

  for (const entry of entries) {
    const srcPath = path.join(src, entry.name);
    const destPath = path.join(dest, entry.name);

    if (entry.isDirectory()) {
      await copyDir(srcPath, destPath);
    } else {
      await fs.copyFile(srcPath, destPath);
    }
  }
}

async function main() {
  try {
    console.log(`Cloning ${repoUrl} into temporary directory...`);
    execSync(`git clone ${repoUrl} "${tempDir}"`, { stdio: 'inherit' });

    // Read plugin.json from .claude-plugin/plugin.json
    const manifestPath = path.join(tempDir, '.claude-plugin', 'plugin.json');
    let manifestExists = false;
    try {
      await fs.access(manifestPath);
      manifestExists = true;
    } catch {}

    if (!manifestExists) {
      throw new Error('Not a valid Claude Code plugin: .claude-plugin/plugin.json is missing.');
    }

    const manifestContent = await fs.readFile(manifestPath, 'utf8');
    const manifest = JSON.parse(manifestContent);
    const pluginName = manifest.name;

    if (!pluginName) {
      throw new Error('Plugin name is missing in plugin.json.');
    }

    const destPluginDir = path.join(pluginsDir, pluginName);
    console.log(`Installing ${pluginName} to ${destPluginDir}...`);

    // Ensure clean install
    try {
      await fs.rm(destPluginDir, { recursive: true, force: true });
    } catch {}
    await fs.mkdir(destPluginDir, { recursive: true });

    // 1. Copy plugin.json
    await fs.copyFile(manifestPath, path.join(destPluginDir, 'plugin.json'));

    // 2. Generate installed_version.json
    const versionInfo = { version: manifest.version || '1.0.0' };
    await fs.writeFile(
      path.join(destPluginDir, 'installed_version.json'),
      JSON.stringify(versionInfo, null, 2)
    );

    // 3. Copy existing skills if they exist
    const srcSkillsDir = path.join(tempDir, 'skills');
    let srcSkillsExist = false;
    try {
      const stat = await fs.stat(srcSkillsDir);
      if (stat.isDirectory()) srcSkillsExist = true;
    } catch {}

    if (srcSkillsExist) {
      await copyDir(srcSkillsDir, path.join(destPluginDir, 'skills'));
    }

    // 4. Convert commands to skills
    const srcCommandsDir = path.join(tempDir, 'commands');
    let srcCommandsExist = false;
    try {
      const stat = await fs.stat(srcCommandsDir);
      if (stat.isDirectory()) srcCommandsExist = true;
    } catch {}

    if (srcCommandsExist) {
      const commands = await fs.readdir(srcCommandsDir);
      for (const file of commands) {
        if (file.endsWith('.md')) {
          const commandName = path.basename(file, '.md');
          const skillDir = path.join(destPluginDir, 'skills', `${commandName}-command`);
          await fs.mkdir(skillDir, { recursive: true });
          
          const srcCommandPath = path.join(srcCommandsDir, file);
          const destSkillPath = path.join(skillDir, 'SKILL.md');
          await fs.copyFile(srcCommandPath, destSkillPath);
          console.log(`Converted command ${file} to skill at ${destSkillPath}`);
        }
      }
    }

    console.log(`Plugin ${pluginName} successfully installed!`);
  } catch (error) {
    console.error('Installation failed:', error.message);
  } finally {
    // Clean up temporary clone directory
    try {
      await fs.rm(tempDir, { recursive: true, force: true });
    } catch {}
  }
}

main();
