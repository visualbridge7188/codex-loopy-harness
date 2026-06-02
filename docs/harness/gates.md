# Verification Gates

## Gate Taxonomy

| Gate type | Meaning |
| --- | --- |
| `block` | Stop completion until fixed. |
| `warn` | Allow progress but record the risk. |
| `suggest` | Recommend a skill, plugin, or check. |
| `observe` | Record telemetry or memory without changing flow. |

## Fail-Closed Gates

These block completion:

| Gate | Trigger | Required response |
| --- | --- | --- |
| `contract-missing` | No acceptance criteria for non-trivial task | Write contract before editing. |
| `verification-missing` | Required command/check not run | Run check or explain why impossible. |
| `test-failed` | Test/build/typecheck fails | Fix and rerun. |
| `evidence-missing` | Evidence required but absent | Create evidence JSON. |
| `finding-critical` | Critical finding remains | Fix and run full QA. |
| `finding-high` | High finding remains | Fix and run full QA. |
| `boundary-violation` | Edit outside declared file boundary | Stop and reconcile scope. |
| `security-review-missing` | Security-sensitive change lacks review | Run security review. |

## Fail-Open Activities

These should not block delivery:

| Activity | Failure response |
| --- | --- |
| Memory extraction | Record skipped memory extraction if relevant. |
| Dashboard rendering | Continue and record dashboard failure. |
| Sync/export | Continue and retry later. |
| Trend capture | Continue. |
| Low-severity insights | Record only. |

## QA Loop

Each QA cycle:

1. Collect findings.
2. Classify severity.
3. Route finding to role.
4. Fix must-fix severities.
5. Rerun targeted verification.
6. Rerun full QA for high/critical changes.
7. Stop after 3 failed cycles and escalate.

## Severity Rules

| Severity | Blocks? | Re-QA |
| --- | --- | --- |
| `CRITICAL` | yes | full |
| `HIGH` | yes | full |
| `MEDIUM` | no after one documented attempt | partial |
| `LOW` | no | none unless cheap |
