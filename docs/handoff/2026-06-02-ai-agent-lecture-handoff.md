# Handoff: AI Agent Lecture For WordPress Creators

Date: 2026-06-02
Repo: `/Users/parkjuncheol/Local Sites/AI Agent/hugh.kim`

## Current Project State

This repo contains a Hugh Kim-inspired Codex Loopy Harness:

- `AGENTS.md`: project-level operating rules.
- `docs/harness/principles.md`: verification-first principles.
- `docs/harness/capability-registry.md`: Hugh skills/plugins mapped to Codex capabilities.
- `docs/harness/gates.md`: fail-closed/fail-open gate rules.
- `docs/harness/memory.md`: durable memory fact model.
- `docs/harness/usage.md`: how to use the harness.
- `docs/harness/workflows/lecture-to-html-pdf.md`: lecture transcript to HTML deck to PDF workflow.
- `docs/prompts/lecture-to-html-pdf-prompt.md`: reusable prompt for deck generation.
- `docs/verification/*`: evidence templates and verification records.

Latest relevant commit:

```text
6d826a3 docs: add harness usage and lecture deck workflow
```

## User's Lecture Goal

The user wants to create a 2-hour lecture for people who:

- Have used AI chatbots like ChatGPT.
- Do not understand AI agents yet.
- Usually build websites with WordPress.
- Are not necessarily professional developers.
- Want practical ways to use AI agents for business, web production, automation, course materials, and coding-adjacent work.

The lecture should not be limited to WordPress, but WordPress should be used as a familiar anchor and demo environment.

## Core Lecture Concept

Recommended concept:

```text
ChatGPT 사용자에서 AI Agent 사용자로:
홈페이지 제작자를 위한 실전 AI 에이전트 입문
```

Core message:

```text
챗봇은 답을 해준다.
에이전트는 목표를 받고, 도구를 쓰고, 파일을 고치고, 검증까지 반복한다.
```

Stronger closing message:

```text
AI Agent 시대의 경쟁력은 프롬프트를 잘 쓰는 것이 아니라,
일을 구조화하고 검증 가능하게 맡기는 능력이다.
```

## Desired Tone And Teaching Style

- Beginner-friendly.
- Motivational, not just conceptual.
- Practical and visual.
- Not too developer-heavy.
- Show rather than make everyone follow live.
- Audience can later follow the documented workflow.
- Emphasize business productivity, safer automation, and verification.

## Visual Direction

The user wants a rich, attractive educational slide deck.

Design direction:

- White / near-white background.
- Tailwind orange as key color:
  - `orange-500`
  - `orange-600`
  - `orange-100`
- Neutral shade family:
  - `neutral-900`
  - `neutral-700`
  - `neutral-200`
  - `neutral-50`
- Clean educational SaaS/workshop style.
- Use diagrams, hierarchy, visual metaphors, and light emoji.
- Emoji examples:
  - 💬 Chatbot
  - 🤖 Agent
  - 🧭 Plan
  - 🛠 Execute
  - ✅ Verify
  - 🧠 Memory

## Proposed 2-Hour Structure

Recommended ratio:

```text
개념 40% + 시연 30% + 따라할 수 있는 정리 30%
```

Suggested sections:

1. Opening: why AI agents matter.
2. Chatbot vs agent.
3. Tool map: ChatGPT, Codex, Claude, Claude Code, Gemini, Antigravity, Cursor, IDE, CLI.
4. Work rules: AGENTS.md / CLAUDE.md.
5. Skills, plugins, MCP.
6. Agent workflow: request -> plan -> execute -> verify -> fix -> remember.
7. Demo: WordPress CTA snippet.
8. Plugin packaging and verification.
9. Optional future: MCP/WordPress automation.
10. Closing checklist and next steps.

## Concepts To Explain

Must include:

- AI chatbot vs AI agent.
- ChatGPT vs Codex.
- Claude vs Claude Code.
- Gemini vs Google Antigravity.
- CLI.
- IDE.
- Why some agent tools may not need a traditional IDE.
- AGENTS.md / CLAUDE.md as agent work manuals.
- Skills as reusable expert instructions.
- Plugins/connectors as tool integrations.
- MCP as an external-tool connection protocol.
- Closed-loop workflow.
- Evidence and verification.
- Safe WordPress automation.

## Beginner-Friendly Analogies

Use these:

- ChatGPT: 상담해주는 선생님.
- AI Agent: 작업실에 들어와 파일을 고치고 테스트하는 작업자.
- CLI: 말로 컴퓨터를 조종하는 창.
- IDE: 홈페이지 제작자의 작업대.
- AGENTS.md: 우리 회사 작업 매뉴얼.
- Skill: 특정 일을 잘하는 작업 레시피.
- MCP: 외부 도구와 연결되는 출입문.
- 검증 루프: “했다”가 아니라 “확인됐다”로 바꾸는 과정.

## Demo Recommendation

Best first demo:

```text
WordPress 글 하단에 CTA 박스를 자동으로 붙이는 코드 스니펫 만들기
```

Why:

- Practical for WordPress creators.
- Visually obvious result.
- Business-relevant.
- Simple enough for beginners.
- Can evolve into a plugin.
- Good surface for verification.

Demo flow:

1. Define desired CTA box.
2. Ask agent to clarify requirements.
3. Generate Code Snippets-compatible PHP.
4. Explain where to paste it.
5. Create test checklist.
6. Verify on staging/local WordPress.
7. Convert into a simple plugin structure.
8. Install/activate/verify plugin.
9. Discuss MCP/automation as future direction, with backup and rollback.

## Recommended Next Output

The next conversation should produce:

1. A 2-hour detailed lecture plan.
2. A 35-45 slide storyboard.
3. HTML deck design system.
4. WordPress CTA demo script.
5. Instructor speaking notes.
6. Student follow-along handout.
7. PDF export QA checklist.

## Reusable Prompt For Next Chat

```text
We are continuing a project in `/Users/parkjuncheol/Local Sites/AI Agent/hugh.kim`.

Use the Codex Loopy Harness in this repo. Read:
- AGENTS.md
- docs/harness/usage.md
- docs/harness/workflows/lecture-to-html-pdf.md
- docs/handoff/2026-06-02-ai-agent-lecture-handoff.md

Goal:
Develop a 2-hour beginner-friendly lecture for WordPress website creators who have used ChatGPT but do not understand AI agents yet.

The lecture should motivate the problem first, then explain core concepts, then demonstrate a practical WordPress-related AI agent workflow.

Audience:
- WordPress website creators, freelancers, small web agency operators.
- Not necessarily developers.
- Familiar with ChatGPT, unfamiliar with CLI, IDE, Codex, Claude Code, MCP, agent workflows.

Core message:
Chatbots answer. Agents take goals, use tools, modify files, verify results, and iterate.

Required topics:
- AI chatbot vs AI agent.
- ChatGPT / Codex.
- Claude / Claude Code.
- Gemini / Google Antigravity.
- CLI and IDE.
- AGENTS.md / CLAUDE.md.
- Skills, plugins, MCP.
- Closed-loop workflow: request -> plan -> execute -> verify -> fix -> remember.
- Safe WordPress automation.

Demo:
Use a WordPress CTA box example:
1. Create a Code Snippets-compatible PHP snippet.
2. Verify it on staging/local WordPress.
3. Convert it into a simple plugin.
4. Explain how MCP or automation could later connect to WordPress safely.

Visual direction:
- White / near-white background.
- Tailwind orange as key color: orange-500, orange-600, orange-100.
- Neutral palette: neutral-900, neutral-700, neutral-200, neutral-50.
- Rich educational slides with diagrams, visual hierarchy, and light emoji.

Outputs wanted:
1. 2-hour lecture design.
2. 35-45 slide storyboard.
3. Section-by-section speaking notes.
4. WordPress demo script.
5. HTML deck production plan.
6. PDF export QA checklist.

Before creating final artifacts, first present the lecture design for review.
```

