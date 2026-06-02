# Harness Principles

## 1. Verification Before Completion

The harness optimizes for closed-loop quality, not one-shot output.

Every meaningful result should move through:

```text
intent -> contract -> execution -> verification -> finding routing -> re-verification -> memory
```

## 2. Filesystem Is Truth

Do not rely on status prose as proof.

Preferred evidence:

- Existing files.
- Diffs.
- Test output.
- Browser screenshots.
- QA findings.
- Evidence JSON.

## 3. Contract-First QA

Before implementation, define:

- Acceptance criteria.
- QA scenarios.
- File boundaries.
- Required verification commands.
- Must-fix severity threshold.

## 4. Verified Capabilities First

Use verified skills and plugins before writing new tools.

The order is:

1. Adopt an installed skill/plugin.
2. Install a reputable skill/plugin and smoke-test it.
3. Adapt a proven method from Hugh's catalog or reference repos.
4. Build a new local tool only when the first three options fail.

## 5. Hard Gates, Soft Learning

Quality gates fail closed. Learning systems fail open.

Tests, evidence, security, and high/critical QA findings can block completion.

Memory extraction, dashboards, and trend capture should not block completion.

## 6. Memory With Scope

Do not turn every conversation into memory.

Save facts that change future behavior. Separate project facts from global preferences.
