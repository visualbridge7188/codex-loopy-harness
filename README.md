# Claude Code Harness System

> **Loopy-Era Autonomous Self-Improving Infrastructure**  
> Inspired by Hugh Kim's multi-agent orchestration harness system. Designed to turn Claude Code into a verification-first personal Work OS.

---

## 1. System Architecture (3 Layers)

```
┌─────────────────────────────────────────────────────────────────────────┐
│  👤 User Input (e.g., "Build a fullstack Todo App")                     │
└───────────────────────────────┬─────────────────────────────────────────┘
                                │
        ┌───────────────────────▼───────────────────────┐
        │     🧠 Layer 1: Manager-Orchestrator (Opus)   │
        │  Plans → Delegates → Verifies → QA → Ships     │
        │  * Rule: Never writes production code directly │
        └───────────────────────┬───────────────────────┘
                                │ task() invocation
        ┌───────────────────────▼───────────────────────┐
        │     🔧 Layer 2: Specialist Agents (Sonnet)     │
        │  [architect] [frontend] [backend] [supabase]  │
        │  [test-writer] [qa-tester] [bug-fixer] [devops]│
        │  * Constrained by strict file-modify boundaries│
        └───────────────────────┬───────────────────────┘
                                │ Triggered on Write / Edit / Bash
        ┌───────────────────────▼───────────────────────┐
        │     🛡️ Layer 3: Hooks (Deterministic Gates)    │
        │  [auto-validate] [no-localstorage]            │
        │  [agent-permission] [sql-injection] [telegram] │
        │  * Blocks commit/execution if rules fail       │
        └───────────────────────────────────────────────┘
```

---

## 2. 8-Phase Workflow

1. **P1: Plan Scaffolding** (`manager-orchestrator`): Creates `docs/plan.md` and initializes tasks.
2. **P2: Architecture Design** (`architect-designer`): Lays out folder structure, configuration files, and package dependencies.
3. **P3: Database Schema** (`supabase-specialist`): Generates SQL migrations, indices, and Row Level Security (RLS) policies.
4. **P4: Parallel Implementation** (`frontend-specialist` & `backend-specialist`): Frontend and backend work in parallel within strict file boundaries.
5. **P5: Test Suite Generation** (`test-writer`): Writes unit/integration/E2E test files. Retries via `bug-fixer` on failure.
6. **P6: Static Review** (`code-reviewer`): Inspects codebase for logic issues and coding standard compliance.
7. **P7: Closed-Loop QA** (`web-qa-tester` & `security-specialist`): Captures E2E findings, parses severities, routes issues back to specialists for automated fixes.
8. **P8: Changelog & Ship** (`manager-orchestrator`): Generates `CHANGELOG.md`, commits changes, pushes to repository, and notifies the user via Telegram.

---

## 3. Synchronizing Your Harness

All settings, specialist profiles, hooks, and skills are stored in this workspace repository. Run the following helper scripts to sync changes between the repository and the active Claude Code configuration directory (`~/.claude/`):

### Apply settings from Git project to Claude Code
Deploys all agents, hooks, rules, and commands to `~/.claude/` and sets execution permissions:
```bash
./scripts/apply.sh
```

### Sync settings from Claude Code back to Git project
Backs up your active active settings and logs from `~/.claude/` to this repository:
```bash
./scripts/sync.sh
```

---

## 4. Harness Directory Structure

- `agents/`: Guideline profiles for the 14 specialist subagents.
- `hooks/`: Bash scripts for validation gates and event automation.
- `skills/`: Core pipelines (`/init-project`, `/team`, `/qa-scenario-gen`, `/qa-cycle`, `/self-improve`, `/loopy-era-eval`, `/harness-report`).
- `commands/`: CLI command mappings.
- `rules/`: Coding standards and strategies.
- `scripts/`: System sync/apply helpers.
