# Codex Loopy Harness v2 — Agent & Hook Upgrade Plan

**기준 커밋**: `4e03749` → 현재 `334290f`
**참고 레포**: `jung-wan-kim/claude-code-infrastructure-showcase`

---

## 🔍 Gap Analysis

### 우리가 가진 것 (Current)
| 영역 | 현황 |
|------|------|
| Agents | 5개 (manager, frontend, backend, qa, devops) — 기본적 |
| Hooks | 10개 shell 스크립트 — 폴리싱/보안 중심 |
| Skills (.codex) | 20+ 스킬 — 풍부하지만 **자동 활성화 없음** |
| Commands | 5개 — 수동 호출만 |
| settings.json | 기본 hook 등록만 |

### Reference Repo에서 배울 점
| 혁신 | 설명 | 우선순위 |
|------|------|---------|
| **skill-activation-prompt** | UserPromptSubmit 훅이 키워드/인텐트 매칭으로 스킬 자동 제안 | 🔴 CRITICAL |
| **skill-rules.json** | 선언적 트리거 시스템 (keywords, intentPatterns, fileTriggers) | 🔴 CRITICAL |
| **post-tool-use-tracker** | 파일 변경 추적 → 세션별 캐시 → 빌드 검증 | 🟡 HIGH |
| **auto-error-resolver** | TSC 에러 자동 분석/수정 에이전트 | 🟡 HIGH |
| **code-architecture-reviewer** | 코드 아키텍처 리뷰 전문 에이전트 | 🟡 HIGH |
| **refactor-planner** | 리팩토링 계획 수립 에이전트 | 🟢 MEDIUM |
| **error-handling-reminder** | 편집 후 에러 핸들링 누락 감지 훅 | 🟢 MEDIUM |

---

## 📋 Implementation Plan

### Phase 1: Skill Auto-Activation System (핵심)
1. `skills/skill-rules.json` — 우리 스킬에 맞는 트리거 룰
2. `hooks/skill-activation-prompt.sh` + `.ts` — TypeScript 기반 자동 감지
3. `hooks/post-tool-use-tracker.sh` — 파일 변경 추적
4. `settings.json` 업데이트 — 훅 등록

### Phase 2: New Agents
5. `agents/auto-error-resolver.md` — 빌드/런타임 에러 자동 해결
6. `agents/code-architecture-reviewer.md` — 아키텍처 품질 리뷰
7. `agents/refactor-planner.md` — 리팩토링 계획 수립
8. `agents/documentation-architect.md` — 문서 아키텍처 관리

### Phase 3: Agent Upgrades
9. 기존 5개 에이전트 역할 강화 (trigger conditions, collaboration patterns 추가)

### Phase 4: Config & Docs
10. `CLAUDE.md`, `AGENTS.md` 업데이트
11. Hook 의존성 설치 (package.json, tsconfig.json)

---

## 📊 Expected Outcomes

- 스킬이 **자동으로** 활성화됨 (키워드/인텐트 매칭)
- 파일 편집 시 **변경 추적** + 빌드 검증 자동화
- 에이전트가 **10개**로 확장 (5 → 10+1 구조)
- 기존 에이전트가 **더 구체적인 지시**를 가짐