# Codex Loopy Harness & Karpathy Guidelines

This workspace uses a Hugh-inspired, verification-first Codex harness combined with Andrej Karpathy's behavioral guidelines.
Core Principle: Verification-first. We do not trust agent assertions without concrete, verifiable evidence.

---

## I. Karpathy Behavioral Guidelines

### 1. Think Before Coding
**Don't assume. Don't hide confusion. Surface tradeoffs.**
- State your assumptions explicitly. If uncertain, ask.
- If multiple interpretations exist, present them - don't pick silently.
- If a simpler approach exists, say so. Push back when warranted.

### 2. Simplicity First
**Minimum code that solves the problem. Nothing speculative.**
- No features beyond what was asked. No abstractions for single-use code.
- No "flexibility" or "configurability" that wasn't requested.
- If you write 200 lines and it could be 50, rewrite it.

### 3. Surgical Changes
**Touch only what you must. Clean up only your own mess.**
- Don't "improve" adjacent code, comments, or formatting.
- Don't refactor things that aren't broken.
- Match existing style. Remove imports/variables/functions that YOUR changes made unused.
- The test: Every changed line should trace directly to the user's request.

### 4. Goal-Driven Execution
**Define success criteria. Loop until verified.**
- Transform tasks into verifiable goals (e.g. write reproducing test first, then make it pass).
- For multi-step tasks, state a brief step-by-step verification plan.

---

## II. Codex Loopy Harness Operating Rules

### 1. Core Rule (Verification-First)
Tools are weaker than structure. Structure is weaker than verification structure.
Do not treat an agent's claim of completion as proof. Use files, logs, tests, screenshots, and evidence records as the source of truth.

### 2. Default Workflow
1. Define the contract/success criteria before implementation.
2. Check existing verified skills/plugins before inventing new tools.
3. Set file boundaries before editing.
4. Implement the smallest useful change.
5. Run targeted verification (tests/build/typecheck).
6. Run full QA when user-facing behavior, security, or shared contracts changed.
7. Record evidence and capture reusable decisions/patterns/constraints.

### 3. Fail-Closed Rules
Completion is blocked when:
- Acceptance criteria are missing for a non-trivial task.
- Required verification (build, typecheck, test, or browser verification) was not run or fails.
- A high or critical finding remains unresolved.
- File boundaries are violated.

### 4. Capability-First Rule
Before creating a new local tool, check:
- Installed Codex skills, enabled plugins, OpenAI curated skills, and project-local scripts.

### 5. Memory Rule
Capture durable facts in the SQLite memory-bank only:
- `decision`: Choices and technical justifications.
- `preference`: Developer/user preferences.
- `pattern`: Reusable code structures or failure modes.
- `constraint`: Fixed environment limitations.
- `knowledge`: Stable project facts.