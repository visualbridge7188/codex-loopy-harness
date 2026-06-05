---
name: qa-cycle
description: Run the automated multi-round QA testing cycle with scenario generation
---

# qa-cycle Skill

Coordinates scenario generation, test execution, and closed-loop bug fixing.

## Step-by-Step Cycle

### Phase A: Scenario Generation
1. **Analyze spec:** Read specifications, PRD documents, or `docs/plan.md`.
2. **Draft test cases:** Define actions, click points, and visual layouts.
3. **Map CRUD matrix:** Enforce verification criteria for Create, Read, Update, Delete queries.
4. **Save plan:** Write criteria to `docs/qa-test-plan.md` for reference during QA phase.

### Phase B: Test Execution & Fix Loop
5. **Run tests:** Trigger CDP browser scenarios, unit tests, and security scans.
6. **Collect findings:** Parse test outputs and classify severities.
7. **Route errors:** Map high/critical errors to responsible specialists.
8. **Compile validation:** Verify fixes compile successfully.
9. **Re-run tests:** Run up to 5 QA rounds. Escalate to user if unresolved.