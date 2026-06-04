# gptaku Plugin Installer Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a script and an Antigravity skill that allows installing Claude Code plugins from the gptaku ecosystem into the Antigravity plugin structure.

**Architecture:** Create a Node.js script that clones gptaku repos, maps their configuration/skills/commands structure into the Antigravity plugin structure under `~/.gemini/config/plugins/`, and define an Antigravity skill to let the agent invoke this script.

**Tech Stack:** Node.js, Git, ES Modules

---

### Task 1: Create the Installer Script

**Files:**
- Create: `scripts/install-gptaku.mjs`

- [ ] **Step 1: Write the installer script code**
  Write the following code into [scripts/install-gptaku.mjs](file:///Users/parkjuncheol/Local%20Sites/AI%20Agent/hugh.kim/scripts/install-gptaku.mjs).

  ```javascript
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
  ```

- [ ] **Step 2: Commit the installer script**
  Run:
  ```bash
  git add scripts/install-gptaku.mjs
  git commit -m "feat: add gptaku plugin installer script"
  ```

---

### Task 2: Create the Antigravity Skill for Plugin Installer

**Files:**
- Create: `skills/install-gptaku/SKILL.md`

- [ ] **Step 1: Write the SKILL.md file**
  Write the following content into [skills/install-gptaku/SKILL.md](file:///Users/parkjuncheol/Local%20Sites/AI%20Agent/hugh.kim/skills/install-gptaku/SKILL.md).

  ```markdown
  ---
  name: install-gptaku
  description: Automatically download and install Claude Code plugins from the gptaku ecosystem to Antigravity's plugin directory. Triggers when the user asks to "install plugin X" or "install gptaku plugin".
  ---

  # Install Gptaku Plugin

  This skill allows the agent to automatically convert and install Claude Code plugins for use within Antigravity.

  ## Activation

  Activate this skill when:
  - The user requests to install a gptaku plugin or a plugin from a repository (e.g., "install vibe-sunsang", "gptaku show-me-the-prd 설치해줘").

  ## Execution Steps

  1. Identify the plugin's repository URL. If the user only gave a name (e.g., `vibe-sunsang`), use the default mappings or search Github/gptaku:
     - `show-me-the-prd` -> `https://github.com/fivetaku/show-me-the-prd.git`
     - `vibe-sunsang` -> `https://github.com/fivetaku/vibe-sunsang.git`
     - `docs-guide` -> `https://github.com/fivetaku/docs-guide.git`
     - `deep-research` -> `https://github.com/fivetaku/deep-research-kit.git`
     - `goaljaby` -> `https://github.com/fivetaku/goaljaby.git`
     - Otherwise, search Google/GitHub for `fivetaku/[plugin-name]`.
  2. Run the installer script:
     ```bash
     node scripts/install-gptaku.mjs <repo-url>
     ```
  3. Inform the user of the successful installation and describe what new skills/commands are now available under the plugin.
  ```

- [ ] **Step 2: Commit the install-gptaku skill**
  Run:
  ```bash
  git add skills/install-gptaku/SKILL.md
  git commit -m "feat: add install-gptaku skill metadata"
  ```

---

### Task 3: Create Korean Documentation (README.ko.md)

**Files:**
- Create: `README.ko.md`

- [ ] **Step 1: Write the README.ko.md file**
  Write the following content into [README.ko.md](file:///Users/parkjuncheol/Local%20Sites/AI%20Agent/hugh.kim/README.ko.md).

  ```markdown
  # Antigravity gptaku-plugins Bridge

  이 프로젝트는 Claude Code 전용 플러그인 마켓플레이스인 **gptaku-plugins**의 플러그인들을 Google Antigravity/Codex 환경에 맞게 변환하여 설치해주는 브릿지 도구입니다.

  ## 주요 기능
  - **플러그인 자동 변환 설치**: `.claude-plugin/plugin.json`을 `plugin.json`으로 이식하고, `installed_version.json`을 생성하여 Antigravity에 올바르게 연동합니다.
  - **슬래시 명령어 스킬 이식**: Claude Code 전용 명령어(`commands/*.md`)를 Antigravity가 이해할 수 있는 개별 스킬(`skills/`) 형태로 변환하여, 사용자가 자연어로 해당 명령어를 실행할 수 있도록 호환성을 부여합니다.
  - **자체 연동 스킬 제공**: Antigravity 내에서 `install-gptaku` 스킬을 탑재하여, 대화 중에 즉시 다른 플러그인을 설치할 수 있게 돕습니다.

  ## 설치 및 사용법

  ### 1. 개별 플러그인 설치
  터미널에서 아래 스크립트를 직접 실행하여 원하는 플러그인을 설치할 수 있습니다:
  ```bash
  node scripts/install-gptaku.mjs <플러그인 Git 저장소 주소>
  ```

  **예시:**
  ```bash
  node scripts/install-gptaku.mjs https://github.com/fivetaku/show-me-the-prd.git
  ```

  ### 2. Antigravity에서 대화로 설치하기
  이 프로젝트의 스킬이 로드된 상태에서는 다음과 같이 에이전트에게 설치를 요청할 수 있습니다:
  - *"show-me-the-prd 플러그인 설치해줘"*
  - *"gptaku vibe-sunsang 설치"*

  에이전트가 알아서 해당 레포지토리를 찾아 설치 스크립트를 구동하고 로드합니다.
  ```

- [ ] **Step 2: Commit README.ko.md**
  Run:
  ```bash
  git add README.ko.md
  git commit -m "docs: add Korean README for gptaku bridge"
  ```

---

### Task 4: Register Skill and Test Installation

**Files:**
- Modify: `docs/verification/skills-registry.md`

- [ ] **Step 1: Add install-gptaku to skills-registry.md**
  Add the following line to [docs/verification/skills-registry.md](file:///Users/parkjuncheol/Local%20Sites/AI%20Agent/hugh.kim/docs/verification/skills-registry.md) under the "Adopted In Current Codex Session" section:
  ```markdown
  | install-gptaku | skill | Installer script to convert and install Claude Code plugins into Antigravity. |
  ```

- [ ] **Step 2: Test installer script on show-me-the-prd**
  Run:
  ```bash
  node scripts/install-gptaku.mjs https://github.com/fivetaku/show-me-the-prd.git
  ```
  Expected: Command completes successfully, outputting:
  `Plugin show-me-the-prd successfully installed!`

- [ ] **Step 3: Verify output folders**
  Run:
  ```bash
  ls -la ~/.gemini/config/plugins/show-me-the-prd
  ls -la ~/.gemini/config/plugins/show-me-the-prd/skills
  ```
  Expected:
  - `plugin.json` and `installed_version.json` are present.
  - `skills/show-me-the-prd` and `skills/show-me-the-prd-command` directories exist.

- [ ] **Step 4: Commit verification findings**
  Run:
  ```bash
  git add docs/verification/skills-registry.md
  git commit -m "chore: update skills registry and verify installer"
  ```
