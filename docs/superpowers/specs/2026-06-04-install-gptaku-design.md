# Antigravity-gptaku Bridge & Installer Design

## Overview
This document designs a bridge tool and an Antigravity skill (`install-gptaku`) that allows installing Claude Code plugins from the `gptaku-plugins` ecosystem (or any Claude Code plugin git repository) into the Google Antigravity/Codex environment.

## User Review Required
No breaking changes are introduced. This tool will install new plugins to `~/.gemini/config/plugins/`, which does not affect existing plugins.

## Proposed Changes

### Folder Mapping Design
We map the Claude Code plugin structure to the Antigravity plugin structure as follows:
```
Source (Claude Code Plugin)                Target (Antigravity Plugin)
├── .claude-plugin/                        ├── plugin.json (copied from .claude-plugin/plugin.json)
│   └── plugin.json                        └── installed_version.json (generated)
├── commands/                              └── skills/
│   └── [command-name].md       ──▶            └── [command-name]-command/
│                                                  └── SKILL.md (copied & renamed)
└── skills/                                └── skills/
    └── [skill-name]/                          └── [skill-name]/
        └── SKILL.md                               └── SKILL.md
```

### Component Breakdown

1. **Installer Script (`scripts/install-gptaku.mjs`)**:
   - Downloads a specified git repository (e.g., `https://github.com/fivetaku/show-me-the-prd.git`) into a temporary folder.
   - Parses `.claude-plugin/plugin.json`.
   - Creates the destination directory under `~/.gemini/config/plugins/<plugin-name>`.
   - Copies `plugin.json` and generates `installed_version.json`.
   - Copies existing `skills/` to the destination.
   - Converts any `commands/[command-name].md` files into a skill under `skills/[command-name]-command/SKILL.md`.
   - Cleans up the temporary directory.

2. **Antigravity Skill (`skills/install-gptaku/SKILL.md`)**:
   - Explains to the AI agent how to execute the installer script.
   - Activates when the user requests to install a gptaku/Claude Code plugin.

3. **Korean Documentation (`README.ko.md`)**:
   - Explains what this bridge does, how to use it, and provides examples.

## Verification Plan
1. Run `node scripts/install-gptaku.mjs https://github.com/fivetaku/show-me-the-prd.git` to install `show-me-the-prd`.
2. Verify that the files are properly placed in `/Users/parkjuncheol/.gemini/config/plugins/show-me-the-prd`.
3. Check if the skills are recognized by Antigravity in the next session (or verify structure matches other working plugins).
