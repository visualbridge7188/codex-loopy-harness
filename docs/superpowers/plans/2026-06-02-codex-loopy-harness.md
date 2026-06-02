# Codex Loopy Harness Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build the v0.1 Codex Loopy Harness document set, capability registry, and evidence template based on the approved Hugh-inspired design.

**Architecture:** This is a document-first harness. `AGENTS.md` defines runtime behavior, `docs/harness/*` defines principles and gates, and `docs/verification/*` records skills/plugins and evidence contracts. No standalone orchestrator or custom runtime script is introduced in v0.1.

**Tech Stack:** Markdown, JSON, Codex skills/plugins, local shell validation with `rg`, `jq` or Python JSON parsing.

---

## File Structure

- Create `AGENTS.md`: project-level operating rules for Codex Loopy Harness.
- Create `docs/harness/principles.md`: Hugh-derived principles adapted to this workspace.
- Create `docs/harness/capability-registry.md`: Hugh skill/plugin catalog mapped to Codex capabilities.
- Create `docs/harness/gates.md`: fail-closed and fail-open verification gate rules.
- Create `docs/harness/memory.md`: project/global memory fact model and capture rules.
- Create `docs/verification/skills-registry.md`: installed, available, recommended, deferred capability list.
- Create `docs/verification/evidence-template.qa-evidence.json`: reusable QA evidence schema.
- Modify `docs/superpowers/specs/2026-06-02-codex-loopy-harness-design.md`: no changes expected unless implementation reveals a contradiction.

## Task 1: Create Project Operating Rules

**Files:**
- Create: `AGENTS.md`

- [ ] **Step 1: Write `AGENTS.md`**

Create the file with this content:

```markdown
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
```

- [ ] **Step 2: Verify file exists and contains core rule**

Run:

```bash
test -f AGENTS.md
rg -n "Tools are weaker than structure|Fail-Closed|Capability-First" AGENTS.md
```

Expected: all three patterns are found.

## Task 2: Create Harness Principles

**Files:**
- Create: `docs/harness/principles.md`

- [ ] **Step 1: Write principles document**

Create the file with this content:

```markdown
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
```

- [ ] **Step 2: Verify principle coverage**

Run:

```bash
rg -n "Filesystem Is Truth|Contract-First QA|Verified Capabilities First|Hard Gates" docs/harness/principles.md
```

Expected: all four headings are found.

## Task 3: Create Capability Registry

**Files:**
- Create: `docs/harness/capability-registry.md`

- [ ] **Step 1: Write registry document**

Create the file with this content:

```markdown
# Capability Registry

This registry maps Hugh Kim's published skill/plugin catalog into this Codex workspace.

## Status Values

| Status | Meaning |
| --- | --- |
| `adopt` | Use now. Already available or directly supported. |
| `install` | Install after approval, then smoke-test. |
| `adapt` | Copy the method, not necessarily the tool. |
| `defer` | Keep as reference until a project needs it. |
| `skip` | Not relevant to this harness. |

## Hugh Skills To Codex Mapping

| Hugh capability | Category | Codex equivalent | Status |
| --- | --- | --- | --- |
| `qa-scenario-gen` | QA | Superpowers brainstorming/spec plus local QA scenario template | `adapt` |
| `qa-cycle` | QA | Superpowers verification plus evidence schema | `adopt` |
| `qa-functional` | QA | Browser plugin and Playwright candidate | `install` |
| `webapp-testing` | QA | Browser plugin and Playwright candidate | `install` |
| `agent-browser` | QA | Browser plugin | `adopt` |
| `security-best-practices` | Security | OpenAI curated skill | `install` |
| `vulnerability-scanner` | Security | Security checklist, future reputable scanner | `adapt` |
| `pentest-checklist` | Security | Security threat model skill plus local checklist | `install` |
| `supabase-postgres` | Platform | Supabase-specific skill/method | `defer` |
| `supabase-manager` | Platform | Supabase-specific skill/method | `defer` |
| `mcp-project-mgr` | Dev tools | Capability registry and project docs | `adapt` |
| `prompt-engineering` | Dev tools | Prompt/process guidance | `adapt` |
| `skill-creation` | Dev tools | Existing skill creator skills | `adopt` |
| `skill-judge` | Dev tools | Skill smoke-test registry | `adapt` |
| `plugin-forge` | Dev tools | Existing plugin creator skill | `adopt` |
| `web-artifacts` | Frontend | Browser plus artifact generation patterns | `adapt` |
| `frontend-design` | Frontend | Existing frontend/design guidance and Browser QA | `adopt` |
| `parallel-agents` | Memory/workflow | Superpowers parallel-agent methods and Codex parallel tools | `adopt` |
| `context-window` | Memory/workflow | AGENTS context hygiene and memory docs | `adapt` |
| `agent-memory` | Memory | Memory docs plus future memory-bank compatibility check | `adapt` |
| `memory` | Memory | Memory docs plus future memory-bank compatibility check | `adapt` |
| `remember-this` | Memory | Memory fact capture rules | `adapt` |
| `mermaid-diagrams` | Docs | Native Mermaid support in markdown | `adopt` |
| `changelog-gen` | Docs | Future local changelog template | `adapt` |
| `dependency-updater` | Dev tools | Project-specific package manager checks | `defer` |
| `git-commit` | Dev tools | Git workflow or future `yeet` skill | `defer` |
| `init-project` | Dev tools | Harness initialization docs | `adapt` |
| `pdf` | Docs | Installed PDF skill | `adopt` |
| `pptx` | Docs | Presentations plugin | `adopt` |
| `xlsx` | Docs | Spreadsheets plugin | `adopt` |
| `auto-idea` | Product | Product discovery workflow | `defer` |
| `auto-mvp` | Product | MVP planning workflow | `defer` |
| `indie-hacker` | Product | Product/business workflow | `defer` |
| `project-orch` | Workflow | This harness | `adapt` |

## Hugh Plugins To Codex Mapping

| Hugh plugin/source | Codex equivalent | Status |
| --- | --- | --- |
| `superpowers` | Installed Superpowers plugin skills | `adopt` |
| `superpowers-chrome` | Browser plugin or Chrome plugin when user profile is needed | `adopt` |
| `superpowers-dev` | Superpowers development skills | `adopt` |
| `superpowers-lab` | Experimental methods only | `defer` |
| `episodic-memory` | Memory docs and future compatibility check | `adapt` |
| `claude-mem` | Future memory capability search | `defer` |
| `memory-bank` | Hugh reference repo, possible install/adaptation target | `adapt` |
| `memory-bank-dev` | Development reference only | `defer` |
| `pr-review-toolkit` | GitHub plugin skills | `adopt` |
| `feature-dev` | Superpowers planning/execution skills | `adopt` |
| `agent-sdk-dev` | Defer until agent SDK work exists | `defer` |
| `commit-commands` | Git workflow or future `yeet` skill | `defer` |
| `code-review` | Review skill/plugin workflows | `adopt` |
| `security-guidance` | Security curated skills | `install` |
| `frontend-design` | Frontend/design guidance | `adopt` |
| `vs-design-diverge` | Design exploration method | `defer` |
| `insights-ui` | Observability reference repo | `defer` |
| `claude-dashboard` | Observability reference | `defer` |
| `oh-my-claudecode` | Harness inspiration only | `defer` |

## v0.1 Install Candidates

Install only after user approval:

1. `playwright`
2. `screenshot`
3. `security-best-practices`
4. `security-threat-model`
5. `gh-fix-ci`
6. `gh-address-comments`
7. `openai-docs`

Evaluate later:

1. `memory-bank`
2. `claude-mem`
3. reputable `vulnerability-scanner`
4. reputable `pentest-checklist`
```

- [ ] **Step 2: Verify Hugh mapping exists**

Run:

```bash
rg -n "qa-scenario-gen|memory-bank|security-best-practices|superpowers|insights-ui" docs/harness/capability-registry.md
```

Expected: all five patterns are found.

## Task 4: Create Gates Document

**Files:**
- Create: `docs/harness/gates.md`

- [ ] **Step 1: Write gates document**

Create the file with this content:

```markdown
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
```

- [ ] **Step 2: Verify gate taxonomy**

Run:

```bash
rg -n "contract-missing|verification-missing|finding-critical|Fail-Open" docs/harness/gates.md
```

Expected: all four patterns are found.

## Task 5: Create Memory Document

**Files:**
- Create: `docs/harness/memory.md`

- [ ] **Step 1: Write memory document**

Create the file with this content:

```markdown
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
```

- [ ] **Step 2: Verify memory model**

Run:

```bash
rg -n "DUPLICATE|CONTRADICTION|EVOLUTION|Capture Template" docs/harness/memory.md
```

Expected: all four patterns are found.

## Task 6: Create Verification Registry And Evidence Template

**Files:**
- Create: `docs/verification/skills-registry.md`
- Create: `docs/verification/evidence-template.qa-evidence.json`

- [ ] **Step 1: Write skills registry**

Create `docs/verification/skills-registry.md` with this content:

```markdown
# Skills And Plugins Registry

Date: 2026-06-02

## Adopted In Current Codex Session

| Capability | Type | Use |
| --- | --- | --- |
| Superpowers | plugin | Brainstorming, planning, verification, TDD, review workflows. |
| Browser | plugin | Local browser verification and screenshots. |
| GitHub | plugin | PR, issue, CI, review, and publishing workflows. |
| Google Drive | plugin | Drive, Docs, Sheets, Slides workflows. |
| Notion | plugin | Specs, research, and implementation planning workflows. |
| Slack | plugin | Summaries, replies, and outbound communication. |
| Documents | plugin | Word/document artifacts. |
| Presentations | plugin | Presentation artifacts. |
| Spreadsheets | plugin | Spreadsheet artifacts. |
| pdf | skill | PDF reading, extraction, creation, and manipulation. |
| humanizer | skill | Natural text editing and voice calibration. |
| watch | skill | Video understanding. |
| wp-plugin-tdd | skill | WordPress plugin TDD. |
| understand suite | skills | Codebase graph, diff, onboarding, domain, and explanation workflows. |

## Install Candidates

| Skill | Source | Reason | Status |
| --- | --- | --- | --- |
| `playwright` | OpenAI curated | Browser/e2e verification | pending approval |
| `screenshot` | OpenAI curated | Visual evidence capture | pending approval |
| `security-best-practices` | OpenAI curated | Security guardrails | pending approval |
| `security-threat-model` | OpenAI curated | Threat modeling | pending approval |
| `gh-fix-ci` | OpenAI curated | GitHub CI repair | pending approval |
| `gh-address-comments` | OpenAI curated | PR review feedback loop | pending approval |
| `openai-docs` | OpenAI curated | Official OpenAI docs | pending approval |

## Hugh Reference Candidates

| Capability | Planned handling |
| --- | --- |
| `memory-bank` | Adapt method first, evaluate installation compatibility later. |
| `claude-mem` | Search for Codex-compatible equivalent later. |
| `vulnerability-scanner` | Prefer reputable skill/plugin if found; otherwise local checklist. |
| `pentest-checklist` | Pair with threat-model skill and local checklist. |
| `insights-ui` | Defer dashboard until evidence loop is working. |

## Smoke Test Record

No new skills have been installed yet.
```

- [ ] **Step 2: Write evidence template**

Create `docs/verification/evidence-template.qa-evidence.json` with this content:

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

- [ ] **Step 3: Validate JSON**

Run:

```bash
python3 -m json.tool docs/verification/evidence-template.qa-evidence.json >/dev/null
```

Expected: exit code 0.

- [ ] **Step 4: Verify registry references Hugh candidates**

Run:

```bash
rg -n "memory-bank|playwright|security-best-practices|Smoke Test" docs/verification/skills-registry.md
```

Expected: all four patterns are found.

## Task 7: Final Verification

**Files:**
- Read: all files created above.

- [ ] **Step 1: Verify required files exist**

Run:

```bash
test -f AGENTS.md
test -f docs/harness/principles.md
test -f docs/harness/capability-registry.md
test -f docs/harness/gates.md
test -f docs/harness/memory.md
test -f docs/verification/skills-registry.md
test -f docs/verification/evidence-template.qa-evidence.json
```

Expected: exit code 0.

- [ ] **Step 2: Scan for unfinished markers**

Run:

```bash
rg -n "TB[D]|TO[D]O|FIXM[E]|implement late[r]" AGENTS.md docs/harness docs/verification
```

Expected: no matches.

- [ ] **Step 3: Verify design linkage**

Run:

```bash
rg -n "Hugh|Filesystem|Contract|evidence|memory-bank|security-best-practices" AGENTS.md docs/harness docs/verification docs/superpowers/specs/2026-06-02-codex-loopy-harness-design.md
```

Expected: matching lines across the design, harness docs, and verification docs.
