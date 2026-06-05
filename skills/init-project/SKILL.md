---
name: init-project
description: Profile project folders and bootstrap configurations
---

# init-project Skill

This skill profiles a target project codebase and bootstraps initial settings.

## Workflow Phases
1. **Analyze:** Inspect directory files to map out tech stack and database integrations.
2. **Setup config:** Initialize or update the project baseline `CLAUDE.md`.
3. **Scaffold rules:** Create `SKILL.md` containing core guidelines and styling conventions.
4. **Link hooks:** Configure event triggers (e.g. linter, compilers) based on stack parameters.

## Execution Rules
- Never delete pre-existing user configurations.
- Profile directories using non-destructive reads (`find`, `glob`).
