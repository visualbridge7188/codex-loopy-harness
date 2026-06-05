# Codex Loopy Harness Guidelines

This workspace uses a Hugh-inspired, verification-first Codex harness.

## Core Rule
Tools are weaker than structure. Structure is weaker than verification structure.
Do not treat an agent's claim of completion as proof. Use files, logs, tests, screenshots, and evidence records as the source of truth.

---

## 1. System 3-Layer Architecture

1. **Layer 1: Manager-Orchestrator** (Opus model): Performs planning, task allocation, reviews, and QA loops. **Rule: Never writes production code directly.**
2. **Layer 2: Specialist Agents** (Sonnet model): Performs specific tasks within boundaries (Architect, Frontend, Backend, Database, QA, Security, DevOps, etc.).
3. **Layer 3: Automated Hooks** (Deterministic Gates): Shell scripts triggered post-tool use to immediately enforce policies (Lint, localStorage checks, permission locks, SQL sanitization).

---

## 2. 8-Phase Execution Rules

Every development lifecycle moves sequentially through these phases:
- **P1: Plan Scaffolding** → Manager defines tasks and requirements in `docs/plan.md`.
- **P2: Architecture Design** → Architect sets up files, scripts, and initial dependencies.
- **P3: Database Schema** → Supabase Specialist writes Postgres migrations and RLS rules.
- **P4: Parallel Implementation** → Frontend and Backend work concurrently.
- **P5: Test Suite Generation** → Test Writer implements Vitest/Jest/E2E test files.
- **P6: Static Review** → Reviewer inspects files for design patterns and clean code principles.
- **P7: Closed-Loop QA** → QA Tester and Security Specialist identify findings, map them to specialists, and rerun tests until green.
- **P8: Ship & Notify** → Manager logs changes in `CHANGELOG.md`, commits, pushes, and triggers notifications.

---

## 3. Specialist Boundary Rules

Specialists must only edit files within their designated boundaries:
- **frontend-specialist:** Allowed `src/components/**`, `src/pages/**`, `src/hooks/**`. Blocked `src/api/**`, `src/lib/server/**`.
- **backend-specialist:** Allowed `src/api/**`, `src/lib/server/**`, `src/middleware/**`. Blocked `src/components/**`, `src/pages/**`.
- **supabase-specialist:** Allowed `supabase/migrations/**`, `supabase/config.toml`. Blocked all `src/**`.

---

## 4. Fail-Closed Rules

Work cannot be completed or merged if any of the following occur:
- Acceptance criteria are missing for non-trivial tasks.
- Mandatory build verification, tsc check, or linting fails.
- A **CRITICAL** or **HIGH** severity QA finding remains unresolved.
- Verification commands specified in the contract are not run.
- Code edits violate declared file boundaries.

---

## 5. Memory Rule
Capture durable facts in the SQLite memory-bank only:
- `decision`: Choices and technical justifications.
- `preference`: Developer/user preferences.
- `pattern`: Reusable code structures or failure modes.
- `constraint`: Fixed environment limitations.
- `knowledge`: Stable project facts.
