# Capability Registry

This registry maps Hugh Kim's published skill/plugin catalog into this Codex workspace.

## Status Values

| Status    | Meaning                                           |
| --------- | ------------------------------------------------- |
| `adopt`   | Use now. Already available or directly supported. |
| `install` | Install after approval, then smoke-test.          |
| `adapt`   | Copy the method, not necessarily the tool.        |
| `defer`   | Keep as reference until a project needs it.       |
| `skip`    | Not relevant to this harness.                     |

## Harness Inventory (after 2026-06-05 shrink)

### Agents (5)

| Agent | Role | Absorbed |
|-------|------|----------|
| `manager-orchestrator` | Team lead, architecture, docs, product specs | Architect, Documentation, Product Specifier |
| `frontend-specialist` | Frontend dev + performance | Performance Optimizer |
| `backend-specialist` | Backend dev + DB + bug fixes | Supabase Specialist, Bug Fixer |
| `qa-specialist` | QA + tests + code review | Test Writer, Code Reviewer |
| `devops-specialist` | DevOps + security + notifications | Security Specialist, Telegram Notifier |

### Skills (4)

| Skill | Purpose |
|-------|---------|
| `init-project` | Project codebase profiling and initial setup |
| `team` | 8-Phase SDLC orchestration pipeline |
| `qa-cycle` | QA scenario generation + automated multi-round testing |
| `discover-skills` | External skill/tool search |

### Commands (2)

| Command | Purpose |
|---------|---------|
| `/team` | All development workflows |
| `/init` | Project initialization |

### Removed

- **Agents (10)**: architect-designer, bug-fixer, code-reviewer, documentation-specialist, performance-optimizer, product-specifier, security-specialist, supabase-specialist, telegram-notifier, test-writer
- **Skills (4)**: harness-report, loopy-era-eval, qa-scenario-gen (merged into qa-cycle), self-improve
- **Commands (4)**: cc-apply, cc-sync, dashboard, project-orchestrator (merged into /team), scenario-test

---

## Hugh Skills To Codex Mapping

| Hugh capability           | Category        | Codex equivalent                                                | Status    |
| ------------------------- | --------------- | --------------------------------------------------------------- | --------- |
| `qa-cycle`                | QA              | Integrated: scenario gen + test execution                      | `adopt`   |
| `qa-functional`           | QA              | Browser plugin and Playwright candidate                         | `install` |
| `webapp-testing`          | QA              | Browser plugin and Playwright candidate                         | `install` |
| `agent-browser`           | QA              | Browser plugin                                                  | `adopt`   |
| `security-best-practices` | Security        | Absorbed into devops-specialist                                 | `install` |
| `vulnerability-scanner`   | Security        | DevOps security checklist                                       | `adapt`   |
| `pentest-checklist`       | Security        | DevOps threat model skill                                       | `adapt`   |
| `supabase-postgres`       | Platform        | Absorbed into backend-specialist                                | `adapt`   |
| `supabase-manager`        | Platform        | Absorbed into backend-specialist                                | `adapt`   |
| `mcp-project-mgr`         | Dev tools       | Capability registry and project docs                            | `adapt`   |
| `prompt-engineering`      | Dev tools       | Prompt/process guidance                                         | `adapt`   |
| `skill-creation`          | Dev tools       | Existing skill creator skills                                   | `adopt`   |
| `skill-judge`             | Dev tools       | Skill smoke-test registry                                       | `adapt`   |
| `discover-skills`         | Dev tools       | Local project skill: autonomous search + install from skills.sh | `adopt`   |
| `plugin-forge`            | Dev tools       | Existing plugin creator skill                                   | `adopt`   |
| `web-artifacts`           | Frontend        | Browser plus artifact generation patterns                       | `adapt`   |
| `frontend-design`         | Frontend        | Existing frontend/design guidance and Browser QA                | `adopt`   |
| `parallel-agents`         | Memory/workflow | Superpowers parallel-agent methods and Codex parallel tools     | `adopt`   |
| `context-window`          | Memory/workflow | AGENTS context hygiene and memory docs                          | `adapt`   |
| `agent-memory`            | Memory          | Memory docs plus future memory-bank compatibility check         | `adapt`   |
| `memory`                  | Memory          | Memory docs plus future memory-bank compatibility check         | `adapt`   |
| `remember-this`           | Memory          | Memory fact capture rules                                       | `adapt`   |
| `mermaid-diagrams`        | Docs            | Native Mermaid support in markdown                              | `adopt`   |
| `changelog-gen`           | Docs            | Future local changelog template                                 | `adapt`   |
| `dependency-updater`      | Dev tools       | Project-specific package manager checks                         | `defer`   |
| `git-commit`              | Dev tools       | Git workflow or future `yeet` skill                             | `defer`   |
| `init-project`            | Dev tools       | Harness initialization docs                                     | `adapt`   |
| `pdf`                     | Docs            | Installed PDF skill                                             | `adopt`   |
| `pptx`                    | Docs            | Presentations plugin                                            | `adopt`   |
| `xlsx`                    | Docs            | Spreadsheets plugin                                             | `adopt`   |
| `auto-idea`               | Product         | Product discovery workflow                                      | `defer`   |
| `auto-mvp`                | Product         | MVP planning workflow                                           | `defer`   |
| `indie-hacker`            | Product         | Product/business workflow                                       | `defer`   |
| `project-orch`            | Workflow        | Merged into /team command                                       | `adapt`   |

## Hugh Plugins To Codex Mapping

| Hugh plugin/source   | Codex equivalent                                            | Status    |
| -------------------- | ----------------------------------------------------------- | --------- |
| `superpowers`        | Installed Superpowers plugin skills                         | `adopt`   |
| `superpowers-chrome` | Browser plugin or Chrome plugin when user profile is needed | `adopt`   |
| `superpowers-dev`    | Superpowers development skills                              | `adopt`   |
| `superpowers-lab`    | Experimental methods only                                   | `defer`   |
| `episodic-memory`    | Memory docs and future compatibility check                  | `adapt`   |
| `claude-mem`         | Future memory capability search                             | `defer`   |
| `memory-bank`        | Hugh reference repo, possible install/adaptation target     | `adapt`   |
| `memory-bank-dev`    | Development reference only                                  | `defer`   |
| `pr-review-toolkit`  | GitHub plugin skills                                        | `adopt`   |
| `feature-dev`        | Superpowers planning/execution skills                       | `adopt`   |
| `agent-sdk-dev`      | Defer until agent SDK work exists                           | `defer`   |
| `commit-commands`    | Git workflow or future `yeet` skill                         | `defer`   |
| `code-review`        | Absorbed into qa-specialist                                 | `adopt`   |
| `security-guidance`  | Absorbed into devops-specialist                             | `install` |
| `frontend-design`    | Frontend/design guidance                                    | `adopt`   |
| `vs-design-diverge`  | Design exploration method                                   | `defer`   |
| `insights-ui`        | Observability reference repo                                | `defer`   |
| `claude-dashboard`   | Observability reference                                     | `defer`   |
| `oh-my-claudecode`   | Harness inspiration only                                    | `defer`   |
| `skillers-suda`      | Converted GPTaku skill                                      | `adopt`   |
| `kkirikkiri`         | Converted GPTaku skill                                      | `adopt`   |
| `vibe-sunsang`       | Converted GPTaku skill                                      | `adopt`   |
| `insane-search`      | Converted GPTaku skill                                      | `adopt`   |

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