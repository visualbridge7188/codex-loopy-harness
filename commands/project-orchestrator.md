---
description: Manage subagent allocations and tasks
---

# /project-orchestrator

Commands the Manager-Orchestrator agent to inspect the current SDLC task list and delegate items to specialists.

## Usage

```bash
/project-orchestrator status
/project-orchestrator delegate <task_id>
```

## Options
- `status`: Lists all tasks, showing in-progress items, file boundaries, and assigned specialist subagents.
- `delegate`: Explicitly triggers subagent execution for a specific task.
