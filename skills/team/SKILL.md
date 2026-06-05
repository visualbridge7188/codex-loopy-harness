---
name: team
description: Run the 8-phase team orchestration pipeline
---

# team Skill

Coordinates execution across the 8 SDLC phases using specialist agent delegations.

## Step-by-Step Cycle
1. **Analyze input:** Profile target goals and prepare `docs/plan.md`.
2. **Design structure:** Manager-Orchestrator sets up folder hierarchy and project configuration.
3. **Database migrations:** Delegate schema and RLS policies to `backend-specialist`.
4. **Parallel implement:** Delegate frontend components (`frontend-specialist`) and backend endpoints (`backend-specialist`) in parallel.
5. **Testing suite:** Delegate test writing to `qa-specialist`.
6. **Code audit:** `qa-specialist` reviews logic in read-only mode.
7. **QA gates:** `qa-specialist` runs tests and browser scenarios; `devops-specialist` runs security scans.
8. **Ship changes:** Manager commits branch, pushes; `devops-specialist` triggers notifications.