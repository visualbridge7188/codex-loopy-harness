# AI Agent Lecture Deck v5 — 계획서

> **목적:** deck-v4의 내용은 유지하되, frontend-slides 아키텍처를 적용하여 PPT 품질을 근본적으로 개선한다.

---

## 1. 문제 진단: deck-v4에서 무엇이 잘못되었나

| 항목 | deck-v4 현황 | 문제 |
|---|---|---|
| 슬라이드 엔진 | 자체 제작 (display:none/block) | frontend-slides의 viewport-base.css 미사용 |
| 전환 방식 | `display: none/block` 토글 | frontend-slides는 `visibility/opacity` 사용 |
| 편집 모드 | 없음 | frontend-slides 인라인 편집 미지원 |
| URL 네비게이션 | `?slide=N` 있음 | OK |
| 애니메이션 | 없음 | frontend-slides `.reveal` 스태거 패턴 미사용 |
| 폰트 로딩 | 로컬 woff2 (Paperlogy/Pretendard) | OK — v4 디자인 룰 준수 |
| 색상 체계 | Orange + Cobalt | OK — v4 디자인 룰 준수 |
| 슬라이드 밀도 | 텍스트 과다, 시각 요소 부족 | "각 슬라이드 = 하나의 주장 + 하나의 시각 증명 객체" 미준수 |
| 인포그래픽 | 거의 없음 | 도표, 그리드, 다이어그램 활용 부족 |

**핵심 원인:** frontend-slides 스킬을 설치하지 않고 자체 제작했음.

---

## 2. frontend-slides 아키텍처 요구사항 (SKILL.md 기준)

### 2.1 필수 포함 (viewport-base.css)
- `.deck-viewport`: position fixed, overflow hidden
- `.deck-stage`: 1920x1080, transform-origin 0 0
- `.slide`: position absolute, visibility/opacity 전환 (display:none 금지)
- `.slide.active/.visible`: visibility visible, opacity 1
- `@media print`: break-after page
- `@media (prefers-reduced-motion)`: animation 축소

### 2.2 SlidePresentation 클래스
- `setupStageScale()`: 윈도우 리사이즈에 맞춰 스케일 조정
- `setupKeyboardNav()`: Arrow 키, Space, PageUp/Down
- `setupTouchNav()`: 스와이프 지원
- `showSlide(index)`: active/visible 클래스 토글 + URL 업데이트

### 2.3 인라인 편집 모드
- 좌측 상단 hotzone hover → 편집 버튼 표시
- 클릭/E키 → contentEditable 토글
- 편집 중일 때 오렌지색 버튼으로 상태 표시

### 2.4 애니메이션 패턴
- `.reveal` 클래스: opacity 0 → 1, translateY(20px) → 0
- `.slide.visible .reveal` 에서 활성화
- nth-child 순차 delay (0.1s 간격)

---

## 3. v4 디자인 룰 (변경 없이 그대로 유지)

```
- Title font: Paperlogy
- Body font: Pretendard
- 메인 강조: Orange (#f97316, #ea580c, #ffedd5)
- 구조/크롬: Cobalt (#1d4ed8, #dbeafe)
- 배경: #fafafa (paper)
- 텍스트: #171717 (ink), #525252 (muted)
- 보더: #d4d4d4 (line)
- 보더 라디우스: 8px 이하
- 중첩 카드: 금지
- 장식용 그라데이션 오브: 금지
- 스토리보드 prose 직접 덤프: 금지
- 각 슬라이드: 하나의 주장 + 하나의 시각 증명 객체
- 키보드 단축키 텍스트 설명: 금지
```

---

## 4. 슬라이드 계약 (46장 — v4와 동일)

| # | 제목 | 시각 증명 객체 타입 |
|---|---|---|
| 1 | 좋은 머리에 손이 생겼다 | grid-2 panel (이전 AI vs Agent) |
| 2 | 오늘의 지도: 두뇌에서 자동화까지 | flow 4-step |
| 3 | 우리가 알던 AI는 좋은 머리였다 | quote block |
| 4 | 대답과 결과물은 다른 문제다 | grid-2 panel |
| 5 | 마지막 1마일은 사람이 했다 | flow 3-step |
| 6 | 챗봇은 답을 주고 Agent는 일을 맡는다 | table (3-col) |
| 7 | 결과물이 채팅창 밖으로 나온다 | grid-2 panel |
| 8 | AI의 손은 연결에서 나온다 | flow 4-step |
| 9 | 연결되면 업무가 이어진다 | branch-map 5-col |
| 10 | 이름보다 역할로 이해한다 | table (3-col) |
| 11 | 모델은 엔진이다 | panel + code |
| 12 | 도구는 실행 환경이다 | grid-2 panel |
| 13 | 도구는 많지만 형태는 몇 가지다 | table (3-col) |
| 14 | CLI는 말로 컴퓨터를 조종하는 창이다 | terminal-window |
| 15 | IDE는 개발자의 작업대다 | ide-frame |
| 16 | 대화형 Agent 도구는 작업창을 감춘다 | panel |
| 17 | 오늘 설치할 도구: Codex 또는 Antigravity | terminal-window |
| 18 | 코딩은 컴퓨터에게 일을 시키는 언어다 | quote block |
| 19 | 한 문장 요청은 빈칸이 너무 많다 | grid-2 panel |
| 20 | Agent는 초엘리트 신입사원이다 | quote block |
| 21 | 신입사원에게 필요한 세 가지 | flow 3-step |
| 22 | AGENTS.md는 회사 작업 매뉴얼이다 | manual-doc |
| 23 | 좋은 AGENTS.md는 짧고 강하다 | manual-doc + code |
| 24 | Skill은 특정 일을 위한 작업 레시피다 | panel |
| 25 | 검증된 Skill을 가져와도 된다 | panel |
| 26 | Superpowers는 작업 루프를 강제한다 | flow 5-step |
| 27 | Brainstorming: 요구사항을 같이 발견한다 | panel + code |
| 28 | Writing Plans: 실행 전 설계도를 만든다 | panel + code |
| 29 | Executing/Subagent: 계획을 작은 단위로 실행한다 | panel |
| 30 | Verification: 완료를 증거로 확인한다 | panel + code |
| 31 | Debugging: 실패 증거로 수정한다 | panel |
| 32 | 전체 루프: 계획, 실행, 검증, 수정 | flow 5-step |
| 33 | 외부 도구로 가는 출입문이 필요하다 | panel |
| 34 | MCP는 외부 서버에 손을 연결한다 | terminal-window |
| 35 | WordPress MCP 자율 루프 | flow 4-step |
| 36 | GitHub는 저장소이자 멀티버스 작업 공간이다 | panel |
| 37 | 오늘의 실험: 네이버 블로그 RSS에서 WordPress로 | terminal-window + table |
| 38 | 실습 1: Agent 도구 설치 | terminal-window |
| 39 | 실습 2: Superpowers 설치와 첫 요청 | terminal-window |
| 40 | 실습 3: 요구사항을 구체화한다 | panel + code |
| 41 | 실습 4: RSS 필드를 설계한다 | table |
| 42 | 실습 5: Code Snippets용 PHP를 만든다 | terminal-window + code |
| 43 | 실습 6: 관리자 화면과 카테고리 매핑 | panel |
| 44 | 실습 7: 검증 로그와 수정 루프 | terminal-window |
| 45 | WordPress 제작자가 해볼 만한 자동화 아이디어 | grid-2 panel |
| 46 | 경쟁력은 검증 가능한 위임 능력이다 | quote block |

---

## 5. 필수 키워드 (10개)

```
네이버 블로그 RSS, MCP, AGENTS.md, Skill, GitHub,
Superpowers, 검증 가능한 위임, Code Snippets, Codex, Antigravity
```

---

## 6. 작업 계획

### Phase 1: 기반 구축 ✅ 완료
- [x] frontend-slides 리포지토리 클론/설치 (`.frontend-slides/`)
- [x] SKILL.md 분석 및 아키텍처 요구사항 문서화
- [x] v4 계획서 핵심 수칙 재확인
- [x] 통합 프리뷰 생성 및 브라우저 확인

### Phase 2: deck-v5 생성
- [ ] `outputs/ai-agent-lecture/deck-v5/` 디렉토리 생성
- [ ] `deck-v4/assets/fonts/` → `deck-v5/assets/fonts/` 복사
- [ ] `deck-v5/index.html` 생성
  - viewport-base.css 전체 포함
  - SlidePresentation 클래스 포함
  - 인라인 편집 모드 포함
  - v4 디자인 룰 CSS 적용
  - 46장 슬라이드 데이터 포함
- [ ] 슬라이드 카운트 46개 검증
- [ ] 필수 키워드 10개 검증

### Phase 3: 검증
- [ ] Chrome headless로 46장 스크린샷 생성
- [ ] Contact sheet 생성
- [ ] 시각 검증 (텍스트 겹침, 오버플로우, 레이아웃)
- [ ] QA evidence JSON 생성

### Phase 4: Notion 업로드 (선택)
- [ ] deck-v5 이미지로 Notion 교안 페이지 업데이트

---

## 7. 승인 필요

- [ ] 위 계획대로 deck-v5 생성 진행 확인
- [ ] 스타일 방향: v4 Orange+Cobalt 유지 확인