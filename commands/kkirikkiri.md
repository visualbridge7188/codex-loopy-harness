---
description: "끼리끼리 — 자연어 한마디로 AI 에이전트 팀을 자동 구성하고 실행"
argument-hint: "[자연어 요청]"
---

# /kkirikkiri

자연어 입력을 받아 AI 에이전트 팀을 자동 구성하고 실행합니다. 리서치, 개발, 분석, 콘텐츠 — 목적에 맞는 팀을 만들어보세요.

## 사용법

```bash
/kkirikkiri                              # 인터랙티브 메뉴
/kkirikkiri 리서치 팀 만들어줘             # 바로 팀 구성 시작
```

## 실행 방법

1. Read `.codex/skills/kkirikkiri-command/SKILL.md`
2. 인수를 파싱해서 동작 결정:
   - 인수 없음 → 인터랙티브 메뉴
   - 자연어 요청 → 의도 파악 → 인터뷰 → 팀 생성 → 실행
3. SKILL.md의 워크플로우를 따라 실행