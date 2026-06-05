# Codex Loopy Harness Guidelines

This workspace uses a Hugh-inspired, verification-first Codex harness.

## Core Rule
Tools are weaker than structure. Structure is weaker than verification structure.
Do not treat an agent's claim of completion as proof. Use files, logs, tests, screenshots, and evidence records as the source of truth.

---

## 1. System 3-Layer Architecture

1. **Layer 1: Manager-Orchestrator** (Opus model): Performs planning, task allocation, reviews, and QA loops. **Rule: Never writes production code directly.** Also handles architecture, documentation, and product specs.
2. **Layer 2: Specialist Agents** (Sonnet model): Performs specific tasks within boundaries.
3. **Layer 3: Automated Hooks** (Deterministic Gates): Shell scripts triggered post-tool use to immediately enforce policies (Lint, localStorage checks, permission locks, SQL sanitization).

---

## 2. Specialist Agents (5)

| Agent | Role | Absorbed Roles |
|-------|------|---------------|
| `manager-orchestrator` | Team lead — planning, delegation, verification | Architect, Documentation, Product Specifier |
| `frontend-specialist` | Frontend development + performance optimization | Performance Optimizer |
| `backend-specialist` | Backend development + DB admin + bug fixing | Supabase Specialist, Bug Fixer |
| `qa-specialist` | QA + test writing + code review | Test Writer, Code Reviewer |
| `devops-specialist` | DevOps + security + notifications | Security Specialist, Telegram Notifier |

### File Boundary Rules

- **frontend-specialist:** Allowed `src/components/**`, `src/pages/**`, `src/hooks/**`. Blocked `src/api/**`, `src/lib/server/**`.
- **backend-specialist:** Allowed `src/api/**`, `src/lib/server/**`, `src/middleware/**`, `supabase/**`. Blocked `src/components/**`, `src/pages/**`.
- **qa-specialist:** Allowed `__tests__/**`, `test/**`, `docs/qa-reports/**`, `docs/reviews/**`. Blocked all source code.
- **devops-specialist:** Allowed `Dockerfile`, `.github/workflows/**`, `scripts/**`, `docs/security/**`. Blocked `src/**`.

---

## 3. 8-Phase Execution Rules

Every development lifecycle moves sequentially through these phases:
- **P1: Plan Scaffolding** → Manager defines tasks and requirements in `docs/plan.md`.
- **P2: Architecture Design** → Manager sets up files, scripts, and initial dependencies.
- **P3: Database Schema** → Backend Specialist writes Postgres migrations and RLS rules.
- **P4: Parallel Implementation** → Frontend and Backend work concurrently.
- **P5: Test Suite Generation** → QA Specialist implements Vitest/Jest/E2E test files.
- **P6: Static Review** → QA Specialist inspects files for design patterns and clean code principles.
- **P7: Closed-Loop QA** → QA Specialist identifies findings, routes them to specialists, and reruns tests until green.
- **P8: Ship & Notify** → Manager logs changes in `CHANGELOG.md`, commits, pushes, and triggers notifications.

---

## 4. Skills (4)

| Skill | Purpose |
|-------|---------|
| `init-project` | Project codebase profiling and initial setup |
| `team` | 8-Phase SDLC orchestration pipeline |
| `qa-cycle` | QA scenario generation + automated multi-round testing (up to 5 rounds) |
| `discover-skills` | External skill/tool search (on user request) |

---

## 5. Commands (2)

| Command | Purpose |
|---------|---------|
| `/team` | All development workflows (includes project-orchestrator functionality) |
| `/init` | Project initialization (links to init-project skill) |

---

## 6. Fail-Closed Rules

Work cannot be completed or merged if any of the following occur:
- Acceptance criteria are missing for non-trivial tasks.
- Mandatory build verification, tsc check, or linting fails.
- A **CRITICAL** or **HIGH** severity QA finding remains unresolved.
- Verification commands specified in the contract are not run.
- Code edits violate declared file boundaries.

---

## 7. Memory Rule
Capture durable facts in the SQLite memory-bank only:
- `decision`: Choices and technical justifications.
- `preference`: Developer/user preferences.
- `pattern`: Reusable code structures or failure modes.
- `constraint`: Fixed environment limitations.
- `knowledge`: Stable project facts.