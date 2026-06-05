---
name: qa-cycle
description: Run the automated multi-round QA testing cycle
---

# qa-cycle Skill

Coordinates execution of visual, E2E, and security tests to identify and fix bugs.

## Step-by-Step Cycle
1. **Run tests:** Trigger CDP browser scenarios, unit tests, and security scans.
2. **Collect findings:** Parse test outputs and classify severities.
3. **Route errors:** Map high/critical errors to responsible specialists.
4. **Compile validation:** Verify fixes compile successfully.
5. **Re-run tests:** Run up to 5 QA rounds. Escalate to user if unresolved.
