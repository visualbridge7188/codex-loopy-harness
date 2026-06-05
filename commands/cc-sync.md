---
description: Synchronize active settings from ~/.claude/ to the git workspace
---

# /cc-sync

Triggers the sync script to copy configurations and logs from the active Claude Code scope to the workspace repository.

## Usage

```bash
/cc-sync [--auto]
```

## Options
- `--auto`: Automatically performs git add, commit, and push if changes are detected in workspace configurations.
