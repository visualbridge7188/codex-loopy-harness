# 🔄 Codex Loopy Harness

> **Verification-first, self-improving multi-agent workspace template**
> "Tools are weaker than structure. Structure is weaker than verification structure."

---

## What is this?

A reusable workspace that turns your AI coding assistant into a structured **"Work OS"** with:

- **15 specialist agents** with strict file-boundary enforcement
- **10 automated hooks** for instant policy validation
- **9 skills** for reusable workflow automation
- **8-phase SDLC pipeline** with closed-loop QA

## Quick Start

### 1. Copy into your project
```bash
cp -r codex-loopy-harness/* /your-project/
```

### 2. Initialize
```
/init-project
```
This profiles your codebase and bootstraps `CLAUDE.md` settings.

### 3. Start building with the hybrid workflow

**Phase 1 — Planning** (Session 1):
```
/office-hour      → Discuss your idea
/brainstorming    → Expand and explore
/grill-with-docs  → Stress-test the spec
/writing-plans    → Create the plan document
```

**Phase 2 — Development** (Session 2):
```
@plan-document
/writing-plans              → Break plan into tasks
/subagent-driven-development → Parallel implementation
/qa-cycle                   → Auto QA (up to 5 rounds)
```

**Phase 3 — Maintenance** (Session 3):
```
/improve-codebase-architecture → Refactor & optimize
```

---

## Directory Structure

```
├── AGENTS.md              # Core rules (3-layer, 8-phase, fail-closed)
├── CLAUDE.md              # Claude Code configuration
├── agents/                # 15 specialist agent definitions
├── skills/                # 9 automation skills
├── commands/              # 6 slash commands
├── hooks/                 # 10 automated policy gates
├── rules/                 # Coding standards (frontend/backend/QA)
├── scripts/               # Utility scripts (sync, apply)
└── docs/
    ├── harness/           # Full documentation
    │   ├── introduction.md          # System overview & operations
    │   ├── workflow-comparison.md   # Hybrid workflow recommendation
    │   ├── principles.md            # Design principles
    │   ├── usage.md                 # Usage guide
    │   ├── gates.md                 # Hook gates reference
    │   └── memory.md                # Memory system docs
    ├── superpowers/       # Plans & specs (project-specific)
    └── verification/      # QA evidence JSON
```

---

## Architecture

### 3-Layer System

| Layer | Role | Model |
|-------|------|-------|
| **Manager-Orchestrator** | Plans, delegates, verifies | Opus |
| **Specialist Agents** | Frontend, Backend, QA, Security, etc. | Sonnet |
| **Automated Hooks** | Instant policy enforcement | Shell scripts |

### 8-Phase Pipeline

```
P1: Plan → P2: Architect → P3: Database → P4: Parallel Implement
P5: Test Suite → P6: Review → P7: Closed-Loop QA → P8: Ship
```

### Fail-Closed Rules

Work is blocked if:
- ❌ Acceptance criteria missing for non-trivial tasks
- ❌ Build/tsc/lint fails
- ❌ CRITICAL/HIGH severity QA issues unresolved
- ❌ Verification commands not run
- ❌ File boundary violations detected

---

## Agents (15)

| Agent | Scope |
|-------|-------|
| `manager-orchestrator` | Overall coordination, never writes code |
| `architect-designer` | File structure, dependencies |
| `product-specifier` | PRDs, specs, user journeys |
| `frontend-specialist` | `src/components/**`, `src/pages/**` |
| `backend-specialist` | `src/api/**`, `src/lib/server/**` |
| `supabase-specialist` | `supabase/migrations/**` |
| `test-writer` | `test/**`, `__tests__/**` |
| `code-reviewer` | Static analysis, patterns |
| `web-qa-tester` | E2E testing, browser scenarios |
| `security-specialist` | Vulnerability scanning |
| `devops-specialist` | CI/CD, deployment |
| `bug-fixer` | Debugging, hotfixes |
| `documentation-specialist` | Docs, README |
| `performance-optimizer` | Profiling, optimization |
| `telegram-notifier` | Notifications via Telegram |

## Skills (8)

| Skill | Purpose |
|-------|---------|
| `init-project` | Profile codebase & bootstrap settings |
| `team` | Run full 8-phase SDLC pipeline |
| `qa-cycle` | Auto multi-round QA testing (up to 5 rounds) |
| `qa-scenario-gen` | Generate QA test plans & coverage matrices |
| `self-improve` | Harness self-improvement cycle |
| `discover-skills` | Search for external skills |
| `harness-report` | Generate harness status report |
| `loopy-era-eval` | Loopy Era evaluation |

## Hooks (10)

| Hook | Action |
|------|--------|
| `auto-validate.sh` | Auto-validate on change |
| `agent-permission-check.sh` | Enforce agent file boundaries |
| `scaffold-violation-check.sh` | Detect structure violations |
| `no-localstorage.sh` | Block localStorage usage |
| `sql-injection-check.sh` | Detect SQL injection patterns |
| `require-telegram-notify.sh` | Require Telegram notification |
| `require-isPWA-check.sh` | Require PWA check |
| `pre-push-qa.sh` | QA gate before push |
| `context-enrichment.sh` | Auto context enrichment |
| `session-cleanup.sh` | Cleanup on session end |

---

## Customization

### Adding project-specific agents
Create a new `.md` file in `agents/` following the existing format:
```markdown
# Agent Name
## Role
## Boundaries
- Allowed: `path/**`
- Blocked: `path/**`
```

### Adding project-specific hooks
Create a new `.sh` file in `hooks/`. Hooks must exit with code `2` to block.

### Removing unnecessary components
- Don't use Supabase? Remove `agents/supabase-specialist.md` and skip P3.
- Don't use Telegram? Remove `agents/telegram-notifier.md` and `hooks/require-telegram-notify.sh`.
- Only need QA? Just use `/qa-cycle` skill standalone.

---

## Documentation

- [System Introduction & Operations](docs/harness/introduction.md)
- [Workflow Comparison & Hybrid Recommendation](docs/harness/workflow-comparison.md)
- [Design Principles](docs/harness/principles.md)
- [Usage Guide](docs/harness/usage.md)
- [Hook Gates Reference](docs/harness/gates.md)
- [Memory System](docs/harness/memory.md)

---

## License

MIT — Use freely. Attribution appreciated but not required.