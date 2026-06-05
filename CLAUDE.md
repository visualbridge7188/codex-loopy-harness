# CLAUDE.md Project Guidelines

## Project Context
This is the master template repository for the Claude Code Harness system. It defines the personas, hooks, custom commands, and workflows that govern the multi-agent orchestration.

## Key Build & Script Commands
- Sync active settings to workspace: `bash scripts/sync.sh`
- Apply workspace settings to active Claude Code: `bash scripts/apply.sh`
- Run integration tests: `node --test test/install-gptaku.test.mjs`
- Test syntax checks for all hooks: `for f in hooks/*.sh scripts/*.sh; do bash -n "$f"; done`

## Environment & Constraints
- Keep all hooks in `/hooks/` pure bash/shell script files.
- Keep all specialist personas in `/agents/` as clean markdown.
- Do not commit private API keys, Telegram Bot Tokens, or secrets to the repository. Use environment variable binding.
- All hook validations must fail closed by exiting with code `2` to block invalid tools/commits.
- Maintain the single-source-of-truth file principles.
