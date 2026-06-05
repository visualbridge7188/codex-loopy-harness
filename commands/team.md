---
description: Trigger the 8-phase multi-agent development orchestration
---

# /team

Executes the team orchestration workflow on the target project, running sequentially from analysis through deploy.

## Usage

```bash
/team "Create a Fullstack Express & React Todo App"
```

## Workflow Phases
- **Phase 0 & 1:** Analysis & Plan Scaffolding (`docs/plan.md`)
- **Phase 2:** Architecture Setup (`tsconfig.json`, `package.json`)
- **Phase 3:** Postgres schema & RLS rules (`supabase/migrations/*.sql`)
- **Phase 4:** Parallel React component and backend endpoint development
- **Phase 5 & 6:** Test writing and Read-only Code Review audits
- **Phase 7 & 8:** Closed-loop E2E browser verification and Git deploy
