# Codex Loopy Harness

This workspace uses a Hugh-inspired, verification-first Codex harness.

## Core Rule

Tools are weaker than structure. Structure is weaker than verification structure.

Do not treat an agent's claim of completion as proof. Use files, logs, tests, screenshots, and evidence records as the source of truth.

## Default Workflow

For non-trivial tasks:

1. Define the contract before implementation.
2. Check existing verified skills and plugins before inventing new tools.
3. Set file boundaries before editing.
4. Implement the smallest useful change.
5. Run targeted verification.
6. Run full QA when user-facing behavior, security, or shared contracts changed.
7. Record evidence.
8. Capture reusable decisions, constraints, preferences, and patterns.

## Fail-Closed Rules

Completion is blocked when:

- Acceptance criteria are missing for a non-trivial task.
- Required verification was not run.
- Build, typecheck, test, or required browser verification fails.
- A high or critical finding remains unresolved.
- Evidence is required but missing.
- File boundaries are violated.
- Security-sensitive changes lack security review.

## Fail-Open Rules

These activities should be attempted when useful but must not block delivery:

- Memory extraction.
- Dashboard/report generation.
- Sync/export.
- Trend capture.
- Non-critical insights.

## Capability-First Rule

Before creating a new local tool, check:

- Installed Codex skills.
- Enabled plugins.
- OpenAI curated skills.
- Project-local scripts.
- Hugh reference capabilities listed in `docs/harness/capability-registry.md`.

Classify each candidate as `adopt`, `install`, `adapt`, or `skip`.

## Memory Rule

Capture durable facts only:

- `decision`: what was chosen and why.
- `preference`: how the user likes work to be done.
- `pattern`: repeated successful or failed workflow.
- `constraint`: rule that limits implementation.
- `knowledge`: stable project fact.

Keep project-scoped facts separate from global facts.
