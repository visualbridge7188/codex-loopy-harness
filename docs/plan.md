# 하네스 축소/통합 계획

> 목표: 과대평가된 "문서 프레임워크"를 실용적인 최소 구조로 축소

---

## Phase 1: 에이전트 축소 (15개 → 5개)

### 유지
| 에이전트 | 역할 |
|----------|------|
| `manager-orchestrator.md` | 팀 리더 — 계획, 위임, 검증 |
| `frontend-specialist.md` | 프론트엔드 개발 |
| `backend-specialist.md` | 백엔드 개발 |
| `web-qa-tester.md` | QA + 테스트 (test-writer, code-reviewer 흡수) |
| `devops-specialist.md` | 배포 + 인프라 (telegram-notifier 흡수) |

### 제거 (10개)
- `architect-designer.md` → manager가 아키텍처 담당
- `bug-fixer.md` → 해당 전문가가 버그 수정
- `code-reviewer.md` → QA가 리뷰 흡수
- `documentation-specialist.md` → manager가 문서 담당
- `performance-optimizer.md` → frontend/backend가 성능 최적화
- `product-specifier.md` → manager가 제품 스펙 담당
- `security-specialist.md` → devops가 보안 담당
- `supabase-specialist.md` → backend가 DB 담당
- `telegram-notifier.md` → devops가 알림 담당
- `test-writer.md` → QA가 테스트 작성

---

## Phase 2: 스킬 축소 (8개 → 4개)

### 유지
| 스킬 | 역할 |
|------|------|
| `init-project` | 프로젝트 초기 설정 |
| `team` | 8-Phase SDLC 파이프라인 |
| `qa-cycle` | QA 자동화 루프 |
| `discover-skills` | 외부 스킬/도구 검색 (사용자 요청) |

### 제거 (4개)
- `harness-report` → 불필요
- `loopy-era-eval` → 평가 인프라 없음
- `qa-scenario-gen` → qa-cycle에 통합
- `self-improve` → 메타 시스템 미구현

### 통합
- `qa-scenario-gen` 내용 → `qa-cycle/SKILL.md`에 시나리오 생성 단계로 추가

---

## Phase 3: 커맨드 축소 (6개 → 2개)

### 유지
| 커맨드 | 역할 |
|--------|------|
| `/team` | 모든 개발 워크플로우 (project-orchestrator 흡수) |
| `/init` | 프로젝트 초기화 (init-project 연동) |

### 제거 (4개)
- `cc-apply.md` → scripts/apply.sh으로 충분
- `cc-sync.md` → scripts/sync.sh으로 충분
- `dashboard.md` → 불필요
- `project-orchestrator.md` → /team에 통합
- `scenario-test.md` → /team 내부 단계로

---

## Phase 4: 유지 에이전트 내용 업데이트

각 유지 에이전트에 흡수된 역할을 명시:

1. **manager-orchestrator.md** — architect, documentation, product-specifier 역할 추가
2. **web-qa-tester.md** → **qa-specialist.md**로 이름 변경 — test-writer, code-reviewer 역할 추가
3. **devops-specialist.md** — telegram-notifier, security 역할 추가
4. **backend-specialist.md** — supabase/DB 역할 추가

---

## Phase 5: 문서 업데이트

1. **AGENTS.md** — 축소된 구조 반영
2. **CLAUDE.md** — 축소된 커맨드/에이전트 반영
3. **docs/harness/introduction.md** — 카운트 업데이트 (5 에이전트, 4 스킬, 2 커맨드)
4. **docs/harness/reality-check.md** — 완료 상태로 업데이트
5. **docs/harness/capability-registry.md** — 축소 반영

---

## Phase 6: 정리 및 푸시

1. 삭제된 파일 git rm
2. 커밋: `refactor: shrink harness to practical minimum (5 agents, 4 skills, 2 commands)`
3. GitHub 푸시

---

## 위험 및 주의사항

- ❌ hooks/는 건드리지 않음 (실행 가능한 코드)
- ❌ rules/는 건드리지 않음 (참조 문서)
- ❌ scripts/는 건드리지 않음 (실행 가능한 코드)
- ⚠️ qa-cycle에 qa-scenario-gen 내용을 먼저 통합 후 삭제
- ⚠️ /team 커맨드에 project-orchestrator 내용 통합 후 삭제