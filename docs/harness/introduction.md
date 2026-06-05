# Codex Loopy Harness — 시스템 소개 및 운용 방안

> **핵심 철학**: "Tools are weaker than structure. Structure is weaker than verification structure."
> 에이전트의 완료 선언을 완료 증명으로 받아들이지 않습니다. 파일, 로그, 테스트, 증빙 레코드만이 진실의 원천입니다.

---

## 1. 시스템 개요

**Codex Loopy Harness**는 Claude Code / Codex를 개인 **"Work OS"**로 변환하는 검증 우선(verification-first) 자율 자가개선 하네스 시스템입니다. Hugh Kim의 멀티에이전트 오케스트레이션 패턴에서 영감을 받았으며, 단일 AI 어시스턴트를 **15개 전문 에이전트 팀**으로 구조화하여 소프트웨어 개발 라이프사이클 전체를 자동화합니다.

### 왜 하네스인가?

| 문제　　　　　　　　　　　　　　　　  | 하네스의 해결　　　　　　　　　 |
| ------------------------------------- | ------------------------------- |
| AI가 "완료"라고 말해도 실제론 미완료  | 증빙 기반 검증 게이트　　　　　 |
| 단일 에이전트의 컨텍스트 한계　　　　 | 전문 에이전트 분산 처리　　　　 |
| 코드 품질 관리 부재　　　　　　　　　 | 자동화된 훅(Hook) + 정적 리뷰　 |
| 반복적 QA 수동 수행　　　　　　　　　 | 폐루프 QA 자동화 (최대 5라운드) |

---

## 2. 3-레이어 아키텍처

```
┌─────────────────────────────────────────────────┐
│  Layer 1: Manager-Orchestrator (Opus 모델)       │
│  • 기획, 태스크 분배, 리뷰, QA 루프 실행          │
│  • ❌ 프로덕션 코드 직접 작성 금지                  │
├─────────────────────────────────────────────────┤
│  Layer 2: Specialist Agents (Sonnet 모델)        │
│  • 15개 전문 에이전트가 역할별 경계 내에서 작업      │
│  • 파일 수정 권한 엄격 분리                         │
├─────────────────────────────────────────────────┤
│  Layer 3: Automated Hooks (결정론적 게이트)        │
│  • 툴 사용 후 자동 트리거되는 쉘 스크립트           │
│  • Lint, 권한 검사, SQL 샌니타이징 등 즉시 시행     │
└─────────────────────────────────────────────────┘
```

### Layer 1 — Manager-Orchestrator

- **역할**: 오케스트레이션 팀 리더 — 계획, 위임, 검증
- **책임**: 전체 SDLC 감독, `docs/plan.md` 관리, 빌드/검증 명령 실행, Git 배포
- **철칙**: 프로덕션 소스 코드를 직접 작성하지 않음. 모든 코드 변경은 전문가에게 위임

### Layer 2 — Specialist Agents (15개)

각 에이전트는 **파일 수정 경계(file boundary)**가 엄격히 정의되어 있습니다:

| 에이전트    | 허용 경로                                              | 차단 경로                   |
| ----------- | ------------------------------------------------------ | --------------------------- |
| Frontend    | `src/components/**`, `src/pages/**`, `src/hooks/**`    | `src/api/**`, 서버 코드     |
| Backend     | `src/api/**`, `src/lib/server/**`, `src/middleware/**` | `src/components/**`, 페이지 |
| Supabase    | `supabase/migrations/**`, `supabase/config.toml`       | 모든 `src/**`               |
| Security    | 보안 감사 결과, 취약점 리포트                          | 비보안 프로덕션 코드        |
| Test-Writer | `test/**`, `__tests__/**`                              | 프로덕션 소스               |
| 기타 전문가 | 각자의 전문 영역                                       | 정의된 경계 외              |

### Layer 3 — Automated Hooks (9개)

쉘 스크립트 기반 자동화 게이트가 도구 사용 후 즉시 정책을 시행합니다:

| 훅(Hook)                      | 기능                    |
| ----------------------------- | ----------------------- |
| `auto-validate.sh`            | 자동 검증 실행          |
| `agent-permission-check.sh`   | 에이전트 파일 권한 검사 |
| `scaffold-violation-check.sh` | 스캐폴딩 위반 감지      |
| `no-localstorage.sh`          | localStorage 사용 차단  |
| `sql-injection-check.sh`      | SQL 인젝션 패턴 감지    |
| `require-telegram-notify.sh`  | 텔레그램 알림 필수 검사 |
| `require-isPWA-check.sh`      | PWA 체크 필수 검사      |
| `pre-push-qa.sh`              | 푸시 전 QA 게이트       |
| `context-enrichment.sh`       | 컨텍스트 자동 보강      |
| `session-cleanup.sh`          | 세션 종료 시 정리       |

---

## 3. 8-Phase 실행 룰

모든 개발 라이프사이클은 다음 8단계를 **순차적**으로 거칩니다:

```
P1: Plan Scaffolding      → Manager가 docs/plan.md에 태스크/요구사항 정의
P2: Architecture Design   → Architect가 파일, 스크립트, 초기 의존성 설정
P3: Database Schema       → Supabase Specialist가 Postgres 마이그레이션/RLS 작성
P4: Parallel Implement    → Frontend와 Backend가 병렬로 동시 작업
P5: Test Suite Generation → Test Writer가 Vitest/Jest/E2E 테스트 파일 구현
P6: Static Review         → Reviewer가 디자인 패턴/클린코드 원칙 검사
P7: Closed-Loop QA        → QA/Security가 이슈 식별 → 전문가 라우팅 → 재테스트 (통과까지)
P8: Ship & Notify         → Manager가 CHANGELOG.md 갱신, 커밋, 푸시, 알림
```

### 병렬 처리 규칙 (P4)

- 동시에 subagent를 사용할 때, 각 subagent는 **분리된(disjoint) 파일 세트**를 받습니다
- 병렬 작업 완료 후 파일 충돌이 없는지 확인합니다

---

## 4. 스킬 시스템 (9개)

스킬은 재사용 가능한 자동화 워크플로우입니다:

| 스킬              | 용도                                        |
| ----------------- | ------------------------------------------- |
| `init-project`    | 프로젝트 코드베이스 프로파일링 및 초기 설정 |
| `team`            | 8-Phase SDLC 오케스트레이션 파이프라인 실행 |
| `qa-scenario-gen` | QA 테스트 계획 및 커버리지 매트릭스 생성    |
| `qa-cycle`        | 자동화 다중 라운드 QA 테스트 (최대 5라운드) |
| `harness-report`  | 하네스 상태 리포트 생성                     |
| `loopy-era-eval`  | Loopy Era 평가 실행                         |
| `self-improve`    | 하네스 자가 개선 사이클                     |
| `discover-skills` | 외부 스킬 검색 및 발견                      |
| `install-gptaku`  | GPTaku 브릿지 설치                          |

### 핵심 스킬: QA Cycle (폐루프 QA)

```
[테스트 실행] → [결과 분석] → [심각도 분류] → [전문가 라우팅]
     ↑                                              ↓
     └──── [수정 검증] ←── [전문가 수정] ←──────────┘

     (최대 5라운드 반복, 잔여 이슈는 에스컬레이션)
```

---

## 5. 커맨드 시스템 (5개)

| 커맨드                  | 용도                              |
| ----------------------- | --------------------------------- |
| `/team`                 | 8-Phase SDLC 파이프라인 트리거    |
| `/project-orchestrator` | 프로젝트 오케스트레이터 직접 호출 |
| `/dashboard`            | 프로젝트 상태 대시보드            |
| `/cc-apply`             | 변경사항 적용                     |
| `/cc-sync`              | 동기화 실행                       |
| `/scenario-test`        | 시나리오 테스트 실행              |

---

## 6. 폐루프(Fail-Closed) 규칙

다음 중 **하나라도** 발생하면 작업 완료/병합이 불가합니다:

- ❌ 비 trivial 태스크에 수용 기준(Acceptance Criteria)이 누락된 경우
- ❌ 필수 빌드 검증, tsc 체크, 또는 린팅이 실패한 경우
- ❌ **CRITICAL** 또는 **HIGH** 심각도 QA 이슈가 미해결된 경우
- ❌ 계약에 명시된 검증 명령이 실행되지 않은 경우
- ❌ 코드 편집이 선언된 파일 경계를 위반한 경우

---

## 7. 메모리 시스템 (SQLite Memory Bank)

하네스는 지속 가능한 지식을 SQLite 메모리 뱅크에 저장합니다:

| 타입         | 용도                    | 예시                                   |
| ------------ | ----------------------- | -------------------------------------- |
| `decision`   | 기술적 선택과 정당성    | "Supabase RLS 대신 앱 레벨 인가 채택"  |
| `preference` | 개발자/사용자 선호      | "컴포넌트는 함수형으로"                |
| `pattern`    | 재사용 가능한 코드 구조 | "React Query + Optimistic Update 패턴" |
| `constraint` | 환경 제약사항           | "Node 18+ 필수"                        |
| `knowledge`  | 안정적인 프로젝트 사실  | "API 베이스 URL = /api/v2"             |

---

## 8. 운용 방안 — 실전 조작 가이드

### 8.1 새 프로젝트 시작

```
1. /init-project 실행 → 코드베이스 프로파일링 + CLAUDE.md 자동 생성
2. /team 실행 → 8-Phase SDLC 자동 시작
3. Manager가 plan.md 작성 → 전문가에게 태스크 위임
4. 자동 QA + 검증 → 완료 시 알림
```

### 8.2 기존 프로젝트 관리

```
1. /dashboard 실행 → 현재 상태 확인
2. 이슈 발생 시 /scenario-test로 재현
3. /qa-cycle 실행 → 자동 QA 루프
4. 이슈 발견 → 해당 전문가에게 자동 라우팅
5. 수정 후 재검증 → 통과 시 배포
```

### 8.3 품질 게이트 운용

```
# pre-push-qa.sh: 푸시 전 자동 QA
# auto-validate.sh: 파일 변경 시 자동 검증
# scaffold-violation-check.sh: 구조 위반 감지

개발자가 코드를 작성/수정하면:
  → Hook이 자동 실행
  → 위반 감지 시 즉시 차단
  → 통과 시에만 다음 단계 진행
```

### 8.4 에이전트 팀 조작

```
# 특정 전문가 직접 호출
/architect-designer → 아키텍처 설계
/frontend-specialist → 프론트엔드 구현
/backend-specialist → 백엔드 구현
/security-specialist → 보안 감사
/code-reviewer → 코드 리뷰

# 병렬 처리 (P4 Phase)
Frontend-specialist ∥ Backend-specialist → 동시 작업
# 각각 분리된 파일 세트에서 작업, 완료 후 병합
```

### 8.5 자가 개선 사이클

```
/self-improve 실행 → 하네스가 자신의 성능을 분석
  → 병목 식별
  → 최적화 제안
  → 자동 적용 또는 승인 요청
```

---

## 9. 시스템 구조도

```
hugh.kim/
├── AGENTS.md              # 핵심 규칙 (3-레이어, 8-Phase, Fail-Closed)
├── CLAUDE.md              # Claude Code 설정
├── settings.json          # 하네스 설정
├── agents/                # 15개 전문 에이전트 정의
│   ├── manager-orchestrator.md
│   ├── architect-designer.md
│   ├── frontend-specialist.md
│   ├── backend-specialist.md
│   ├── supabase-specialist.md
│   ├── test-writer.md
│   ├── code-reviewer.md
│   ├── web-qa-tester.md
│   ├── security-specialist.md
│   ├── devops-specialist.md
│   ├── bug-fixer.md
│   ├── documentation-specialist.md
│   ├── performance-optimizer.md
│   ├── product-specifier.md
│   └── telegram-notifier.md
├── skills/                # 9개 자동화 스킬
├── commands/              # 6개 커맨드
├── hooks/                 # 10개 자동화 훅
├── rules/                 # 코딩 규칙 (프론트/백/QA)
├── scripts/               # 유틸리티 스크립트
├── docs/
│   ├── harness/           # 하네스 문서
│   │   ├── principles.md
│   │   ├── usage.md
│   │   ├── gates.md
│   │   ├── memory.md
│   │   └── capability-registry.md
│   ├── superpowers/       # 계획/스펙 문서
│   └── verification/      # QA 증빙 JSON
└── test/                  # 테스트 파일
```

---

## 10. 핵심 원칙 요약

| #   | 원칙                   | 설명                                              |
| --- | ---------------------- | ------------------------------------------------- |
| 1   | **검증 우선**          | 완료 선언 ≠ 완료 증명. 증빙이 있어야 완료         |
| 2   | **구조 > 도구**        | 도구보다 구조가 강하고, 구조보다 검증 구조가 강함 |
| 3   | **엄격한 경계**        | 에이전트는 정의된 파일 경계 내에서만 작업         |
| 4   | **폐루프**             | 게이트 통과 불가 시 작업 차단 (Fail-Closed)       |
| 5   | **자동화 최우선**      | Hook, Skill, Command로 반복 작업 자동화           |
| 6   | **지속 가능한 메모리** | 의사결정/패턴/제약을 SQLite에 영속 저장           |
| 7   | **자가 개선**          | 하네스가 자신의 성능을 분석하고 최적화            |

---

> **Codex Loopy Harness** — "에이전트를 믿지 말고, 증빙을 믿어라."

---

## 참고 문서

- [워크플로우 비교 분석 — 기존 스킬 체인 vs 하네스 8-Phase](workflow-comparison.md) → **하이브리드 3-Phase 워크플로우 추천**
- [하네스 원칙](principles.md) | [사용 가이드](usage.md) | [게이트](gates.md) | [메모리](memory.md) | [역량 레지스트리](capability-registry.md)
