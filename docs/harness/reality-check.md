# 하네스 현실 점검 — 실용적 가이드

> **상태**: ✅ 축소 완료 — 과대평가된 문서 프레임워크를 실용적인 최소 구조로 정리함
> (2026-06-05: 15 에이전트→5, 8 스킬→4, 6 커맨드→2)

---

## 1. 현재 상태 정리

### 실행 가능한 것 (실제 작동)

| 항목 | 파일 | 상태 |
|------|------|------|
| Hooks (10개) | `hooks/*.sh` | ✅ 쉘 스크립트 — Claude Code에서 자동 실행 |
| AGENTS.md | 루트 | ✅ Claude Code가 자동 로드 |
| CLAUDE.md | 루트 | ✅ Claude Code가 자동 로드 |
| settings.json | 루트 | ✅ Claude Code 설정 |

### "지시서"인 것 (AI가 읽고 수동 수행)

| 항목 | 파일 | 상태 |
|------|------|------|
| Agents (5개) | `agents/*.md` | 📄 페르소나 정의 — AI가 역할扮演 |
| Skills (4개) | `skills/*/SKILL.md` | 📄 단계별 지시서 — AI가 순서대로 수행 |
| Commands (2개) | `commands/*.md` | 📄 워크플로우 설명서 |

---

## 2. 그럼 어떻게 쓰는 건가?

### Claude Code에서의 사용법

Claude Code는 `CLAUDE.md`와 `AGENTS.md`를 **자동으로 로드**합니다.
따라서:

```
1. Claude Code 실행
2. CLAUDE.md가 자동 로드됨 → 하네스 규칙 활성화
3. Hooks가 자동 트리거됨 → 파일 변경 시 검증 실행
4. 사용자가 명령 입력 → AI가 commands/*.md를 참조하여 수행
```

**핵심**: Claude Code에서는 "설치" 개념이 필요 없습니다.
파일이 워크스페이스에 있으면 자동으로 인식됩니다.

### Cline (현재 환경)에서의 사용법

Cline은 `CLAUDE.md`를 인식하지만, `commands/`나 `skills/`를 자동 로드하지 않습니다.

```
1. /team 하고 싶으면 → commands/team.md 내용을 복사해서 입력
2. /qa-cycle 하고 싶으면 → skills/qa-cycle/SKILL.md 내용을 복사해서 입력
3. 에이전트 역할 부여 → agents/xxx.md 내용을 컨텍스트로 제공
```

---

## 3. 축소 결과 요약

### 이전 → 이후

| 항목 | 이전 | 이후 | 변경 |
|------|------|------|------|
| 에이전트 | 15개 | 5개 | 10개 삭제, 역할 흡수 |
| 스킬 | 8개 | 4개 | 4개 삭제, qa-scenario-gen→qa-cycle 통합 |
| 커맨드 | 6개 | 2개 | 4개 삭제, project-orchestrator→/team 통합 |

### 삭제된 것 (이유)

| 항목 | 이유 |
|------|------|
| `architect-designer` | Manager가 아키텍처 담당 |
| `bug-fixer` | 해당 전문가가 버그 수정 |
| `code-reviewer` | QA가 리뷰 흡수 |
| `documentation-specialist` | Manager가 문서 담당 |
| `performance-optimizer` | Frontend가 성능 최적화 |
| `product-specifier` | Manager가 제품 스펙 담당 |
| `security-specialist` | DevOps가 보안 담당 |
| `supabase-specialist` | Backend가 DB 담당 |
| `telegram-notifier` | DevOps가 알림 담당 |
| `test-writer` | QA가 테스트 작성 |
| `harness-report` | 불필요 |
| `loopy-era-eval` | 평가 인프라 없음 |
| `qa-scenario-gen` | qa-cycle에 통합 |
| `self-improve` | 메타 시스템 미구현 |
| `/cc-apply`, `/cc-sync` | scripts/apply.sh, scripts/sync.sh으로 충분 |
| `/dashboard` | 불필요 |
| `/project-orchestrator` | /team에 통합 |
| `/scenario-test` | /team 내부 단계로 |

---

## 4. 실용적 추천 — 남긴 것

```
✅ AGENTS.md         — 핵심 규칙 (간결하게 유지)
✅ CLAUDE.md         — 프로젝트 컨텍스트
✅ hooks/            — 실제 실행 가능한 자동화
✅ rules/            — 코딩 규칙 (AI가 참조)
✅ docs/harness/     — 참고 문서
✅ 5 에이전트        — 각각 흡수된 역할 포함
✅ 4 스킬            — 핵심 워크플로우만
✅ 2 커맨드          — /team, /init
```

---

## 5. 실제 워크플로우 시작 방법

### Claude Code 환경 (권장)

```bash
# 1. 저장소 클론
git clone https://github.com/visualbridge7188/codex-loopy-harness.git my-project
cd my-project

# 2. Claude Code 실행
claude

# 3. CLAUDE.md가 자동 로드됨 → 바로 작업 시작
> /team  # 8-Phase SDLC 시작
```

### Cline 환경 (현재)

```
1. 워크스페이스 열기 (이미 열려있음)
2. commands/team.md 내용을 프롬프트로 입력
3. AI가 단계별로 수행
```

---

## 6. 결론

**하네스는 "프롬프트 엔지니어링 프레임워크"로서 가치가 있습니다.**
실제 자동화 시스템이 아니지만, 구조화된 지시서를 통해 AI의 출력 품질을 높이는 역할을 합니다.
과도한 문서보다 핵심만 유지하는 것이 실용적입니다.

| 질문 | 답변 |
|------|------|
| 스킬 설치가 필요한가? | **아니요** — 파일이 있으면 AI가 읽습니다 |
| 에이전트가 실제 작동하는가? | **페르소나 전환**으로 부분 작동 |
| 축소 후에도 충분한가? | **네** — 흡수된 역할이 각 에이전트에 명시되어 있습니다 |
| 무엇을 먼저 해야 하나? | **핵심만 남긴 상태 → 실제 프로젝트에 적용** |