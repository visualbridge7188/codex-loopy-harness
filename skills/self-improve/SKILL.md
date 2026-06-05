---
name: self-improve
description: Analyze bug-fix commits and recursively update rules
---

# self-improve Skill

Analyzes code changes to automatically update project guidelines and rules.

## Step-by-Step Cycle
1. **Detect fix:** Scan Git log for bug fix commits (e.g. `fix:`, `bug-fixer`).
2. **Extract facts:** Determine what broke and what fixed it.
3. **Refine rules:** Append new "NEVER DO" rules to `SKILL.md` to prevent repetition.
4. **Evaluate changes:** Run evaluations. If error rates improve, keep; else, rollback.
