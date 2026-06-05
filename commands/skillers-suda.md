---
description: "스킬러들의 수다 — 4명의 전문가가 수다 떨면서 바이브코더의 아이디어를 동작하는 스킬로 변환"
argument-hint: "[스킬 설명|분석 <스킬경로>]"
---

# /skillers-suda

스킬러들의 수다 — 4명의 전문가가 인터뷰로 아이디어를 동작하는 스킬로 변환합니다.

## 사용법

```bash
/skillers-suda                          # 인터랙티브 메뉴
/skillers-suda 유튜브 댓글 분석 스킬     # 바로 인터뷰 시작
/skillers-suda 분석 skills/my-skill      # 기존 스킬 분석
```

## 실행 방법

1. Read `.codex/skills/skillers-suda-command/SKILL.md`
2. 인수를 파싱해서 동작 결정:
   - 인수 없음 → 인터랙티브 메뉴
   - 스킬 설명 → 바로 인터뷰 시작
   - `분석 [경로]` → 기존 스킬/에이전트 분석
3. SKILL.md의 워크플로우를 따라 실행