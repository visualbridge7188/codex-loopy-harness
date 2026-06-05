---
name: discover-skills
description: >
  Autonomous skill discovery and dynamic tool mounting from the vercel-labs/skills
  ecosystem. When the agent detects a gap in its knowledge or tooling, it searches
  skills.sh, evaluates candidates, installs the best match, and integrates its
  executable tools into the current runtime. Triggers on phrases like "I don't have
  a tool for X", "how do I do X", "find a skill", "install a skill", or implicit
  capability gaps detected during task execution.
---

# Discover Skills — Autonomous Skill Discovery & Dynamic Tool Mounting

This skill enables the agent to autonomously discover, evaluate, and install
executable skills and tools from the [vercel-labs/skills](https://github.com/vercel-labs/skills)
ecosystem when it detects a gap in its current capabilities.

## Activation Triggers

Activate this skill when **any** of these conditions are true:

1. **Explicit request**: User says "find a skill for X", "install a skill",
   "search for a tool", or "I need help with X".
2. **Capability gap detected**: During task execution, the agent recognizes it
   lacks a tool, CLI, API, or knowledge area needed to complete the current task.
3. **Workflow failure**: A task fails because the right tool isn't available,
   and a skill might provide it.
4. **Proactive improvement**: After completing a task, the agent identifies a
   domain where an installed skill would improve future performance.

## Architecture

```text
┌──────────────┐     ┌───────────────┐     ┌──────────────┐     ┌─────────────┐
│  Gap Detect  │────▶│  Search API   │────▶│  Evaluate    │────▶│  Install &  │
│  Trigger     │     │  skills.sh    │     │  Candidate   │     │  Mount      │
└──────────────┘     └───────────────┘     └──────────────┘     └─────────────┘
```

### Phase 1: Gap Detection

Before searching externally, check local resources per the **Capability-First Rule**:

1. Installed Codex skills (see `docs/verification/skills-registry.md`)
2. Enabled plugins
3. Project-local scripts (`scripts/`, `bin/`)
4. Hugh reference capabilities (`docs/harness/capability-registry.md`)

If none of these cover the need, proceed to Phase 2.

### Phase 2: Search

Use the Skills CLI or the skills.sh API:

```bash
# CLI search (non-interactive, outputs JSON)
npx skills find <query>

# Direct API search
curl -s "https://skills.sh/api/search?q=<encoded-query>&limit=10"
```

The local helper script provides structured output:

```bash
# Project-local helper (recommended)
node skills/discover-skills/search-skills.mjs "<query>"
```

This returns JSON with `name`, `slug`, `source`, `installs` for each result.

### Phase 3: Evaluate

Before installing, verify quality:

| Criterion | Threshold | Action |
|-----------|-----------|--------|
| Install count | ≥ 1,000 installs | Proceed confidently |
| Install count | 100–999 | Proceed with caution, inspect content |
| Install count | < 100 | Skip unless source is trusted |
| Source reputation | Official org (`vercel-labs`, `anthropics`, `microsoft`) | Trust |
| Source reputation | Known community author | Verify |
| Source reputation | Unknown | Inspect content before installing |

**Security gate**: Never install a skill from an unknown source without reading
its SKILL.md content first. Use:

```bash
gh api repos/<owner>/<repo>/contents/skills/<skill-name>/SKILL.md --jq '.content' | base64 -d
```

### Phase 4: Install & Mount (Dynamic Tool Integration)

Installation places the skill's instructions and tools into the agent's
configuration directory. The agent must then **mount** the new tools by:

1. **Installing the skill** to the correct scope:
   ```bash
   # Global scope (available across all projects)
   npx skills add <owner/repo@skill-name> -g -y

   # Project scope (shared with team via version control)
   npx skills add <owner/repo@skill-name> -y
   ```

2. **Reading the installed SKILL.md** immediately after install to understand:
   - What tools/commands the skill provides
   - What triggers the skill's activation
   - What the skill's output format is
   - How to invoke the skill's capabilities

3. **Dynamically mounting** the skill's executable tools into the current
   runtime session. This means:
   - If the skill provides CLI commands → use `execute_command` to call them
   - If the skill provides API patterns → incorporate them into the workflow
   - If the skill provides code templates → use them as scaffolding
   - If the skill provides workflow instructions → follow them step by step

4. **Recording the new capability**:
   - Update `docs/verification/skills-registry.md` with the new skill entry
   - Update `docs/harness/capability-registry.md` status if applicable
   - Log the install command and quality assessment in the task evidence

5. **Resuming** the original task with the new capability now active.

```text
Install → Read SKILL.md → Identify tools → Mount to runtime → Resume task
```

#### Listing Installed Skills

```bash
# List all installed skills
npx skills list

# List with project-local helper
node skills/discover-skills/search-skills.mjs --installed
```

## Decision Protocol

When this skill triggers during a task:

```text
IF current task needs capability X
AND X is not in local skills/plugins/scripts
THEN
  search_skills("X domain keywords")
  IF results found
    evaluate_best_candidate()
    IF quality_threshold_met()
      install_candidate()
      mount_tools()
      resume_task()
    ELSE
      log("Skill found but below quality threshold")
      attempt_with_general_capabilities()
    END
  ELSE
    log("No skills found for: X")
    attempt_with_general_capabilities()
  END
END
```

## Common Search Queries by Domain

| Domain | Example Queries |
|--------|----------------|
| Web Development | `react`, `nextjs`, `typescript`, `css`, `tailwind`, `frontend` |
| Testing | `testing`, `jest`, `playwright`, `e2e`, `tdd` |
| DevOps | `deploy`, `docker`, `kubernetes`, `ci-cd`, `terraform` |
| Documentation | `docs`, `readme`, `changelog`, `api-docs` |
| Code Quality | `review`, `lint`, `refactor`, `best-practices` |
| Design | `ui`, `ux`, `design-system`, `accessibility` |
| Security | `security`, `vulnerability`, `pentest`, `threat-model` |
| Data | `database`, `postgres`, `supabase`, `migration` |
| Productivity | `workflow`, `automation`, `git`, `commit` |
| AI/ML | `ai`, `ml`, `model`, `prompt`, `rag`, `embedding` |

## Integration with Harness

This skill integrates with the Codex Loopy Harness as follows:

- **Capability-First Rule**: This skill is the external fallback when internal
  capabilities are insufficient.
- **Fail-Closed**: Skill installation from untrusted sources is blocked until
  content review.
- **Fail-Open**: Skill search and evaluation never block task completion.
- **Memory**: Installed skills are recorded in the skills registry for future sessions.

## Evidence Requirements

When this skill is used during a task:

1. Record which capability gap triggered the search.
2. Record the search query and results count.
3. Record the chosen skill, its source, and install count.
4. If installed, record the installation command and outcome.
5. Update `docs/verification/skills-registry.md` with the new entry.

## Troubleshooting

| Problem | Solution |
|---------|----------|
| `npx skills` not found | Run `npm install -g skills` or use `npx -y skills` |
| API returns empty | Try alternative keywords or check network |
| Install fails | Check write permissions, try with `--copy` flag |
| Skill doesn't activate | Check agent compatibility with `-a` flag |
| Multiple candidates | Prefer highest install count from trusted source |