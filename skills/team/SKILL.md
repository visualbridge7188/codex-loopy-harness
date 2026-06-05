---
name: team
description: Run the 8-phase team orchestration pipeline
---

# team Skill

Coordinates execution across the 8 SDLC phases using specialist agent delegations.

## Step-by-Step Cycle
1. **Analyze input:** Profile target goals and prepare `docs/plan.md`.
2. **Design structure:** Delegate folder setup to `architect-designer`.
3. **Database migrations:** Delegate schema and RLS policies to `supabase-specialist`.
4. **Parallel implement:** Delegate frontend components and backend endpoints in parallel.
5. **Testing suite:** Delegate test writing to `test-writer`.
6. **Code audit:** Review logic with `code-reviewer` in read-only mode.
7. **QA gates:** Run tests and browser scenarios via `web-qa-tester`.
8. **Ship changes:** Commit branch, push, and alert via `telegram-notifier`.
