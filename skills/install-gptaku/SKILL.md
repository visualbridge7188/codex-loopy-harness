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
