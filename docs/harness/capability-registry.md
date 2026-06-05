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
| `pentest-checklist` | Security | Security threat model skill plus local checklist | `adapt` |
| `supabase-postgres` | Platform | Supabase-specific skill/method | `defer` |
| `supabase-manager` | Platform | Supabase-specific skill/method | `defer` |
| `mcp-project-mgr` | Dev tools | Capability registry and project docs | `adapt` |
| `prompt-engineering` | Dev tools | Prompt/process guidance | `adapt` |
| `skill-creation` | Dev tools | Existing skill creator skills | `adopt` |
| `skill-judge` | Dev tools | Skill smoke-test registry | `adapt` |
| `discover-skills` | Dev tools | Local project skill: autonomous search + install from skills.sh | `adopt` |
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
| `skillers-suda` | Converted GPTaku skill | `adopt` |
| `kkirikkiri` | Converted GPTaku skill | `adopt` |
| `vibe-sunsang` | Converted GPTaku skill | `adopt` |
| `insane-search` | Converted GPTaku skill | `adopt` |

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
