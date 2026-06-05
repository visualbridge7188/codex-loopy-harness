# CLAUDE.md Project Guidelines

## Project Context
This is the **Codex Loopy Harness** — a reusable workspace template for AI-powered multi-agent development. Copy this directory into any new project to get the full harness system with agents, skills, hooks, and commands.

## Key Commands
- Sync active settings to workspace: `bash scripts/sync.sh`
- Apply workspace settings to active session: `bash scripts/apply.sh`
- Validate all hooks syntax: `for f in hooks/*.sh scripts/*.sh; do bash -n "$f"; done`

## Environment & Constraints
- Keep all hooks in `/hooks/` pure bash/shell script files.
- Keep all specialist personas in `/agents/` as clean markdown.
- Do not commit private API keys, Telegram Bot Tokens, or secrets. Use environment variables.
- All hook validations must fail closed by exiting with code `2` to block invalid tools/commits.
- Maintain the single-source-of-truth file principles.

## Workflow
See `docs/harness/introduction.md` for full system overview.
See `docs/harness/workflow-comparison.md` for the recommended hybrid 3-Phase workflow.