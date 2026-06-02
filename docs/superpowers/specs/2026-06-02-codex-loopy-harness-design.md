# Codex Loopy Harness v0.1 Design

Date: 2026-06-02
Workspace: `/Users/parkjuncheol/Local Sites/AI Agent/hugh.kim`

## 1. Source Methodology

This design adapts Hugh Kim's autonomous project builder pattern into a Codex-centered personal work OS.

Primary references:

- Hugh Kim homepage: autonomous project builder, 13 agents, 50+ skills, 21 plugins, contract-first QA, filesystem as truth.
- `jung-wan-kim/manager-orchestrator`: manager-led 8-phase orchestration, specialist routing, QA feedback loop.
- `jung-wan-kim/memory-bank`: conversation archive, fact extraction, ontology, project/global scope isolation, contradiction/evolution handling.
- `jung-wan-kim/usage-gate`: hook-based operational gate with fail-open model switching.
- `jung-wan-kim/cc-sync-template`: user-scope harness sync and restore pattern.
- `jung-wan-kim/insights-ui`: dashboard/reporting layer for observability.
- `jung-wan-kim/claude-code-infrastructure-showcase`: skill auto-activation, hook taxonomy, agents, commands, progressive disclosure.

The important lesson is not "install many tools." The lesson is:

> Tools are weaker than structure. Structure is weaker than verification structure.

## 2. Goals

Codex Loopy Harness v0.1 should create a small, explicit operating loop for high-quality work:

1. Convert user intent into a written contract before implementation.
2. Route work through existing verified skills, plugins, and specialist roles where possible.
3. Treat the filesystem, logs, tests, and evidence files as the source of truth.
4. Block completion when critical verification is missing or failing.
5. Record decisions, constraints, preferences, and recurring patterns for future sessions.
6. Keep v0.1 small enough to use immediately inside Codex.

## 3. Non-Goals

v0.1 will not build a full standalone multi-agent platform.

It will not clone Hugh's entire stack, recreate Claude Code hooks verbatim, or invent new tools before checking existing skills and plugins.

It will not make memory extraction, dashboards, or trend capture block normal delivery.

## 4. Architecture

Codex Loopy Harness has five layers.

### Layer 1: Manager

The manager interprets the request, defines the contract, decides which skills/plugins to use, sets file boundaries, and judges verification evidence.

In Codex, this is mainly enforced through `AGENTS.md`, specs, checklists, and future commands.

### Layer 2: Verified Capability Registry

Before creating new tools, the harness checks installed and installable capabilities:

- Installed Codex skills.
- OpenAI curated skills.
- Enabled plugins/connectors.
- Project-local scripts and tests.
- Existing external repositories only when they are directly relevant.

Each capability gets one of four statuses:

| Status | Meaning |
| --- | --- |
| `adopt` | Use as-is. Already verified enough for v0.1. |
| `install` | Install after user approval and verify with a smoke test. |
| `adapt` | Use the method, but rewrite for the local stack. |
| `skip` | Useful-looking but not needed for the current harness. |

### Layer 3: Specialist Roles

Specialists are roles, not necessarily separate processes in v0.1:

- `planner`: turns intent into contract and acceptance criteria.
- `researcher`: checks references and current docs.
- `implementer`: edits files inside agreed boundaries.
- `qa-reviewer`: runs tests, browser checks, accessibility checks, and evidence capture.
- `security-reviewer`: checks security-sensitive changes.
- `memory-curator`: extracts reusable facts after meaningful decisions.

### Layer 4: Verification Gates

Verification gates decide whether the work can finish.

Fail-closed gates block completion:

- Acceptance contract missing.
- Required tests not run.
- Test/build/typecheck failure.
- High or critical QA finding unresolved.
- Evidence file missing when a task requires verification.
- File boundary violation.
- Security finding marked high or critical.

Fail-open activities do not block delivery:

- Memory extraction.
- Dashboard/report rendering.
- Sync/export.
- Trend harvesting.
- Non-critical insight generation.

### Layer 5: Memory and Self-Improvement

Memory records reusable facts, not raw noise.

Fact categories:

- `decision`: "We chose X because Y."
- `preference`: "The user prefers Korean responses for planning."
- `pattern`: "For frontend work, screenshot after implementation."
- `constraint`: "Do not complete without evidence."
- `knowledge`: "This repo uses a specific stack or command."

Scopes:

- `project`: applies only to this workspace.
- `global`: applies across the user's work OS.

Relation handling:

- `DUPLICATE`: merge.
- `CONTRADICTION`: keep revision history and prefer the latest validated decision.
- `EVOLUTION`: update with reason.
- `INDEPENDENT`: keep both.

## 5. Phase Pipeline

v0.1 uses a compact 7-phase pipeline.

| Phase | Name | Output | Gate |
| --- | --- | --- | --- |
| P0 | Capability scan | installed/installable skill and plugin list | none |
| P1 | Contract | acceptance criteria, QA scenarios, file boundaries | fail-closed |
| P2 | Plan | steps, specialist roles, verification commands | fail-closed |
| P3 | Execute | implementation or document changes | fail-closed if boundaries break |
| P4 | Micro-verify | targeted tests/checks | fail-closed for required checks |
| P5 | Full QA | review, browser/e2e/security as applicable | fail-closed for high/critical findings |
| P6 | Learn | memory facts, changelog, next improvements | fail-open |

## 6. Evidence Schema

Each non-trivial task should create or update a small evidence record.

Target path:

```text
docs/verification/<YYYY-MM-DD>-<task-slug>.qa-evidence.json
```

Schema:

```json
{
  "task": {
    "title": "",
    "date": "2026-06-02",
    "scope": ""
  },
  "contract": {
    "acceptanceCriteria": [],
    "qaScenarios": [],
    "fileBoundaries": []
  },
  "capabilities": {
    "skillsUsed": [],
    "pluginsUsed": [],
    "toolsInstalled": [],
    "toolsSkipped": []
  },
  "verification": {
    "commandsRun": [],
    "testResults": [],
    "browserChecks": [],
    "securityChecks": [],
    "findings": []
  },
  "loop": {
    "cycle": 1,
    "maxCycles": 3,
    "mustFixSeverities": ["CRITICAL", "HIGH"],
    "status": "pass"
  },
  "memory": {
    "decisions": [],
    "preferences": [],
    "patterns": [],
    "constraints": []
  }
}
```

## 7. Finding Routing

Findings are routed back to the right role.

| Category | Route |
| --- | --- |
| `UI` | frontend/design specialist |
| `ACCESSIBILITY` | frontend/design specialist |
| `API` | backend specialist |
| `DB` | database specialist |
| `SECURITY` | security specialist |
| `PERFORMANCE` | performance specialist |
| `TYPE` | bug fixer |
| `BUILD` | bug fixer |
| `DOCS` | documentation specialist |
| `MEMORY` | memory curator |

Severity rules:

| Severity | Behavior |
| --- | --- |
| `CRITICAL` | must fix, blocks completion, full re-QA |
| `HIGH` | must fix, blocks completion, full re-QA |
| `MEDIUM` | one fix attempt, partial re-QA, may proceed if documented |
| `LOW` | document only unless cheap to fix |

## 8. Verified Skill and Plugin Adoption

This harness should actively install and use verified capabilities instead of inventing local versions.

### Hugh Reference Capability Catalog

The Hugh page lists a concrete capability catalog. v0.1 should treat that catalog as the reference map, then translate it into Codex-available equivalents.

Reference skills from Hugh's page:

| Group | Hugh skills | Harness interpretation |
| --- | --- | --- |
| QA | `qa-scenario-gen`, `qa-cycle`, `qa-functional`, `webapp-testing`, `agent-browser` | Contract-first QA, browser verification, evidence capture, repeated QA loop. |
| Security | `security-best-practices`, `vulnerability-scanner`, `pentest-checklist` | Security guardrails, threat checks, must-fix high/critical findings. |
| Dev tools | `mcp-project-mgr`, `prompt-engineering`, `skill-creation`, `skill-judge`, `plugin-forge`, `dependency-updater`, `git-commit`, `init-project` | Capability registry, skill/plugin lifecycle, project initialization, dependency and git hygiene. |
| Memory | `parallel-agents`, `context-window`, `agent-memory`, `memory`, `remember-this` | Context management, project/global memory facts, self-improvement loop. |
| Docs/artifacts | `mermaid-diagrams`, `changelog-gen`, `pdf`, `pptx`, `xlsx`, `pen-import` | Visual architecture, changelog, document/spreadsheet/presentation outputs. |
| Frontend/product | `shadcn`, `web-artifacts`, `frontend-design`, `auto-idea`, `auto-mvp`, `indie-hacker`, `project-orch` | UI implementation, product ideation, MVP workflow, project orchestration. |
| Platform-specific | `supabase-postgres`, `supabase-manager`, `remotion`, `telegram-mini-app`, `hany-mis` | Install or adapt only when the project uses that stack. |

Reference plugins/extensions from Hugh's page:

| Hugh plugin/source | Harness interpretation |
| --- | --- |
| `superpowers`, `superpowers-chrome`, `superpowers-dev`, `superpowers-lab` | Process discipline, browser verification, development workflows, experimental loops. |
| `episodic-memory`, `claude-mem`, `memory-bank`, `memory-bank-dev` | Long-term memory, fact extraction, project-scoped context, recall surface. |
| `pr-review-toolkit`, `code-review`, `security-guidance` | Review and security gates. |
| `feature-dev`, `agent-sdk-dev`, `commit-commands` | Feature implementation and commit workflows. |
| `frontend-design`, `vs-design-diverge`, `ui-ux-pro-max`, `frontend-opus-4.5` | UI/design specialist workflows. |
| `claude-dashboard`, `insights-ui`, `oh-my-claudecode` | Observability, usage reporting, harness status surface. |

The registry must not blindly install every item. It should classify each reference into:

1. Already available in Codex.
2. Installable as a Codex skill/plugin.
3. Available only as a method to adapt.
4. Deferred because the current project does not need it.

### Hugh-to-Codex Translation Matrix

| Hugh capability | Codex v0.1 equivalent | Action |
| --- | --- | --- |
| `qa-scenario-gen` | Superpowers brainstorming/spec + local QA scenario template | `adapt` |
| `qa-cycle` | Superpowers verification-before-completion + evidence schema | `adopt/adapt` |
| `qa-functional` | Browser plugin + Playwright skill candidate | `install` Playwright if approved |
| `agent-browser` | Browser plugin | `adopt` |
| `webapp-testing` | Browser plugin + Playwright skill candidate | `install` Playwright if approved |
| `security-best-practices` | OpenAI curated `security-best-practices` | `install` |
| `vulnerability-scanner` | Security review checklist + future scanner skill if found | `adapt` |
| `pentest-checklist` | OpenAI curated `security-threat-model` plus local checklist | `install/adapt` |
| `parallel-agents` | Superpowers parallel/subagent skills, Codex multi-tool parallelism | `adopt` |
| `context-window` | AGENTS context hygiene + memory docs | `adapt` |
| `agent-memory`, `memory`, `remember-this` | memory-bank method + local memory docs; install memory skill only if compatible | `adapt` first |
| `mermaid-diagrams` | Native Mermaid in docs | `adopt` |
| `changelog-gen` | Local command/template, future skill search | `adapt` |
| `git-commit` | Git workflow + future `yeet` skill | `install` only when publishing |
| `init-project` | Harness `init` command/spec | `adapt` |
| `frontend-design` | Installed design guidance + Browser verification | `adopt/adapt` |
| `pdf`, `pptx`, `xlsx` | Installed/available Documents, Presentations, Spreadsheets, PDF skills/plugins | `adopt` |
| `memory-bank` | Reference repo + possible external plugin pattern | `adapt`, then evaluate install |
| `insights-ui` | Reference observability dashboard | `defer` |

### Already Enabled Plugins To Prefer

These are already available in this Codex session and should be used when relevant:

- `Superpowers`: brainstorming, planning, TDD, verification, review workflows.
- `Browser`: local app/browser verification.
- `GitHub`: PR, issue, CI, review, publishing workflows.
- `Google Drive`: Docs, Sheets, Slides work.
- `Notion`: specs, meeting intelligence, research documentation.
- `Slack`: communication, summaries, reply drafting.
- `Documents`, `Presentations`, `Spreadsheets`: artifact creation and verification.

### Already Installed Local Skills To Prefer

These are installed in the user's skill folders and should be part of the capability registry:

- `pdf`
- `humanizer`
- `watch`
- `wp-plugin-tdd`
- `understand`
- `understand-chat`
- `understand-dashboard`
- `understand-diff`
- `understand-domain`
- `understand-explain`
- `understand-knowledge`
- `understand-onboard`

### OpenAI Curated Skill Candidates

The curated skill list currently includes these relevant not-yet-installed candidates:

- `playwright`: browser/e2e verification.
- `playwright-interactive`: interactive browser verification.
- `screenshot`: visual evidence capture.
- `security-best-practices`: security guardrails.
- `security-threat-model`: structured threat modeling.
- `security-ownership-map`: security responsibility mapping.
- `sentry`: error tracking integration.
- `openai-docs`: official OpenAI API/product docs.
- `gh-fix-ci`: GitHub Actions failure diagnosis.
- `gh-address-comments`: PR review feedback handling.
- `yeet`: publish local changes to GitHub.
- `cloudflare-deploy`, `netlify-deploy`, `vercel-deploy`, `render-deploy`: deploy workflows.
- `notion-*`: Notion workflows if plugin-provided skills are not enough.

### v0.1 Recommended Install Set

Install after user approval:

1. `playwright`
2. `screenshot`
3. `security-best-practices`
4. `security-threat-model`
5. `gh-fix-ci`
6. `gh-address-comments`
7. `openai-docs`

Evaluate for install or adaptation after v0.1 smoke tests:

1. `memory-bank` from Hugh's reference repo or an equivalent Codex-compatible memory skill.
2. `claude-mem` or an equivalent episodic memory capability if available and compatible.
3. `frontend-design` alternatives only if current installed frontend guidance is insufficient.
4. `vulnerability-scanner` or `pentest-checklist` equivalents if reputable installable versions are found.

Defer until a project needs them:

- deploy skills.
- Figma skills.
- Sentry.
- Notion duplicates if the installed Notion plugin skills are enough.

### Installation Verification

Every installed skill must pass a small verification loop:

1. Install skill.
2. Confirm it appears under `$CODEX_HOME/skills`.
3. Restart Codex if required.
4. Trigger the skill with a harmless prompt or task.
5. Record the result in `docs/verification/skills-registry.md`.

No skill is considered adopted until it has a recorded smoke test.

## 9. Codex Environment Integration Plan

v0.1 should add these project files:

```text
AGENTS.md
docs/harness/principles.md
docs/harness/capability-registry.md
docs/harness/gates.md
docs/harness/memory.md
docs/verification/skills-registry.md
docs/verification/evidence-template.qa-evidence.json
docs/superpowers/specs/2026-06-02-codex-loopy-harness-design.md
```

Future optional files:

```text
commands/init-harness.md
commands/qa-loop.md
commands/capture-memory.md
scripts/verify-evidence.js
scripts/skill-smoke-test.js
```

## 10. Success Criteria

v0.1 is successful when:

1. A new task can start with a contract, not a vague prompt.
2. The harness checks existing skills/plugins before proposing new tools.
3. Non-trivial completion requires evidence.
4. High/critical findings cannot be ignored.
5. Repeated decisions become explicit memory facts.
6. The user can inspect the current harness state through files.
7. Skill/plugin installation has a registry and smoke-test record.

## 11. Immediate Next Step

After this design is approved:

1. Create the harness documents listed in section 9.
2. Create the evidence template.
3. Create the skills registry with installed/current candidates.
4. Ask user approval before installing the v0.1 recommended skill set.
5. Install approved skills.
6. Smoke-test each installed skill.
7. Record installation evidence.
