# CLAUDE.md Project Guidelines

## Project Context
This is the **Codex Loopy Harness v2** — a reusable workspace template for AI-powered multi-agent development with automatic skill activation, edit tracking, and 9 specialist agents. Copy this directory into any new project to get the full harness system.

## Architecture Summary
- **11 Agents**: 5 Core + 4 Extended + 2 Additional (plan-reviewer, code-refactor-master)
- **9 Skills**: init-project, team, qa-cycle, discover-skills, frontend-dev-guidelines, backend-dev-guidelines, skill-developer, route-tester, error-tracking + auto-activated via skill-rules.json (18 rules)
- **7 Commands**: `/team`, `/init`, `/skillers-suda`, `/kkirikkiri`, `/vibe-sunsang`, `/insane-search`, `/dev-docs`
- **16 Hooks**: Automated validation gates + skill activation + edit tracking + error handling + build checks

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
- Edit tracking data stored in `.claude-cache/{session_id}/` (gitignored).

## Workflow
See `docs/harness/introduction.md` for full system overview.
See `docs/harness/workflow-comparison.md` for the recommended hybrid 3-Phase workflow.
See `AGENTS.md` for the complete agent roster, hook system, and fail-closed rules.

## v2 New Features
1. **Skill Auto-Activation**: `skill-activation-prompt.sh` hook reads `skills/skill-rules.json` and suggests matching skills based on user prompt keywords and intent patterns.
2. **Edit Tracking**: `post-tool-use-tracker.sh` hook logs all code edits per session, detects hot-spots, and warns about cross-area edits.
3. **4 Extended Agents**: auto-error-resolver, code-architecture-reviewer, refactor-planner, documentation-architect.
4. **12 Active Skills**: Domain skills + guardrail skills with priority-based activation.