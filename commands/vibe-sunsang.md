---
description: "바선생 — AI 활용 성장 멘토 에이전트 (시작/변환/멘토링/성장/지식)"
argument-hint: "[시작|변환|멘토링|성장|지식]"
---

# /vibe-sunsang

AI 활용 성장을 돕는 멘토 에이전트. 인자에 따라 적절한 스킬로 분기합니다.

## 사용법

```bash
/vibe-sunsang            # 안내 메뉴
/vibe-sunsang 시작        # 초기 설정
/vibe-sunsang 변환        # 대화 로그 변환
/vibe-sunsang 멘토링      # AI 활용 능력 코칭
/vibe-sunsang 성장        # 성장 리포트 생성
/vibe-sunsang 지식        # 개념/용어 학습
```

## 실행 방법

1. Read `.codex/skills/vibe-sunsang-command/SKILL.md`
2. 인자에 따라 분기:
   - `시작`/`setup` → vibe-sunsang-onboard
   - `변환`/`retro` → vibe-sunsang-retro
   - `멘토링`/`mentor` → vibe-sunsang-mentor
   - `성장`/`growth` → vibe-sunsang-growth
   - `지식`/`knowledge` → vibe-sunsang-knowledge
   - 인자 없음 → 선택 메뉴
3. 해당 스킬의 SKILL.md를 읽고 워크플로우 실행