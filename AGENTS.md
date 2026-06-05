# Codex Loopy Harness v2 Guidelines

This workspace uses a Hugh-inspired, verification-first Codex harness with automatic skill activation.

## Core Rule
Tools are weaker than structure. Structure is weaker than verification structure.
Do not treat an agent's claim of completion as proof. Use files, logs, tests, screenshots, and evidence records as the source of truth.

---

## 1. System 3-Layer Architecture

1. **Layer 1: Manager-Orchestrator** (Opus model): Performs planning, task allocation, reviews, and QA loops. **Rule: Never writes production code directly.** Also handles architecture, documentation, and product specs.
2. **Layer 2: Specialist Agents** (Sonnet model): Performs specific tasks within boundaries. **9 agents** organized into Core Team (5) and Extended Team (4).
3. **Layer 3: Automated Hooks** (Deterministic Gates): Shell/TypeScript scripts triggered at UserPromptSubmit, PostToolUse, and Stop to enforce policies, track changes, and auto-activate skills.

---

## 2. Specialist Agents (9)

### Core Team (5)

| Agent | Role | Absorbed Roles |
|-------|------|---------------|
| `manager-orchestrator` | Team lead — planning, delegation, verification | Architect, Documentation, Product Specifier |
| `frontend-specialist` | Frontend development + performance + accessibility | Performance Optimizer, UI/UX Implementer |
| `backend-specialist` | Backend development + DB admin + API design | Supabase Specialist, Bug Fixer, API Architect |
| `qa-specialist` | QA + test writing + code review + closed-loop | Test Writer, Code Reviewer |
| `devops-specialist` | DevOps + security + deployment + notifications | Security Specialist, Telegram Notifier, Infra Engineer |

### Extended Team (6)

| Agent | Role | Trigger |
|-------|------|---------|
| `auto-error-resolver` | Build/runtime error analysis and fixing | Build failures, type errors |
| `code-architecture-reviewer` | Architecture quality review | Pre-merge, tech debt |
| `refactor-planner` | Refactoring strategy and planning | Architecture issues, migrations |
| `documentation-architect` | Documentation maintenance and accuracy | API changes, releases, stale docs |
| `plan-reviewer` | Plan quality validation before implementation | Before implementation starts |
| `code-refactor-master` | Code refactoring execution | Architecture issues, code smells |

### File Boundary Rules

- **frontend-specialist:** Allowed `src/components/**`, `src/pages/**`, `src/hooks/**`. Blocked `src/api/**`, `src/lib/server/**`.
- **backend-specialist:** Allowed `src/api/**`, `src/lib/server/**`, `src/middleware/**`, `supabase/**`. Blocked `src/components/**`, `src/pages/**`.
- **qa-specialist:** Allowed `__tests__/**`, `test/**`, `docs/qa-reports/**`, `docs/reviews/**`. Blocked all source code.
- **devops-specialist:** Allowed `Dockerfile`, `.github/workflows/**`, `scripts/**`, `docs/security/**`. Blocked `src/**`.
- **auto-error-resolver:** Allowed to edit source files for error fixes only. Blocked `docs/**`.
- **code-architecture-reviewer:** Read-only. Writes to `docs/reviews/**` only.
- **refactor-planner:** Read-only. Writes to `docs/refactoring/**` and `docs/plan.md` only.
- **documentation-architect:** Allowed `docs/**`, `README.md`, `CHANGELOG.md`, `CLAUDE.md`. Blocked `src/**`.

---

## 3. 8-Phase Execution Rules

Every development lifecycle moves sequentially through these phases:
- **P1: Plan Scaffolding** → Manager defines tasks and requirements in `docs/plan.md`.
- **P2: Architecture Design** → Manager sets up files, scripts, and initial dependencies.
- **P3: Database Schema** → Backend Specialist writes Postgres migrations and RLS rules.
- **P4: Parallel Implementation** → Frontend and Backend work concurrently.
- **P5: Test Suite Generation** → QA Specialist implements Vitest/Jest/E2E test files.
- **P6: Static Review** → Code Architecture Reviewer inspects for design patterns and clean code.
- **P7: Closed-Loop QA** → QA Specialist identifies findings, routes them to specialists, and reruns tests until green.
- **P8: Ship & Notify** → Manager logs changes in `CHANGELOG.md`, commits, pushes, and triggers notifications.

---

## 4. Skill Auto-Activation System (v2 NEW)

Skills are automatically activated via the `skill-activation-prompt` hook:

### How It Works
1. **UserPromptSubmit hook** reads `skills/skill-rules.json`
2. Matches user prompt against **keywords** and **intent patterns** (regex)
3. Suggests matching skills grouped by priority (critical/high/medium/low)
4. Agent uses Skill tool to activate before responding

### skill-rules.json Structure
```json
{
  "skills": {
    "skill-name": {
      "type": "domain" | "guardrail",
      "enforcement": "suggest" | "block" | "warn",
      "priority": "critical" | "high" | "medium" | "low",
      "promptTriggers": {
        "keywords": ["..."],
        "intentPatterns": ["regex..."]
      },
      "fileTriggers": {
        "pathPatterns": ["glob..."],
        "pathExclusions": ["glob..."]
      }
    }
  }
}
```

### Active Skills (18 Rules)

| Skill | Purpose | Priority |
|-------|---------|----------|
| `team` | 8-Phase SDLC orchestration pipeline | Critical |
| `init-project` | Project codebase profiling and setup | High |
| `qa-cycle` | QA scenario generation + automated testing | High |
| `insane-search` | Auto-bypass blocked websites | High |
| `skillers-suda` | Create production-ready skills | High |
| `kkirikkiri` | AI agent team auto-configuration | High |
| `nextjs-frontend-guidelines` | Next.js best practices | High |
| `vercel-react-best-practices` | React performance optimization | High |
| `frontend-dev-guidelines` | Comprehensive frontend dev guide (patterns, styling, routing) | High |
| `backend-dev-guidelines` | Comprehensive backend dev guide (architecture, DB, validation) | High |
| `error-tracking` | Error tracking, analysis, and resolution | High |
| `vibe-sunsang` | AI growth mentor | Medium |
| `fastapi-backend-guidelines` | FastAPI development guide | Medium |
| `improve-codebase-architecture` | Architecture improvement | Medium |
| `frontend-design` | Production-grade UI design | Medium |
| `skill-developer` | Custom skill creation guide with hook mechanisms | Medium |
| `route-tester` | Automated route/API endpoint testing | Medium |

---

## 5. Hook System (16 Hooks)

### UserPromptSubmit Hooks
| Hook | Purpose |
|------|---------|
| `context-enrichment.sh` | Enrich context for agent processing |
| `skill-activation-prompt.sh` | **v2 NEW** — Auto-suggest skills based on prompt |

### PostToolUse Hooks (Edit/Write)
| Hook | Purpose |
|------|---------|
| `agent-permission-check.sh` | Verify agent file boundary permissions |
| `no-localstorage.sh` | Block localStorage usage |
| `auto-validate.sh` | Auto-validate edited files |
| `require-isPWA-check.sh` | Enforce PWA requirements |
| `scaffold-violation-check.sh` | Detect scaffold violations |
| `post-tool-use-tracker.sh` | **v2 NEW** — Track file changes per session |
| `error-handling-reminder.sh` | Remind about error handling patterns |

### PostToolUse Hooks (Bash)
| Hook | Purpose |
|------|---------|
| `require-telegram-notify.sh` | Require Telegram notification for commands |

### Stop Hooks
| Hook | Purpose |
|------|---------|
| `session-cleanup.sh` | Clean up session artifacts |

### Additional Hooks (Available for Advanced Use)
| Hook | Purpose |
|------|---------|
| `trigger-build-resolver.sh` | Auto-trigger error resolver on build failure |
| `stop-build-check-enhanced.sh` | Enhanced build check on session stop |
| `tsc-check.sh` | TypeScript type checking gate |

---

## 6. Commands

| Command | Purpose |
|---------|---------|
| `/team` | All development workflows (8-phase SDLC) |
| `/init` | Project initialization |
| `/skillers-suda` | Create new skills |
| `/kkirikkiri` | Build agent teams |
| `/vibe-sunsang` | AI growth mentor |
| `/insane-search` | Bypass blocked websites |

---

## 7. Fail-Closed Rules

Work cannot be completed or merged if any of the following occur:
- Acceptance criteria are missing for non-trivial tasks.
- Mandatory build verification, tsc check, or linting fails.
- A **CRITICAL** or **HIGH** severity QA finding remains unresolved.
- Verification commands specified in the contract are not run.
- Code edits violate declared file boundaries.

---

## 8. Memory Rule
Capture durable facts in the SQLite memory-bank only:
- `decision`: Choices and technical justifications.
- `preference`: Developer/user preferences.
- `pattern`: Reusable code structures or failure modes.
- `constraint`: Fixed environment limitations.
- `knowledge`: Stable project facts.

---

## 9. Edit Tracking (v2 NEW)

File edits are tracked per session in `.claude-cache/{session_id}/`:
- `edit-log.tsv` — Timestamped log of all code edits
- `affected-areas.txt` — Deduplicated list of project areas modified
- `file-types.txt` — Types of files edited (component, script, etc.)
- `edit-counts.txt` — Edit frequency per area (for hot-spot detection)

This data enables:
- **Build verification** after edits
- **Hot-spot detection** (areas with excessive edits)
- **Cross-area warnings** (editing frontend + backend simultaneously)