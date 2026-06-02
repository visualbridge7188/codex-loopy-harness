# Codex Loopy Harness Usage

This document explains how to use the harness in daily work.

## When To Use It

Use the harness for any task where quality depends on more than one answer:

- Research, planning, and writing that will become a deliverable.
- Code or document changes that need verification.
- Presentation, PDF, spreadsheet, or web artifact production.
- Repeated workflows that should improve over time.

For tiny one-off questions, answer directly. Do not force the full loop when there is no meaningful verification surface.

## Operating Loop

Use this loop for non-trivial work:

```text
intent -> contract -> capability scan -> plan -> execute -> verify -> improve -> record memory
```

## Step 1: Intent

Restate the user's request in operational terms.

Capture:

- Desired output.
- Audience.
- Input sources.
- Format requirements.
- Deadline or quality bar.
- What must be verified before completion.

## Step 2: Contract

Before implementation, define:

- Acceptance criteria.
- QA scenarios.
- File boundaries.
- Required commands or checks.
- Evidence record path.

If the task is ambiguous, ask one targeted question before editing.

## Step 3: Capability Scan

Check existing capabilities before creating a new tool.

Use this priority:

1. Adopt installed skills/plugins.
2. Install approved skills/plugins and smoke-test them.
3. Adapt Hugh reference methods from `docs/harness/capability-registry.md`.
4. Create new local tools only after the first three fail.

Record chosen skills/plugins in the evidence file.

## Step 4: Plan

For multi-step work, write or reference a short plan.

The plan should say:

- Which files will change.
- Which capability owns each phase.
- Which checks prove success.
- Which findings block completion.

## Step 5: Execute

Keep execution small and bounded.

Rules:

- Respect file boundaries.
- Do not add speculative features.
- Prefer filesystem truth over agent status prose.
- When using subagents, give each one a disjoint write scope.

## Step 6: Verify

Run the checks named in the contract.

Examples:

- `python3 -m json.tool <file>` for JSON.
- `rg` checks for required sections.
- Browser screenshot or Playwright check for HTML artifacts.
- PDF export and page count check for PDF deliverables.
- Security review for security-sensitive content or code.

Completion is blocked by high/critical findings, missing evidence, failed checks, or missing required verification.

## Step 7: Improve

If verification finds a problem:

1. Classify the finding.
2. Route it to the responsible role or capability.
3. Fix the smallest sufficient cause.
4. Re-run targeted checks.
5. Re-run full QA for high/critical changes.

Stop after 3 failed cycles and escalate.

## Step 8: Record Memory

Capture durable facts only.

Record decisions, preferences, patterns, constraints, and stable knowledge in the shape described in `docs/harness/memory.md`.

Do not store secrets, credentials, personal data, or raw private content without explicit consent.

## Daily Operating Checklist

For every meaningful task:

- [ ] Contract exists.
- [ ] Capability scan happened.
- [ ] File boundaries are clear.
- [ ] Verification commands are named.
- [ ] Evidence record is created or updated.
- [ ] High/critical findings are resolved.
- [ ] Useful memory facts are captured.

