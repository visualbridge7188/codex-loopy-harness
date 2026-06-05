---
name: loopy-era-eval
description: Evaluate harness compliance against 36 HARD checks
---

# loopy-era-eval Skill

Validates project configuration and code against 36 hard rules.

## Step-by-Step Cycle
1. **File scans:** Verify required files and structures exist.
2. **Keyword checks:** Search code for prohibited practices (e.g. `localStorage`).
3. **Syntax validation:** Run `bash -n` on all hook and script files.
4. **Compile check:** Assert build passes without errors.
5. **Output ratios:** Record total passed checks in `results.tsv`.
