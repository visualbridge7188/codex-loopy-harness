# Harness Memory

Memory is for durable facts that should change future behavior.

## Fact Categories

| Category | Use |
| --- | --- |
| `decision` | A choice and its reason. |
| `preference` | A stable user preference. |
| `pattern` | A repeated workflow or failure mode. |
| `constraint` | A rule that limits implementation. |
| `knowledge` | A stable project fact. |

## Scope

| Scope | Meaning |
| --- | --- |
| `project` | Applies only to this workspace. |
| `global` | Applies across the user's work OS. |

## Relation Handling

| Relation | Action |
| --- | --- |
| `DUPLICATE` | Merge facts and increment confidence/count. |
| `CONTRADICTION` | Keep revision history and prefer latest validated fact. |
| `EVOLUTION` | Update fact with reason. |
| `INDEPENDENT` | Keep both facts. |

## Capture Template

```json
{
  "fact": "",
  "category": "decision",
  "scope": "project",
  "source": "",
  "reason": "",
  "date": "2026-06-02"
}
```

## Capture Rules

Capture facts when:

- The user approves a design direction.
- A recurring failure is found.
- A skill/plugin is adopted or rejected.
- A project constraint is discovered.
- A preference clearly affects future work.

Do not capture:

- Temporary status updates.
- One-off command output.
- Unverified assumptions.
- Private or sensitive data unless explicitly needed and safe.
