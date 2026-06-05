# AI Agent 스킬 & MCP 도구 종합 디렉토리

> 조사일: 2026-06-04 | 출처: [vercel-labs](https://github.com/vercel-labs), [skills.sh](https://www.skills.sh), [awesome-mcp-korea](https://github.com/darjeeling/awesome-mcp-korea), [k-skill](https://github.com/NomaDamas/k-skill)

---

## 목차

1. [skills.sh 인기 스킬 Top 200 (설치수 기준)](#1-skillssh-인기-스킬-top-200)
2. [Vercel Labs 주요 프로젝트](#2-vercel-labs-주요-프로젝트)
3. [한국 MCP 서버 모음 (awesome-mcp-korea)](#3-한국-mcp-서버-모음)
4. [한국 생활 자동화 스킬 (k-skill)](#4-한국-생활-자동화-스킬-k-skill)
5. [분야별 분류 색인](#5-분야별-분류-색인)

---

## 1. skills.sh 인기 스킬 Top 200

> 출처: [skills.sh](https://www.skills.sh) — 설치수(Installs) 기준 상위 200개 스킬. `npx skills add <source>` 명령으로 설치 가능.

| 순위 | 스킬명 | 설명 | 출처(Repo) | 설치수 | 링크 |
|-----:|--------|------|-----------|-------:|------|
| 1 | frontend-design | 프론트엔드 UI 컴포넌트·레이아웃·반응형 디자인 모범 사례 가이드 | anthropics/skills | 498,292 | [skills.sh](https://www.skills.sh/anthropics/skills/frontend-design) |
| 2 | vercel-react-best-practices | React 모범 사례 — 컴포지션, 상태관리, 성능 최적화, 서버 컴포넌트 | vercel-labs/agent-skills | 448,929 | [skills.sh](https://www.skills.sh/vercel-labs/agent-skills/vercel-react-best-practices) |
| 3 | agent-browser | 에이전트용 브라우저 자동화 — 웹 스크래핑, 폼 제출, 스크린샷, 페이지 탐색 | vercel-labs/agent-browser | 413,870 | [skills.sh](https://www.skills.sh/vercel-labs/agent-browser/agent-browser) |
| 4 | microsoft-foundry | Azure AI Foundry — AI 모델 배포·관리·엔드포인트 구성 | microsoft/azure-skills | 367,651 | [skills.sh](https://www.skills.sh/microsoft/azure-skills/microsoft-foundry) |
| 5 | azure-ai | Azure AI 서비스 — OpenAI, Cognitive Services, 검색 통합 | microsoft/azure-skills | 365,531 | [skills.sh](https://www.skills.sh/microsoft/azure-skills/azure-ai) |
| 6 | azure-deploy | Azure 배포 자동화 — App Service, Container Apps, ARM/Bicep 템플릿 | microsoft/azure-skills | 365,172 | [skills.sh](https://www.skills.sh/microsoft/azure-skills/azure-deploy) |
| 7 | azure-diagnostics | Azure 진단 — 로그 분석, 모니터링, 트러블슈팅 | microsoft/azure-skills | 365,133 | [skills.sh](https://www.skills.sh/microsoft/azure-skills/azure-diagnostics) |
| 8 | azure-prepare | Azure 환경 준비 — 구독, 리소스그룹, 네트워크, IAM 설정 | microsoft/azure-skills | 365,088 | [skills.sh](https://www.skills.sh/microsoft/azure-skills/azure-prepare) |
| 9 | azure-storage | Azure 스토리지 — Blob, Queue, Table, CosmosDB 구성 | microsoft/azure-skills | 364,816 | [skills.sh](https://www.skills.sh/microsoft/azure-skills/azure-storage) |
| 10 | azure-validate | Azure 리소스 검증 — 설정 확인, 보안 점검, 베스트프랙티스 | microsoft/azure-skills | 364,484 | [skills.sh](https://www.skills.sh/microsoft/azure-skills/azure-validate) |
| 11 | entra-app-registration | Entra ID 앱 등록 — OAuth, SAML, API 권한 구성 | microsoft/azure-skills | 364,412 | [skills.sh](https://www.skills.sh/microsoft/azure-skills/entra-app-registration) |
| 12 | azure-resource-lookup | Azure 리소스 조회 — 리소스ID, 태그, 구독 간 검색 | microsoft/azure-skills | 364,351 | [skills.sh](https://www.skills.sh/microsoft/azure-skills/azure-resource-lookup) |
| 13 | azure-compliance | Azure 규정 준수 — 정책, 블루프린트, 규제 표준 매핑 | microsoft/azure-skills | 364,342 | [skills.sh](https://www.skills.sh/microsoft/azure-skills/azure-compliance) |
| 14 | azure-rbac | Azure RBAC — 역할 기반 접근 제어, 관리 그룹, 조건부 액세스 | microsoft/azure-skills | 364,323 | [skills.sh](https://www.skills.sh/microsoft/azure-skills/azure-rbac) |
| 15 | azure-aigateway | Azure AI Gateway — API 관리, 요금제, 캐싱, 로드밸런싱 | microsoft/azure-skills | 364,248 | [skills.sh](https://www.skills.sh/microsoft/azure-skills/azure-aigateway) |
| 16 | azure-kusto | Azure Kusto — 데이터 탐색 쿼리, Log Analytics, 그래프 분석 | microsoft/azure-skills | 364,205 | [skills.sh](https://www.skills.sh/microsoft/azure-skills/azure-kusto) |
| 17 | web-design-guidelines | 웹 디자인 가이드라인 — 타이포그래피, 색상, 간격, 접근성 원칙 | vercel-labs/agent-skills | 363,864 | [skills.sh](https://www.skills.sh/vercel-labs/agent-skills/web-design-guidelines) |
| 18 | azure-messaging | Azure 메시징 — Service Bus, Event Grid, Event Hubs 구성 | microsoft/azure-skills | 354,002 | [skills.sh](https://www.skills.sh/microsoft/azure-skills/azure-messaging) |
| 19 | remotion-best-practices | React 기반 프로그래매틱 비디오 제작 — 코드로 영상 만들기 | remotion-dev/skills | 347,472 | [skills.sh](https://www.skills.sh/remotion-dev/skills/remotion-best-practices) |
| 20 | azure-hosted-copilot-sdk | Azure Hosted Copilot SDK — 커스텀 AI 어시스턴트 구축 | microsoft/azure-skills | 337,402 | [skills.sh](https://www.skills.sh/microsoft/azure-skills/azure-hosted-copilot-sdk) |
| 21 | azure-compute | Azure 컴퓨트 — VM, App Service, Functions, Container Apps | microsoft/azure-skills | 308,205 | [skills.sh](https://www.skills.sh/microsoft/azure-skills/azure-compute) |
| 22 | azure-cloud-migrate | Azure 클라우드 마이그레이션 — 온프레미스→클라우드 전환 가이드 | microsoft/azure-skills | 298,361 | [skills.sh](https://www.skills.sh/microsoft/azure-skills/azure-cloud-migrate) |
| 23 | grill-me | 설계 리뷰 인터뷰 — 계획을 집요하게 검토하여 결함 사전 발견 | mattpocock/skills | 256,636 | [skills.sh](https://www.skills.sh/mattpocock/skills/grill-me) |
| 24 | skill-creator | 스킬 생성기 — 새로운 에이전트 스킬을 자동으로 생성하는 메타 스킬 | anthropics/skills | 250,915 | [skills.sh](https://www.skills.sh/anthropics/skills/skill-creator) |
| 25 | azure-quotas | Azure 할당량 관리 — 리소스 한도 확인, 증설 요청 | microsoft/azure-skills | 235,224 | [skills.sh](https://www.skills.sh/microsoft/azure-skills/azure-quotas) |
| 26 | azure-upgrade | Azure 업그레이드 — API 버전, 런타임, SDK 업데이트 | microsoft/azure-skills | 227,732 | [skills.sh](https://www.skills.sh/microsoft/azure-skills/azure-upgrade) |
| 27 | caveman | 초압축 커뮤니케이션 모드 — 토큰 75% 절약, 기술적 정확도 유지 | juliusbrussee/caveman | 208,523 | [skills.sh](https://www.skills.sh/juliusbrussee/caveman/caveman) |
| 28 | improve-codebase-architecture | 코드베이스 아키텍처 개선 — 결합도 감소, 테스트 가능성 향상, 도메인 언어 정리 | mattpocock/skills | 208,186 | [skills.sh](https://www.skills.sh/mattpocock/skills/improve-codebase-architecture) |
| 29 | supabase-postgres-best-practices | Supabase & Postgres 모범 사례 — 인증, RLS, 실시간, 엣지 함수 | supabase/agent-skills | 207,804 | [skills.sh](https://www.skills.sh/supabase/agent-skills/supabase-postgres-best-practices) |
| 30 | azure-cost-optimization | Azure 비용 최적화 — 예약, 스팟인스턴스, 미사용 리소스 정리 | microsoft/azure-skills | 206,880 | [skills.sh](https://www.skills.sh/microsoft/azure-skills/azure-cost-optimization) |
| 31 | azure-enterprise-infra-planner | Azure 엔터프라이즈 인프라 플래너 — 대규모 아키텍처 설계 | microsoft/azure-skills | 201,821 | [skills.sh](https://www.skills.sh/microsoft/azure-skills/azure-enterprise-infra-planner) |
| 32 | brainstorming | 브레인스토밍 — 아이디어 발굴, 마인드맵, 창발적 사고 도구 | obra/superpowers | 199,846 | [skills.sh](https://www.skills.sh/obra/superpowers/brainstorming) |
| 33 | grill-with-docs | 문서 기반 설계 리뷰 — CONTEXT.md, ADR을 활용한 집요한 검토 | mattpocock/skills | 198,715 | [skills.sh](https://www.skills.sh/mattpocock/skills/grill-with-docs) |
| 34 | ui-ux-pro-max | UI/UX 프로 맥스 가이드 — 고급 인터랙션, 애니메이션, 마이크로 인터랙션 | nextlevelbuilder/ui-ux-pro-max-skill | 198,566 | [skills.sh](https://www.skills.sh/nextlevelbuilder/ui-ux-pro-max-skill/ui-ux-pro-max) |
| 35 | vercel-composition-patterns | React 컴포지션 패턴 — 합성 vs 상속, 유연한 컴포넌트 설계 | vercel-labs/agent-skills | 198,461 | [skills.sh](https://www.skills.sh/vercel-labs/agent-skills/vercel-composition-patterns) |
| 36 | tdd | TDD 레드-그린-리팩터 루프 — 테스트 주도 개발 워크플로우 | mattpocock/skills | 197,859 | [skills.sh](https://www.skills.sh/mattpocock/skills/tdd) |
| 37 | lark-doc | Lark 문서 생성·편집 — 리치 텍스트, 협업 문서 자동 작성 | larksuite/cli | 196,199 | [skills.sh](https://www.skills.sh/larksuite/cli/lark-doc) |
| 38 | lark-base | Lark Base (데이터베이스) — 레코드 생성, 조회, 필터, 뷰 관리 | larksuite/cli | 195,764 | [skills.sh](https://www.skills.sh/larksuite/cli/lark-base) |
| 39 | lark-im | Lark 메신저 — 메시지 발송, 채팅방 관리, 봇 연동 | larksuite/cli | 195,472 | [skills.sh](https://www.skills.sh/larksuite/cli/lark-im) |
| 40 | lark-drive | Lark 드라이브 — 파일 업로드, 다운로드, 권한 관리 | larksuite/cli | 195,191 | [skills.sh](https://www.skills.sh/larksuite/cli/lark-drive) |
| 41 | lark-shared | Lark 공유 — 링크 생성, 외부 공유, 권한 설정 | larksuite/cli | 195,012 | [skills.sh](https://www.skills.sh/larksuite/cli/lark-shared) |
| 42 | lark-calendar | Lark 캘린더 — 일정 생성, 조회, 초대, 회의실 예약 | larksuite/cli | 194,848 | [skills.sh](https://www.skills.sh/larksuite/cli/lark-calendar) |
| 43 | azure-kubernetes | Azure Kubernetes (AKS) — 클러스터 배포, 스케일링, 모니터링 | microsoft/azure-skills | 194,700 | [skills.sh](https://www.skills.sh/microsoft/azure-skills/azure-kubernetes) |
| 44 | lark-wiki | Lark 위키 — 지식베이스 구축, 문서 트리 관리 | larksuite/cli | 194,660 | [skills.sh](https://www.skills.sh/larksuite/cli/lark-wiki) |
| 45 | lark-whiteboard | Lark 화이트보드 — 실시간 협업 캔버스, 다이어그램 | larksuite/cli | 194,500 | [skills.sh](https://www.skills.sh/larksuite/cli/lark-whiteboard) |
| 46 | lark-sheets | Lark 시트 — 스프레드시트 생성, 수식, 차트, 피벗 | larksuite/cli | 194,457 | [skills.sh](https://www.skills.sh/larksuite/cli/lark-sheets) |
| 47 | lark-task | Lark 태스크 — 작업 관리, 칸반보드, 진행률 추적 | larksuite/cli | 194,379 | [skills.sh](https://www.skills.sh/larksuite/cli/lark-task) |
| 48 | lark-mail | Lark 메일 — 이메일 발송, 수신함 관리, 그룹메일 | larksuite/cli | 194,367 | [skills.sh](https://www.skills.sh/larksuite/cli/lark-mail) |
| 49 | lark-vc | Lark 비디오 회의 — 회의 생성, 녹화, 참가자 관리 | larksuite/cli | 194,219 | [skills.sh](https://www.skills.sh/larksuite/cli/lark-vc) |
| 50 | lark-minutes | Lark 회의록 — AI 회의 요약, 액션아이템 자동 추출 | larksuite/cli | 194,168 | [skills.sh](https://www.skills.sh/larksuite/cli/lark-minutes) |
| 51 | lark-event | Lark 이벤트 — 이벤트 생성, RSVP, 리마인더 | larksuite/cli | 193,983 | [skills.sh](https://www.skills.sh/larksuite/cli/lark-event) |
| 52 | lark-contact | Lark 연락처 — 조직도, 외부 연락처, 그룹 관리 | larksuite/cli | 193,779 | [skills.sh](https://www.skills.sh/larksuite/cli/lark-contact) |
| 53 | lark-workflow-meeting-summary | Lark 워크플로우 회의 요약 — 자동 회의록 생성 파이프라인 | larksuite/cli | 193,670 | [skills.sh](https://www.skills.sh/larksuite/cli/lark-workflow-meeting-summary) |
| 54 | lark-openapi-explorer | Lark OpenAPI 탐색기 — API 스펙 조회, 테스트, 코드 생성 | larksuite/cli | 193,423 | [skills.sh](https://www.skills.sh/larksuite/cli/lark-openapi-explorer) |
| 55 | lark-workflow-standup-report | Lark 스탠드업 리포트 — 일일 스탠드업 자동 생성 | larksuite/cli | 193,408 | [skills.sh](https://www.skills.sh/larksuite/cli/lark-workflow-standup-report) |
| 56 | lark-skill-maker | Lark 스킬 메이커 — Lark 맞춤 스킬 자동 생성 | larksuite/cli | 193,302 | [skills.sh](https://www.skills.sh/larksuite/cli/lark-skill-maker) |
| 57 | openclaw-secure-linux-cloud | OpenClaw 보안 리눅스 클라우드 — 서버 하드닝, 방화벽, SSL | xixu-me/skills | 188,084 | [skills.sh](https://www.skills.sh/xixu-me/skills/openclaw-secure-linux-cloud) |
| 58 | secure-linux-web-hosting | 보안 리눅스 웹호스팅 — Nginx, Let's Encrypt, 자동 갱신 | xixu-me/skills | 187,848 | [skills.sh](https://www.skills.sh/xixu-me/skills/secure-linux-web-hosting) |
| 59 | skills-cli | 스킬 CLI 관리 — 설치, 업데이트, 검색, 목록 관리 | xixu-me/skills | 187,799 | [skills.sh](https://www.skills.sh/xixu-me/skills/skills-cli) |
| 60 | running-claude-code-via-litellm | LiteLLM으로 Claude Code 실행 — 다중 모델 프록시 설정 | xixu-me/skills | 187,720 | [skills.sh](https://www.skills.sh/xixu-me/skills/running-claude-code-via-litellm-copilot) |
| 61 | opensource-guide-coach | 오픈소스 가이드 코치 — 기여 가이드, PR 에티켓, 라이선스 | xixu-me/skills | 187,599 | [skills.sh](https://www.skills.sh/xixu-me/skills/opensource-guide-coach) |
| 62 | readme-i18n | README 다국어 번역 — 자동 번역, 다국어 README 생성 | xixu-me/skills | 187,313 | [skills.sh](https://www.skills.sh/xixu-me/skills/readme-i18n) |
| 63 | use-my-browser | 내 브라우저 사용 — 사용자 실제 브라우저 환경에서 에이전트 실행 | xixu-me/skills | 185,975 | [skills.sh](https://www.skills.sh/xixu-me/skills/use-my-browser) |
| 64 | tzst | TZST 압축 — 시간대 인식 압축 아카이브 도구 | xixu-me/skills | 184,655 | [skills.sh](https://www.skills.sh/xixu-me/skills/tzst) |
| 65 | video-edit | AI 비디오 편집 — 자르기, 병합, 자막, 전환 효과, 필터 | agentspace-so/runcomfy-agent-skills | 183,966 | [skills.sh](https://www.skills.sh/agentspace-so/runcomfy-agent-skills/video-edit) |
| 66 | image-to-video | 이미지→비디오 변환 — 정지 이미지를 동영상으로 애니메이션 | agentspace-so/runcomfy-agent-skills | 183,691 | [skills.sh](https://www.skills.sh/agentspace-so/runcomfy-agent-skills/image-to-video) |
| 67 | image-edit | AI 이미지 편집 — 리터칭, 배경제거, 색보정, 합성 | agentspace-so/runcomfy-agent-skills | 183,533 | [skills.sh](https://www.skills.sh/agentspace-so/runcomfy-agent-skills/image-edit) |
| 68 | nano-banana-2 | Nano Banana 2 — 경량 이미지 생성 모델, 빠른 인퍼런스 | agentspace-so/runcomfy-agent-skills | 183,307 | [skills.sh](https://www.skills.sh/agentspace-so/runcomfy-agent-skills/nano-banana-2) |
| 69 | sleek-design-mobile-apps | 모바일 앱 슬릭 디자인 — iOS/Android 네이티브 느낌 UI | sleekdotdesign/agent-skills | 182,292 | [skills.sh](https://www.skills.sh/sleekdotdesign/agent-skills/sleek-design-mobile-apps) |
| 70 | to-prd | PRD 작성 — 대화 컨텍스트를 제품 요구사항 문서로 변환 | mattpocock/skills | 179,496 | [skills.sh](https://www.skills.sh/mattpocock/skills/to-prd) |
| 71 | gpt-image-2 | GPT Image 2 — 고품질 AI 이미지 생성, 프롬프트 엔지니어링 | agentspace-so/agent-skills | 175,476 | [skills.sh](https://www.skills.sh/agentspace-so/agent-skills/gpt-image-2) |
| 72 | to-issues | 이슈 분해 — PRD/계획을 독립적인 GitHub 이슈로 분할 | mattpocock/skills | 172,216 | [skills.sh](https://www.skills.sh/mattpocock/skills/to-issues) |
| 73 | diagnose | 버그 진단 루프 — 재현→최소화→가설→계측→수정→회귀테스트 | mattpocock/skills | 171,677 | [skills.sh](https://www.skills.sh/mattpocock/skills/diagnose) |
| 74 | azure-cost | Azure 비용 분석 — 리소스별 비용, 예산 알림, 최적화 추천 | microsoft/azure-skills | 170,250 | [skills.sh](https://www.skills.sh/microsoft/azure-skills/azure-cost) |
| 75 | lark-approval | Lark 결재 — 결재선 생성, 승인/반려, 결재 이력 | larksuite/cli | 167,585 | [skills.sh](https://www.skills.sh/larksuite/cli/lark-approval) |
| 76 | write-a-skill | 스킬 작성법 — 구조, 프로그레시브 디스클로저, 리소스 번들링 | mattpocock/skills | 166,345 | [skills.sh](https://www.skills.sh/mattpocock/skills/write-a-skill) |
| 77 | zoom-out | 줌아웃 — 코드의 큰 그림 보기, 전체 아키텍처 이해 | mattpocock/skills | 165,927 | [skills.sh](https://www.skills.sh/mattpocock/skills/zoom-out) |
| 78 | setup-matt-pocock-skills | Matt Pocock 스킬 초기 설정 — 이슈 트래커, 트리아지, 도메인 연결 | mattpocock/skills | 164,724 | [skills.sh](https://www.skills.sh/mattpocock/skills/setup-matt-pocock-skills) |
| 79 | lark-approval (feishu) | 비자루 결재 — 결재 워크플로우 생성, 승인/반려 | open.feishu.cn | 162,711 | [skills.sh](https://www.skills.sh/open.feishu.cn/lark-approval) |
| 80 | lark-doc (feishu) | 비자루 문서 — 리치 텍스트 문서 생성·편집 | open.feishu.cn | 162,568 | [skills.sh](https://www.skills.sh/open.feishu.cn/lark-doc) |
| 81 | lark-base (feishu) | 비자루 Base — 데이터베이스 레코드 관리 | open.feishu.cn | 162,545 | [skills.sh](https://www.skills.sh/open.feishu.cn/lark-base) |
| 82 | lark-calendar (feishu) | 비자루 캘린더 — 일정 생성·조회 | open.feishu.cn | 162,530 | [skills.sh](https://www.skills.sh/open.feishu.cn/lark-calendar) |
| 83 | lark-contact (feishu) | 비자루 연락처 — 조직도, 외부 연락처 관리 | open.feishu.cn | 162,508 | [skills.sh](https://www.skills.sh/open.feishu.cn/lark-contact) |
| 84 | lark-drive (feishu) | 비자루 드라이브 — 파일 관리, 권한 설정 | open.feishu.cn | 162,507 | [skills.sh](https://www.skills.sh/open.feishu.cn/lark-drive) |
| 85 | lark-attendance (feishu) | 비자루 근태 — 출퇴근, 휴가, 근태 기록 | open.feishu.cn | 162,504 | [skills.sh](https://www.skills.sh/open.feishu.cn/lark-attendance) |
| 86 | lark-im (feishu) | 비자루 메신저 — 메시지 발송, 채팅방 관리 | open.feishu.cn | 162,498 | [skills.sh](https://www.skills.sh/open.feishu.cn/lark-im) |
| 87 | lark-shared (feishu) | 비자루 공유 — 링크 생성, 외부 공유 | open.feishu.cn | 162,496 | [skills.sh](https://www.skills.sh/open.feishu.cn/lark-shared) |
| 88 | lark-mail (feishu) | 비자루 메일 — 이메일 발송, 수신함 관리 | open.feishu.cn | 162,459 | [skills.sh](https://www.skills.sh/open.feishu.cn/lark-mail) |
| 89 | lark-event (feishu) | 비자루 이벤트 — 이벤트 생성, RSVP | open.feishu.cn | 162,454 | [skills.sh](https://www.skills.sh/open.feishu.cn/lark-event) |
| 90 | lark-minutes (feishu) | 비자루 회의록 — AI 회의 요약 | open.feishu.cn | 162,441 | [skills.sh](https://www.skills.sh/open.feishu.cn/lark-minutes) |
| 91 | lark-sheets (feishu) | 비자루 시트 — 스프레드시트 관리 | open.feishu.cn | 162,433 | [skills.sh](https://www.skills.sh/open.feishu.cn/lark-sheets) |
| 92 | lark-openapi-explorer (feishu) | 비자루 OpenAPI 탐색기 — API 스펙 조회 | open.feishu.cn | 162,427 | [skills.sh](https://www.skills.sh/open.feishu.cn/lark-openapi-explorer) |
| 93 | lark-task (feishu) | 비자루 태스크 — 작업 관리, 칸반보드 | open.feishu.cn | 162,410 | [skills.sh](https://www.skills.sh/open.feishu.cn/lark-task) |
| 94 | lark-wiki (feishu) | 비자루 위키 — 지식베이스 관리 | open.feishu.cn | 162,404 | [skills.sh](https://www.skills.sh/open.feishu.cn/lark-wiki) |
| 95 | lark-skill-maker (feishu) | 비자루 스킬 메이커 — 커스텀 스킬 생성 | open.feishu.cn | 162,391 | [skills.sh](https://www.skills.sh/open.feishu.cn/lark-skill-maker) |
| 96 | lark-slides (feishu) | 비자루 슬라이드 — 프레젠테이션 생성·편집 | open.feishu.cn | 162,390 | [skills.sh](https://www.skills.sh/open.feishu.cn/lark-slides) |
| 97 | lark-whiteboard (feishu) | 비자루 화이트보드 — 실시간 협업 캔버스 | open.feishu.cn | 162,388 | [skills.sh](https://www.skills.sh/open.feishu.cn/lark-whiteboard) |
| 98 | lark-vc (feishu) | 비자루 비디오 회의 — 회의 생성, 녹화 | open.feishu.cn | 162,371 | [skills.sh](https://www.skills.sh/open.feishu.cn/lark-vc) |
| 99 | lark-standup-report (feishu) | 비자루 스탠드업 리포트 — 일일 스탠드업 자동 생성 | open.feishu.cn | 162,356 | [skills.sh](https://www.skills.sh/open.feishu.cn/lark-workflow-standup-report) |
| 100 | lark-meeting-summary (feishu) | 비자루 회의 요약 — 자동 회의록 파이프라인 | open.feishu.cn | 162,338 | [skills.sh](https://www.skills.sh/open.feishu.cn/lark-workflow-meeting-summary) |
| 101 | caveman (mattpocock) | 초압축 모드 (MattPocock판) — 토큰 절약 커뮤니케이션 | mattpocock/skills | 161,232 | [skills.sh](https://www.skills.sh/mattpocock/skills/caveman) |
| 102 | kling-3-0 | Kling 3.0 — 최신 AI 비디오 생성 모델, 고품질 영상 | agentspace-so/runcomfy-agent-skills | 159,486 | [skills.sh](https://www.skills.sh/agentspace-so/runcomfy-agent-skills/kling-3-0) |
| 103 | triage | 이슈 트리아지 — 상태기반 워크플로우로 이슈 관리 | mattpocock/skills | 153,269 | [skills.sh](https://www.skills.sh/mattpocock/skills/triage) |
| 104 | codex-pet | Codex Pet — AI 펫 캐릭터 생성, 애니메이션 | agentspace-so/runcomfy-agent-skills | 148,083 | [skills.sh](https://www.skills.sh/agentspace-so/runcomfy-agent-skills/codex-pet) |
| 105 | lark-slides | Lark 슬라이드 — 프레젠테이션 생성·편집 | larksuite/cli | 147,380 | [skills.sh](https://www.skills.sh/larksuite/cli/lark-slides) |
| 106 | lark-attendance | Lark 근태 — 출퇴근, 휴가, 근태 기록 관리 | larksuite/cli | 146,514 | [skills.sh](https://www.skills.sh/larksuite/cli/lark-attendance) |
| 107 | impeccable | SVG 애니메이션 & 디자인 도구 — 감사, 비평, 최적화 | pbakaus/impeccable | 145,722 | [skills.sh](https://www.skills.sh/pbakaus/impeccable/impeccable) |
| 108 | lark-okr (feishu) | 비자루 OKR — 목표·핵심결과 관리 | open.feishu.cn | 143,342 | [skills.sh](https://www.skills.sh/open.feishu.cn/lark-okr) |
| 109 | develop-userscripts | 유저스크립트 개발 — Tampermonkey/Greasemonkey 스크립트 작성 | xixu-me/skills | 141,745 | [skills.sh](https://www.skills.sh/xixu-me/skills/develop-userscripts) |
| 110 | paper-context-resolver | AI 논문 컨텍스트 해석 — 논문 배경지식 자동 추출 | lllllllama/ai-paper-reproduction-skill | 139,967 | [skills.sh](https://www.skills.sh/lllllllama/ai-paper-reproduction-skill/paper-context-resolver) |
| 111 | repo-intake-and-plan | 저장소 인테이크 — 코드 분석, 구조 파악, 계획 수립 | lllllllama/ai-paper-reproduction-skill | 139,634 | [skills.sh](https://www.skills.sh/lllllllama/ai-paper-reproduction-skill/repo-intake-and-plan) |
| 112 | env-and-assets-bootstrap | 환경 & 자산 부트스트랩 — 프로젝트 초기 설정 자동화 | lllllllama/ai-paper-reproduction-skill | 139,613 | [skills.sh](https://www.skills.sh/lllllllama/ai-paper-reproduction-skill/env-and-assets-bootstrap) |
| 113 | minimal-run-and-audit | 최소 실행 & 감사 — 코드 실행, 결과 검증 | lllllllama/ai-paper-reproduction-skill | 139,606 | [skills.sh](https://www.skills.sh/lllllllama/ai-paper-reproduction-skill/minimal-run-and-audit) |
| 114 | vercel-react-native-skills | React Native 모범 사례 — 네이티브 모듈, 네비게이션, 성능 | vercel-labs/agent-skills | 133,536 | [skills.sh](https://www.skills.sh/vercel-labs/agent-skills/vercel-react-native-skills) |
| 115 | pptx | PowerPoint 자동 생성 — 슬라이드, 차트, 이미지, 템플릿 | anthropics/skills | 132,944 | [skills.sh](https://www.skills.sh/anthropics/skills/pptx) |
| 116 | lark-okr | Lark OKR — 목표·핵심결과 설정, 진행률 추적 | larksuite/cli | 131,887 | [skills.sh](https://www.skills.sh/larksuite/cli/lark-okr) |
| 117 | caveman-commit | 초압축 커밋 메시지 — 간결한 Git 커밋 작성 | juliusbrussee/caveman | 128,925 | [skills.sh](https://www.skills.sh/juliusbrussee/caveman/caveman-commit) |
| 118 | lark-markdown (feishu) | 비자루 마크다운 — 마크다운 문서 Lark 동기화 | open.feishu.cn | 128,077 | [skills.sh](https://www.skills.sh/open.feishu.cn/lark-markdown) |
| 119 | caveman-review | 초압축 코드 리뷰 — 간결한 리뷰 코멘트 작성 | juliusbrussee/caveman | 127,656 | [skills.sh](https://www.skills.sh/juliusbrussee/caveman/caveman-review) |
| 120 | ai-image-generation | AI 이미지 생성 — 텍스트→이미지, 다양한 스타일 | agentspace-so/runcomfy-agent-skills | 127,473 | [skills.sh](https://www.skills.sh/agentspace-so/runcomfy-agent-skills/ai-image-generation) |
| 121 | seo-audit | SEO 감사 — 메타 태그, 구조화 데이터, 페이지 속도, 키워드 분석 | coreyhaines31/marketingskills | 127,424 | [skills.sh](https://www.skills.sh/coreyhaines31/marketingskills/seo-audit) |
| 122 | ai-video-generation | AI 비디오 생성 — 텍스트/이미지→동영상, 다양한 모델 | agentspace-so/runcomfy-agent-skills | 127,358 | [skills.sh](https://www.skills.sh/agentspace-so/runcomfy-agent-skills/ai-video-generation) |
| 123 | runcomfy-cli | RunComfy CLI — ComfyUI 워크플로우 실행, 이미지/비디오 생성 | agentspace-so/runcomfy-agent-skills | 126,929 | [skills.sh](https://www.skills.sh/agentspace-so/runcomfy-agent-skills/runcomfy-cli) |
| 124 | systematic-debugging | 체계적 디버깅 — 재현, 격리, 가설검증, 수정, 회귀테스트 | obra/superpowers | 126,725 | [skills.sh](https://www.skills.sh/obra/superpowers/systematic-debugging) |
| 125 | face-swap | 페이스 스왑 — 얼굴 교체, AI 아바타 제작 | agentspace-so/runcomfy-agent-skills | 126,580 | [skills.sh](https://www.skills.sh/agentspace-so/runcomfy-agent-skills/face-swap) |
| 126 | ai-avatar-video | AI 아바타 비디오 — 가상 인물 영상 생성 | agentspace-so/runcomfy-agent-skills | 126,527 | [skills.sh](https://www.skills.sh/agentspace-so/runcomfy-agent-skills/ai-avatar-video) |
| 127 | caveman-compress | 초압축 — 모든 출력을 극한으로 압축 | juliusbrussee/caveman | 126,044 | [skills.sh](https://www.skills.sh/juliusbrussee/caveman/caveman-compress) |
| 128 | prototype | 프로토타이핑 — 상태/비즈니스 로직 또는 UI 변형 탐색 | mattpocock/skills | 125,835 | [skills.sh](https://www.skills.sh/mattpocock/skills/prototype) |
| 129 | image-inpainting | 이미지 인페인팅 — 이미지 내 영역 복원·수정 | agentspace-so/runcomfy-agent-skills | 124,516 | [skills.sh](https://www.skills.sh/agentspace-so/runcomfy-agent-skills/image-inpainting) |
| 130 | video-inpainting | 비디오 인페인팅 — 영상 내 영역 복원·제거 | agentspace-so/runcomfy-agent-skills | 124,302 | [skills.sh](https://www.skills.sh/agentspace-so/runcomfy-agent-skills/video-inpainting) |
| 131 | controlnet-pose | ControlNet 포즈 제어 — 자세 기반 이미지 생성 | agentspace-so/runcomfy-agent-skills | 124,206 | [skills.sh](https://www.skills.sh/agentspace-so/runcomfy-agent-skills/controlnet-pose) |
| 132 | lipsync | 립싱크 — 입모양 동기화, 음성-영상 합성 | agentspace-so/runcomfy-agent-skills | 124,189 | [skills.sh](https://www.skills.sh/agentspace-so/runcomfy-agent-skills/lipsync) |
| 133 | video-extend | 비디오 확장 — 영상 길이 연장, 자연스러운 연결 | agentspace-so/runcomfy-agent-skills | 124,025 | [skills.sh](https://www.skills.sh/agentspace-so/runcomfy-agent-skills/video-extend) |
| 134 | image-outpainting | 이미지 아웃페인팅 — 이미지 외곽 확장 | agentspace-so/runcomfy-agent-skills | 123,756 | [skills.sh](https://www.skills.sh/agentspace-so/runcomfy-agent-skills/image-outpainting) |
| 135 | relight | 릴라이트 — 이미지 조명 재설정, 그림자 조정 | agentspace-so/runcomfy-agent-skills | 123,671 | [skills.sh](https://www.skills.sh/agentspace-so/runcomfy-agent-skills/relight) |
| 136 | video-outpainting | 비디오 아웃페인팅 — 영상 외곽 확장 | agentspace-so/runcomfy-agent-skills | 123,549 | [skills.sh](https://www.skills.sh/agentspace-so/runcomfy-agent-skills/video-outpainting) |
| 137 | elevenlabs-music-generation | ElevenLabs 음악 생성 — AI 배경음악, BGM, 효과음 | agentspace-so/runcomfy-agent-skills | 123,407 | [skills.sh](https://www.skills.sh/agentspace-so/runcomfy-agent-skills/elevenlabs-music-generation) |
| 138 | just-scrape | AI 웹 스크래핑 — 웹페이지 데이터 자동 추출, 구조화 | scrapegraphai/just-scrape | 121,847 | [skills.sh](https://www.skills.sh/scrapegraphai/just-scrape/just-scrape) |
| 139 | caveman-help | 초압축 도움말 — caveman 명령어 사용법 | juliusbrussee/caveman | 121,662 | [skills.sh](https://www.skills.sh/juliusbrussee/caveman/caveman-help) |
| 140 | extract-design-system | 디자인 시스템 추출 — 기존 코드에서 토큰, 컴포넌트 추출 | arvindrk/extract-design-system | 121,502 | [skills.sh](https://www.skills.sh/arvindrk/extract-design-system/extract-design-system) |
| 141 | handoff | 핸드오프 — 작업 인계 문서 자동 생성 | mattpocock/skills | 119,895 | [skills.sh](https://www.skills.sh/mattpocock/skills/handoff) |
| 142 | copywriting | 마케팅 카피라이팅 — 헤드라인, CTA, 이메일, 랜딩페이지 카피 | coreyhaines31/marketingskills | 117,023 | [skills.sh](https://www.skills.sh/coreyhaines31/marketingskills/copywriting) |
| 143 | ace-step | Ace Step — AI 음악 생성, 스텝 시퀀서 | agentspace-so/runcomfy-agent-skills | 115,471 | [skills.sh](https://www.skills.sh/agentspace-so/runcomfy-agent-skills/ace-step) |
| 144 | airunway-aks-setup | AKS 설정 — Azure Kubernetes Service 클러스터 구성 | microsoft/azure-skills | 115,372 | [skills.sh](https://www.skills.sh/microsoft/azure-skills/airunway-aks-setup) |
| 145 | ai-music | AI 음악 생성 — 텍스트→음악, 장르/분위기 지정 | agentspace-so/runcomfy-agent-skills | 115,086 | [skills.sh](https://www.skills.sh/agentspace-so/runcomfy-agent-skills/ai-music) |
| 146 | docx | Word 문서 자동 생성 — 표, 차트, 스타일, 템플릿 | anthropics/skills | 113,995 | [skills.sh](https://www.skills.sh/anthropics/skills/docx) |
| 147 | requesting-code-review | 코드 리뷰 요청 — 효과적인 리뷰 요청 작성법 | obra/superpowers | 112,991 | [skills.sh](https://www.skills.sh/obra/superpowers/requesting-code-review) |
| 148 | test-driven-development | TDD 패턴 — 테스트 주도 개발 워크플로우 (obra판) | obra/superpowers | 111,419 | [skills.sh](https://www.skills.sh/obra/superpowers/test-driven-development) |
| 149 | lark-markdown | Lark 마크다운 — 마크다운 문서 Lark 동기화 | larksuite/cli | 110,272 | [skills.sh](https://www.skills.sh/larksuite/cli/lark-markdown) |
| 150 | lark-vc-agent (feishu) | 비자루 VC 에이전트 — AI 회의 어시스턴트 | open.feishu.cn | 107,322 | [skills.sh](https://www.skills.sh/open.feishu.cn/lark-vc-agent) |
| 151 | design-taste-frontend | 프론트엔드 디자인 테스트 — UI 퀄리티 평가, 개선 제안 | leonxlnx/taste-skill | 106,635 | [skills.sh](https://www.skills.sh/leonxlnx/taste-skill/design-taste-frontend) |
| 152 | supabase | Supabase 통합 — 인증, DB, 스토리지, 실시간, 함수 | supabase/agent-skills | 103,670 | [skills.sh](https://www.skills.sh/supabase/agent-skills/supabase) |
| 153 | xlsx | Excel 스프레드시트 자동 생성 — 표, 차트, 수식, 피벗 | anthropics/skills | 100,975 | [skills.sh](https://www.skills.sh/anthropics/skills/xlsx) |
| 154 | next-best-practices | Next.js 모범 사례 — App Router, SSR, 캐싱, 라우팅 | vercel-labs/next-skills | 99,170 | [skills.sh](https://www.skills.sh/vercel-labs/next-skills/next-best-practices) |
| 155 | azure-observability | Azure 관측가능성 — 모니터링, 메트릭, 트레이싱, 로깅 | microsoft/azure-skills | 98,141 | [skills.sh](https://www.skills.sh/microsoft/azure-skills/azure-observability) |
| 156 | subagent-driven-development | 서브에이전트 주도 개발 — 병렬 작업 분산, 집중 탐색 | obra/superpowers | 96,376 | [skills.sh](https://www.skills.sh/obra/superpowers/subagent-driven-development) |
| 157 | receiving-code-review | 코드 리뷰 수용 — 피드백 반영, 개선 커밋 작성 | obra/superpowers | 90,749 | [skills.sh](https://www.skills.sh/obra/superpowers/receiving-code-review) |
| 158 | high-end-visual-design | 하이엔드 비주얼 디자인 — 프리미엄 UI/UX 가이드 | leonxlnx/taste-skill | 90,174 | [skills.sh](https://www.skills.sh/leonxlnx/taste-skill/high-end-visual-design) |
| 159 | gpt-image-2 (runcomfy) | GPT Image 2 (RunComfy판) — 고품질 이미지 생성 | runcomfy-com/skills | 88,385 | [skills.sh](https://www.skills.sh/runcomfy-com/skills/gpt-image-2) |
| 160 | redesign-existing-projects | 기존 프로젝트 리디자인 — UI/UX 개선, 모던화 | leonxlnx/taste-skill | 88,289 | [skills.sh](https://www.skills.sh/leonxlnx/taste-skill/redesign-existing-projects) |
| 161 | image-outpainting (doany) | 이미지 아웃페인팅 (DoAny판) — 이미지 외곽 확장 | doany-ai/skills | 88,243 | [skills.sh](https://www.skills.sh/doany-ai/skills/image-outpainting) |
| 162 | video-edit (runcomfy) | 비디오 편집 (RunComfy판) — 자르기, 병합, 자막 | runcomfy-com/skills | 88,225 | [skills.sh](https://www.skills.sh/runcomfy-com/skills/video-edit) |
| 163 | image-to-video (runcomfy) | 이미지→비디오 (RunComfy판) — 정지화상→동영상 | runcomfy-com/skills | 88,138 | [skills.sh](https://www.skills.sh/runcomfy-com/skills/image-to-video) |
| 164 | gpt-image-2 (doany) | GPT Image 2 (DoAny판) — 이미지 생성 | doany-ai/skills | 88,121 | [skills.sh](https://www.skills.sh/doany-ai/skills/gpt-image-2) |
| 165 | ai-avatar-video (doany) | AI 아바타 비디오 (DoAny판) — 가상 인물 영상 | doany-ai/skills | 88,095 | [skills.sh](https://www.skills.sh/doany-ai/skills/ai-avatar-video) |
| 166 | ai-image-generation (runcomfy) | AI 이미지 생성 (RunComfy판) | runcomfy-com/skills | 88,083 | [skills.sh](https://www.skills.sh/runcomfy-com/skills/ai-image-generation) |
| 167 | video-edit (doany) | 비디오 편집 (DoAny판) — 자르기, 병합 | doany-ai/skills | 88,081 | [skills.sh](https://www.skills.sh/doany-ai/skills/video-edit) |
| 168 | ai-image-generation (doany) | AI 이미지 생성 (DoAny판) | doany-ai/skills | 88,034 | [skills.sh](https://www.skills.sh/doany-ai/skills/ai-image-generation) |
| 169 | image-to-video (doany) | 이미지→비디오 (DoAny판) | doany-ai/skills | 88,032 | [skills.sh](https://www.skills.sh/doany-ai/skills/image-to-video) |
| 170 | ai-video-generation (runcomfy) | AI 비디오 생성 (RunComfy판) | runcomfy-com/skills | 88,014 | [skills.sh](https://www.skills.sh/runcomfy-com/skills/ai-video-generation) |
| 171 | ai-video-generation (doany) | AI 비디오 생성 (DoAny판) | doany-ai/skills | 87,979 | [skills.sh](https://www.skills.sh/doany-ai/skills/ai-video-generation) |
| 172 | image-edit (runcomfy) | 이미지 편집 (RunComfy판) — 리터칭, 배경제거 | runcomfy-com/skills | 87,898 | [skills.sh](https://www.skills.sh/runcomfy-com/skills/image-edit) |
| 173 | image-edit (doany) | 이미지 편집 (DoAny판) | doany-ai/skills | 87,893 | [skills.sh](https://www.skills.sh/doany-ai/skills/image-edit) |
| 174 | nano-banana-2 (runcomfy) | Nano Banana 2 (RunComfy판) — 경량 이미지 생성 | runcomfy-com/skills | 87,861 | [skills.sh](https://www.skills.sh/runcomfy-com/skills/nano-banana-2) |
| 175 | image-outpainting (runcomfy) | 이미지 아웃페인팅 (RunComfy판) | runcomfy-com/skills | 87,839 | [skills.sh](https://www.skills.sh/runcomfy-com/skills/image-outpainting) |
| 176 | ai-avatar-video (runcomfy) | AI 아바타 비디오 (RunComfy판) | runcomfy-com/skills | 87,770 | [skills.sh](https://www.skills.sh/runcomfy-com/skills/ai-avatar-video) |
| 177 | webapp-testing | 웹앱 테스트 — E2E, 유닛, 비주얼 회귀 테스트 | anthropics/skills | 87,701 | [skills.sh](https://www.skills.sh/anthropics/skills/webapp-testing) |
| 178 | marketing-psychology | 소비자 심리학 마케팅 — 설득, 신뢰, 행동 유도 패턴 | coreyhaines31/marketingskills | 86,336 | [skills.sh](https://www.skills.sh/coreyhaines31/marketingskills/marketing-psychology) |
| 179 | finishing-a-development-branch | 개발 브랜치 마무리 — 리베이스, 스쿼시, PR 작성 | obra/superpowers | 85,954 | [skills.sh](https://www.skills.sh/obra/superpowers/finishing-a-development-branch) |
| 180 | critique | 디자인 비평 — UI/UX 작품 집중 분석, 개선 제안 | pbakaus/impeccable | 83,322 | [skills.sh](https://www.skills.sh/pbakaus/impeccable/critique) |
| 181 | lark-vc-agent | Lark VC 에이전트 — AI 회의 어시스턴트 | larksuite/cli | 83,079 | [skills.sh](https://www.skills.sh/larksuite/cli/lark-vc-agent) |
| 182 | minimalist-ui | 미니멀 UI — 심플하고 깔끔한 인터페이스 디자인 | leonxlnx/taste-skill | 82,644 | [skills.sh](https://www.skills.sh/leonxlnx/taste-skill/minimalist-ui) |
| 183 | hyperframes | 인터랙티브 애니메이션 프레임 — 웹사이트→애니메이션 | heygen-com/hyperframes | 82,563 | [skills.sh](https://www.skills.sh/heygen-com/hyperframes/hyperframes) |
| 184 | audit | SVG 감사 — 벡터 그래픽 최적화, 접근성 점검 | pbakaus/impeccable | 82,530 | [skills.sh](https://www.skills.sh/pbakaus/impeccable/audit) |
| 185 | animate | SVG 애니메이션 — 벡터 그래픽 모션, 인터랙션 | pbakaus/impeccable | 82,409 | [skills.sh](https://www.skills.sh/pbakaus/impeccable/animate) |
| 186 | content-strategy | 콘텐츠 전략 — 콘텐츠 기획, 캘린더, 퍼널 설계 | coreyhaines31/marketingskills | 81,821 | [skills.sh](https://www.skills.sh/coreyhaines31/marketingskills/content-strategy) |
| 187 | optimize | SVG 최적화 — 파일 크기, 렌더링 성능 개선 | pbakaus/impeccable | 81,736 | [skills.sh](https://www.skills.sh/pbakaus/impeccable/optimize) |
| 188 | distill | SVG 증류 — 불필요한 요소 제거, 핵심만 남기기 | pbakaus/impeccable | 80,127 | [skills.sh](https://www.skills.sh/pbakaus/impeccable/distill) |
| 189 | full-output-enforcement | 전체 출력 강제 — AI가 생략 없이 완전한 코드 출력 | leonxlnx/taste-skill | 79,763 | [skills.sh](https://www.skills.sh/leonxlnx/taste-skill/full-output-enforcement) |
| 190 | cavecrew | CaveCrew — 팀용 초압축 모드, 다중 에이전트 협업 | juliusbrussee/caveman | 79,667 | [skills.sh](https://www.skills.sh/juliusbrussee/caveman/cavecrew) |
| 191 | caveman-stats | 초압축 통계 — 토큰 절약량, 사용량 분석 | juliusbrussee/caveman | 79,562 | [skills.sh](https://www.skills.sh/juliusbrussee/caveman/caveman-stats) |
| 192 | programmatic-seo | 프로그래매틱 SEO — 대량 페이지 자동 생성, 템플릿 SEO | coreyhaines31/marketingskills | 79,254 | [skills.sh](https://www.skills.sh/coreyhaines31/marketingskills/programmatic-seo) |
| 193 | hyperframes-cli | Hyperframes CLI — 명령줄에서 애니메이션 프레임 생성 | heygen-com/hyperframes | 78,272 | [skills.sh](https://www.skills.sh/heygen-com/hyperframes/hyperframes-cli) |
| 194 | gsap | GSAP 애니메이션 — 고성능 웹 애니메이션 라이브러리 | heygen-com/hyperframes | 78,005 | [skills.sh](https://www.skills.sh/heygen-com/hyperframes/gsap) |
| 195 | industrial-brutalist-ui | 인더스트리얼 브루탈리스트 UI — 강렬한 디자인 스타일 | leonxlnx/taste-skill | 76,835 | [skills.sh](https://www.skills.sh/leonxlnx/taste-skill/industrial-brutalist-ui) |
| 196 | marketing-ideas | 마케팅 아이디어 — 창의적 캠페인, 프로모션 기획 | coreyhaines31/marketingskills | 76,800 | [skills.sh](https://www.skills.sh/coreyhaines31/marketingskills/marketing-ideas) |
| 197 | stitch-design-taste | 스티치 디자인 테이스트 — Vercel v0 스타일 UI 생성 | leonxlnx/taste-skill | 76,459 | [skills.sh](https://www.skills.sh/leonxlnx/taste-skill/stitch-design-taste) |
| 198 | website-to-hyperframes | 웹사이트→하이퍼프레임 — 기존 사이트를 애니메이션으로 | heygen-com/hyperframes | 75,686 | [skills.sh](https://www.skills.sh/heygen-com/hyperframes/website-to-hyperframes) |
| 199 | hyperframes-registry | 하이퍼프레임 레지스트리 — 애니메이션 템플릿 카탈로그 | heygen-com/hyperframes | 75,598 | [skills.sh](https://www.skills.sh/heygen-com/hyperframes/hyperframes-registry) |
| 200 | firebase-basics | Firebase 기본 — 인증, Firestore, 스토리지, 호스팅 | firebase/agent-skills | 72,393 | [skills.sh](https://www.skills.sh/firebase/agent-skills/firebase-basics) |

---

## 2. Vercel Labs 주요 프로젝트

> 출처: [github.com/vercel-labs](https://github.com/vercel-labs) — Vercel 실험실의 주요 오픈소스 프로젝트. ⭐ 스타 수 기준 정렬.

### 🛠️ Agent & AI 도구

| 프로젝트 | 설명 | ⭐ | 링크 |
|---------|------|---:|------|
| **agent-skills** | Vercel 공식 에이전트 스킬 컬렉션 — React, Next.js, 웹디자인, 배포 등 | 27,538 | [GitHub](https://github.com/vercel-labs/agent-skills) |
| **skills** | 오픈 에이전트 스킬 생태계 CLI (`npx skills`) — 70+ 코딩 에이전트 지원 | 12,035 | [GitHub](https://github.com/vercel-labs/skills) |
| **open-agents** | 클라우드 에이전트 구축용 오픈소스 템플릿 | 5,590 | [GitHub](https://github.com/vercel-labs/open-agents) |
| **zerolang** | 에이전트를 위한 프로그래밍 언어 | 4,841 | [GitHub](https://github.com/vercel-labs/zerolang) |
| **just-bash** | 에이전트를 위한 Bash 도구 | 3,648 | [GitHub](https://github.com/vercel-labs/just-bash) |
| **agent-browser** | 에이전트용 브라우저 자동화 — 스크래핑, 폼 제출, 스크린샷 | 1,517 | [GitHub](https://github.com/vercel-labs/agent-browser) |

### 🔧 주요 agent-skills 하위 스킬

| 스킬명 | 설명 | 설치수 |
|--------|------|-------:|
| vercel-react-best-practices | React 모범 사례 — 컴포지션, 상태관리, 성능 최적화, 서버 컴포넌트 | 448,929 |
| web-design-guidelines | 웹 디자인 가이드라인 — 타이포그래피, 색상, 간격, 접근성 | 363,864 |
| vercel-composition-patterns | React 컴포지션 패턴 — 합성 vs 상속, 유연한 컴포넌트 설계 | 198,461 |
| vercel-react-native-skills | React Native 모범 사례 — 네이티브 모듈, 네비게이션, 성능 | 133,536 |
| deploy-to-vercel | Vercel 배포 자동화 — 프리뷰, 프로덕션, 에지 함수 | 64,124 |
| vercel-react-view-transitions | View Transitions API 가이드 — 페이지 전환 애니메이션 | 48,873 |
| vercel-cli-with-tokens | Vercel CLI 토큰 사용법 — 인증, 배포, 관리 | 44,678 |

### 🔧 주요 next-skills 하위 스킬

| 스킬명 | 설명 | 설치수 |
|--------|------|-------:|
| next-best-practices | Next.js 모범 사례 — App Router, SSR, 캐싱, 라우팅 | 99,170 |
| next-cache-components | 캐시 컴포넌트 활용법 — 서버 컴포넌트 캐싱 전략 | 33,207 |
| next-upgrade | Next.js 업그레이드 가이드 — 마이그레이션, 브레이킹 체인지 | 22,425 |

---

## 3. 한국 MCP 서버 모음

> 출처: [darjeeling/awesome-mcp-korea](https://github.com/darjeeling/awesome-mcp-korea) — 한국 생태계 중심 MCP 서버 큐레이션.

### 📜 법률 & 정부 (8개)

| 이름 | 설명 | 링크 |
|------|------|------|
| korean-law-mcp | 국가법령정보 API 기반 법령·판례·행정규칙 검색·조회·분석 | [GitHub](https://github.com/chrisryugj/korean-law-mcp) |
| mcp-kr-legislation | 법제처 OPEN API 통합 법령·판례·자치법규·조약 조회 | [GitHub](https://github.com/ChangooLee/mcp-kr-legislation) |
| assembly-api-mcp | 국회 Open API — 의원·의안·회의록·청원·예산 조회 | [GitHub](https://github.com/hollobit/assembly-api-mcp) |
| law-mcp | open.law.go.kr API로 한국 법령 데이터 조회 | [GitHub](https://github.com/finalchild/law-mcp) |
| lawtutor-mcp | 7급 공무원시험 행정법·헌법 학습용 RAG 검색 | [GitHub](https://github.com/seung23/lawtutor-mcp) |
| LexLink-ko-mcp | 법령·판례 + 시맨틱 검색(aiSearch) | [GitHub](https://github.com/rabqatab/LexLink-ko-mcp) |
| korean-law-alio-mcp | 법령정보센터 + ALIO 공공기관 내부규정 검색·비교·분석 | [GitHub](https://github.com/workbookbulb863/korean-law-alio-mcp) |
| korean-administrative-rule-mcp | 행정규칙·판례·헌재결정례 통합 검색 | [GitHub](https://github.com/IntelliBridge/korean-administrative-rule-mcp) |

### 🛒 커머스 & 리테일 (2개)

| 이름 | 설명 | 링크 |
|------|------|------|
| daiso-mcp | 다이소 매장 검색, 상품 재고 및 가격 조회 | [GitHub](https://github.com/hmmhmmhm/daiso-mcp) |
| kr-pc-deals-mcp | 다나와·컴퓨존 PC 부품 최저가 검색, 가격 비교 | [GitHub](https://github.com/edward-kim-dev/kr-pc-deals-mcp) |

### 🏦 금융 & 세무 (8개)

| 이름 | 설명 | 링크 |
|------|------|------|
| KIS_MCP_Server | 한국투자증권 KIS Open API 트레이딩 (주식, ETF, 선물) | [GitHub](https://github.com/migusdn/KIS_MCP_Server) |
| kiwoom-mcp | 키움증권 영웅문 Open API+ 트레이딩 | [GitHub](https://github.com/sactho/kiwoom-mcp) |
| nh-investment-mcp | NH투자증권 나무/HTS API 트레이딩 | [GitHub](https://github.com/jgpKOR/nh-investment-mcp) |
| krecofin-mcp | 한국은행 경제통계시스템 API (금리, 환율, 물가 등) | [GitHub](https://github.com/MinBoo1104/krecofin-mcp) |
| dart-mcp | 금융감독원 DART API (공시, 재무제표, 사업보고서) | [GitHub](https://github.com/jaemanc/dart-mcp) |
| korea-irkorea-mcp | 국세청 홈택스/손택스 API (종합소득세, 부가세) | [GitHub](https://github.com/IntelliBridge/korea-irkorea-mcp) |
| ks-tax-mcp | 국세청 현금영수증 API, 사업자등록 상태 조회 | [GitHub](https://github.com/nicekk/ks-tax-mcp) |
| ks-bank-mcp | 오픈뱅킹 API 기반 은행 계좌 조회·이체 | [GitHub](https://github.com/nicekk/ks-bank-mcp) |

### 🏙️ 공공 데이터 & 행정 (5개)

| 이름 | 설명 | 링크 |
|------|------|------|
| korea-public-data-mcp | 공공데이터포털 API 통합 검색 (날씨, 교통, 인구 등) | [GitHub](https://github.com/yunjong0/ks-public-data-mcp) |
| korean-address-mcp | 도로명주소·지번주소 검색 및 좌표 변환 | [GitHub](https://github.com/nicekk/korean-address-mcp) |
| korea-region-code-mcp | 법정동코드 조회 및 행정구역 정보 | [GitHub](https://github.com/nicekk/korea-region-code-mcp) |
| mcp-korea-real-estate | 부동산 실거래가 조회 (아파트, 단독, 토지) | [GitHub](https://github.com/SunYH311/mcp-korea-real-estate) |
| korea-shelter-mcp | 전국 대피소 위치 정보 조회 | [GitHub](https://github.com/IntelliBridge/korea-shelter-mcp) |

### 🚆 교통 & 여행 (5개)

| 이름 | 설명 | 링크 |
|------|------|------|
| Odsay-MCP | ODsay 대중교통 경로 검색 (버스, 지하철) | [GitHub](https://github.com/RDWGit/Odsay-MCP) |
| korean-subway-mcp | 지하철 실시간 도착 정보 및 역 정보 조회 | [GitHub](https://github.com/SeoNaRu/korean-subway-mcp) |
| korail-mcp | 코레일 API 기반 기차 예매, 운임, 시간표 조회 | [GitHub](https://github.com/junwoo1211/korail-mcp) |
| t-money-bus-mcp | 티머니 버스 도착 정보 조회 | [GitHub](https://github.com/nicekk/t-money-bus-mcp) |
| seoul-bus-mcp | 서울시 버스 도착 정보 및 노선 조회 | [GitHub](https://github.com/seung23/seoul-bus-mcp) |

### 📱 메신저 & 커뮤니케이션 (4개)

| 이름 | 설명 | 링크 |
|------|------|------|
| kakaotalk-mcp | 카카오톡 메시지 전송 (친구, 그룹채팅) | [GitHub](https://github.com/nicekk/kakaotalk-mcp) |
| discord-kr-mcp | 디스코드 한국어 메시지 전송 및 채널 관리 | [GitHub](https://github.com/nicekk/discord-kr-mcp) |
| slack-kr-mcp | 슬랙 한국어 메시지 및 채널 관리 | [GitHub](https://github.com/nicekk/slack-kr-mcp) |
| telegram-kr-mcp | 텔레그램 한국어 메시지 및 봇 관리 | [GitHub](https://github.com/nicekk/telegram-kr-mcp) |

### 🏥 건강 & 의료 (4개)

| 이름 | 설명 | 링크 |
|------|------|------|
| koregx | 헬스케어 법령·행정해석·식약처·HIRA 결정 통합 조회 | [GitHub](https://github.com/DrMoony/koregx) |
| healthymap-mcp | 건강보험심사평가원 병원·약국 정보 및 진료과별 검색 | [GitHub](https://github.com/nicekk/healthymap-mcp) |
| korea-pharmacy-mcp | 약국 정보 및 영업시간, 휴무일 조회 | [GitHub](https://github.com/nicekk/korea-pharmacy-mcp) |
| korea-emergency-mcp | 전국 응급실 가용 병상 및 위치 조회 | [GitHub](https://github.com/IntelliBridge/korea-emergency-mcp) |

### 🌤️ 날씨 & 환경 (3개)

| 이름 | 설명 | 링크 |
|------|------|------|
| korea-weather-mcp | 기상청 API 단기/중기 예보, 미세먼지 조회 | [GitHub](https://github.com/nicekk/korea-weather-mcp) |
| airkorea-mcp | 에어코리아 대기질 정보 (미세먼지, 오존 등) | [GitHub](https://github.com/nicekk/airkorea-mcp) |
| korea-earthquake-mcp | 기상청 지진 정보 실시간 조회 | [GitHub](https://github.com/IntelliBridge/korea-earthquake-mcp) |

### 🛠️ 개발 & IT (5개)

| 이름 | 설명 | 링크 |
|------|------|------|
| naver-search-mcp | 네이버 검색 API (블로그, 뉴스, 쇼핑 등) | [GitHub](https://github.com/nicekk/naver-search-mcp) |
| kakao-search-mcp | 카카오 Daum 검색 API | [GitHub](https://github.com/nicekk/kakao-search-mcp) |
| naver-map-mcp | 네이버 지도 API (장소 검색, 경로, 지오코딩) | [GitHub](https://github.com/nicekk/naver-map-mcp) |
| kakao-map-mcp | 카카오맵 API (주소 검색, 좌표 변환) | [GitHub](https://github.com/nicekk/kakao-map-mcp) |
| google-search-kr-mcp | 구글 검색 (한국어 특화) | [GitHub](https://github.com/nicekk/google-search-kr-mcp) |

---

## 4. 한국 생활 자동화 스킬 (k-skill)

> 출처: [NomaDamas/k-skill](https://github.com/NomaDamas/k-skill) — 한국 서비스 기반 코딩 에이전트용 자동화 스킬 (86개). Claude Code, Codex, OpenCode, OpenClaw 지원. HTTP 프록시(`k-skill-proxy`) 패턴 사용.

### 🚄 교통 & 예약 (4개)

| 스킬명 | 설명 | 링크 |
|--------|------|------|
| `srt-booking` | SRT 열차 조회, 예약, 확인, 취소 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/srt-booking.md) |
| `ktx-booking` | KTX/코레일 열차 조회, 예약, 확인, 취소 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/ktx-booking.md) |
| `express-bus-booking` | KOBUS 고속버스 배차·좌석·요금 조회 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/express-bus-booking.md) |
| `intercity-bus-booking` | 티머니 시외버스 배차·좌석·요금 조회 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/intercity-bus-booking.md) |

### 🚇 대중교통 & 도시 정보 (2개)

| 스킬명 | 설명 | 링크 |
|--------|------|------|
| `seoul-subway-arrival` | 서울 지하철 역 실시간 도착 예정 열차 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/seoul-subway-arrival.md) |
| `seoul-density` | 서울 121개 핫스팟 실시간 혼잡도·인구 조회 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/seoul-density.md) |

### 🏨 숙박 (1개)

| 스킬명 | 설명 | 링크 |
|--------|------|------|
| `foresttrip-vacancy` | 숲나들e 자연휴양림 예약 가능 객실 자동 조회 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/foresttrip-vacancy.md) |

### 💬 메신저 (1개)

| 스킬명 | 설명 | 링크 |
|--------|------|------|
| `kakaotalk-mac` | macOS 카카오톡 대화 조회, 검색, 메시지 전송/삭제 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/kakaotalk-mac.md) |

### 🏦 금융 (3개)

| 스킬명 | 설명 | 링크 |
|--------|------|------|
| `kis-trading` | 한국투자증권 주식 매수/매도/조회 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/kis-trading.md) |
| `toss-ledger` | 토스 가계부 조회 및 분석 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/toss-ledger.md) |
| `korea-tax` | 홈택스 세금 신고·조회 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/korea-tax.md) |

### 📜 법률 & 공공 (2개)

| 스킬명 | 설명 | 링크 |
|--------|------|------|
| `law-search` | 국가법령정보 법령·판례 검색 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/law-search.md) |
| `national-assembly` | 국회 의안·회의록·의원 정보 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/national-assembly.md) |

### 🛒 쇼핑 & 커머스 (3개)

| 스킬명 | 설명 | 링크 |
|--------|------|------|
| `coupang-search` | 쿠팡 상품 검색 및 가격 비교 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/coupang-search.md) |
| `daiso-search` | 다이소 상품 검색 및 재고 확인 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/daiso-search.md) |
| `naver-shopping` | 네이버 쇼핑 최저가 검색 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/naver-shopping.md) |

### 🍽️ 식당 & 배달 (2개)

| 스킬명 | 설명 | 링크 |
|--------|------|------|
| `naver-restaurant` | 네이버 지도 기반 식당 검색·리뷰 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/naver-restaurant.md) |
| `baemin-order` | 배달의민족 주문 내역 조회 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/baemin-order.md) |

### 🌤️ 날씨 & 환경 (2개)

| 스킬명 | 설명 | 링크 |
|--------|------|------|
| `korea-weather` | 기상청 날씨·미세먼지 조회 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/korea-weather.md) |
| `air-quality` | 에어코리아 실시간 대기질 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/air-quality.md) |

### 🏥 건강 & 병원 (2개)

| 스킬명 | 설명 | 링크 |
|--------|------|------|
| `hospital-search` | 병원·약국 정보 및 영업시간 조회 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/hospital-search.md) |
| `emergency-bed` | 전국 응급실 가용 병상 실시간 조회 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/emergency-bed.md) |

### 📱 통신 & 생활 (3개)

| 스킬명 | 설명 | 링크 |
|--------|------|------|
| `skt-billing` | SKT 통신비 명세서 조회 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/skt-billing.md) |
| `kepco-bill` | 한전 전기요금 조회 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/kepco-bill.md) |
| `korea-post` | 우체국 우편·택배 배송 조회 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/korea-post.md) |

### 📝 문서 & 작업 (3개)

| 스킬명 | 설명 | 링크 |
|--------|------|------|
| `notion-kr` | 한국어 Notion 페이지 읽기·쓰기 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/notion-kr.md) |
| `google-docs-kr` | 구글 문서 한국어 읽기·쓰기 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/google-docs-kr.md) |
| `hwp-reader` | 한글(.hwp) 파일 읽기 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/hwp-reader.md) |

---

## 5. 분야별 분류 색인

### 🎨 프론트엔드 & UI/UX 디자인

| 스킬/도구 | 출처 | 설명 |
|----------|------|------|
| frontend-design | skills.sh #1 (Anthropics) | 프론트엔드 UI 컴포넌트·레이아웃·반응형 디자인 모범 사례 |
| web-design-guidelines | skills.sh #17 (Vercel) | 웹 디자인 가이드라인 — 타이포그래피, 색상, 간격, 접근성 |
| ui-ux-pro-max | skills.sh #34 | UI/UX 프로 맥스 — 고급 인터랙션, 애니메이션, 마이크로 인터랙션 |
| sleek-design-mobile-apps | skills.sh #69 | 모바일 앱 슬릭 디자인 — iOS/Android 네이티브 느낌 UI |
| impeccable | skills.sh #107 (pbakaus) | SVG 애니메이션 & 디자인 도구 — 감사, 비평, 최적화 |
| extract-design-system | skills.sh #140 | 기존 코드에서 디자인 토큰, 컴포넌트 자동 추출 |
| design-taste-frontend | skills.sh #151 | 프론트엔드 디자인 테스트 — UI 퀄리티 평가, 개선 제안 |
| high-end-visual-design | skills.sh #158 | 하이엔드 비주얼 디자인 — 프리미엄 UI/UX 가이드 |
| minimalist-ui | skills.sh #182 | 미니멀 UI — 심플하고 깔끔한 인터페이스 디자인 |
| industrial-brutalist-ui | skills.sh #195 | 인더스트리얼 브루탈리스트 UI — 강렬한 디자인 스타일 |

### ⚛️ React & Next.js

| 스킬/도구 | 출처 | 설명 |
|----------|------|------|
| vercel-react-best-practices | skills.sh #2 | React 모범 사례 — 컴포지션, 상태관리, 성능 최적화 |
| vercel-composition-patterns | skills.sh #35 | React 컴포지션 패턴 — 합성 vs 상속 |
| vercel-react-native-skills | skills.sh #114 | React Native — 네이티브 모듈, 네비게이션, 성능 |
| next-best-practices | skills.sh #154 | Next.js 모범 사례 — App Router, SSR, 캐싱 |
| next-cache-components | Vercel | Next.js 캐시 컴포넌트 — 서버 컴포넌트 캐싱 전략 |
| next-upgrade | Vercel | Next.js 업그레이드 — 마이그레이션, 브레이킹 체인지 |

### ☁️ 클라우드 & 인프라

| 스킬/도구 | 출처 | 설명 |
|----------|------|------|
| microsoft-foundry | skills.sh #4 | Azure AI Foundry — AI 모델 배포·관리·엔드포인트 |
| azure-ai | skills.sh #5 | Azure AI — OpenAI, Cognitive Services, 검색 통합 |
| azure-deploy | skills.sh #6 | Azure 배포 — App Service, Container Apps, ARM/Bicep |
| azure-kubernetes | skills.sh #43 | AKS — 클러스터 배포, 스케일링, 모니터링 |
| azure-cost-optimization | skills.sh #30 | 비용 최적화 — 예약, 스팟인스턴스, 미사용 정리 |
| supabase-postgres-best-practices | skills.sh #29 | Supabase & Postgres — 인증, RLS, 실시간, 엣지 함수 |
| firebase-basics | skills.sh #200 | Firebase — 인증, Firestore, 스토리지, 호스팅 |

### 🔧 개발 워크플로우 & 아키텍처

| 스킬/도구 | 출처 | 설명 |
|----------|------|------|
| improve-codebase-architecture | skills.sh #28 | 코드베이스 아키텍처 개선 — 결합도 감소, 도메인 언어 정리 |
| tdd | skills.sh #36 | TDD 레드-그린-리팩터 루프 |
| diagnose | skills.sh #73 | 버그 진단 — 재현→최소화→가설→수정→회귀테스트 |
| prototype | skills.sh #128 | 프로토타이핑 — 상태/비즈니스 로직 또는 UI 변형 탐색 |
| triage | skills.sh #103 | 이슈 트리아지 — 상태기반 워크플로우로 이슈 관리 |
| write-a-skill | skills.sh #76 | 스킬 작성법 — 구조, 프로그레시브 디스클로저 |
| skill-creator | skills.sh #24 | 스킬 생성기 — 새로운 에이전트 스킬 자동 생성 |
| to-prd | skills.sh #70 | PRD 작성 — 대화 컨텍스트를 제품 요구사항 문서로 |
| to-issues | skills.sh #72 | 이슈 분해 — PRD/계획을 독립적 GitHub 이슈로 분할 |
| grill-me / grill-with-docs | skills.sh #23, #33 | 설계 리뷰 인터뷰 — 계획을 집요하게 검토 |
| zoom-out | skills.sh #77 | 줌아웃 — 코드의 큰 그림 보기, 전체 아키텍처 이해 |
| brainstorming | skills.sh #32 | 브레인스토밍 — 아이디어 발굴, 마인드맵, 창발적 사고 |
| caveman | skills.sh #27 | 초압축 커뮤니케이션 — 토큰 75% 절약 |
| handoff | skills.sh #141 | 핸드오프 — 작업 인계 문서 자동 생성 |

### 🖼️ AI 이미지 & 비디오 생성

| 스킬/도구 | 출처 | 설명 |
|----------|------|------|
| gpt-image-2 | skills.sh #71 | GPT Image 2 — 고품질 AI 이미지 생성, 프롬프트 엔지니어링 |
| ai-image-generation | skills.sh #120 | AI 이미지 생성 — 텍스트→이미지, 다양한 스타일 |
| ai-video-generation | skills.sh #122 | AI 비디오 생성 — 텍스트/이미지→동영상 |
| image-to-video | skills.sh #66 | 이미지→비디오 — 정지 이미지를 동영상으로 애니메이션 |
| face-swap | skills.sh #125 | 페이스 스왑 — 얼굴 교체, AI 아바타 제작 |
| lipsync | skills.sh #132 | 립싱크 — 입모양 동기화, 음성-영상 합성 |
| image-inpainting | skills.sh #129 | 이미지 인페인팅 — 이미지 내 영역 복원·수정 |
| image-outpainting | skills.sh #134 | 이미지 아웃페인팅 — 이미지 외곽 확장 |
| video-edit | skills.sh #65 | AI 비디오 편집 — 자르기, 병합, 자막, 전환 효과 |
| controlnet-pose | skills.sh #131 | ControlNet 포즈 제어 — 자세 기반 이미지 생성 |
| ai-avatar-video | skills.sh #126 | AI 아바타 비디오 — 가상 인물 영상 생성 |
| kling-3-0 | skills.sh #102 | Kling 3.0 — 최신 AI 비디오 생성 모델 |
| remotion-best-practices | skills.sh #19 | React 기반 프로그래매틱 비디오 제작 |
| relight | skills.sh #135 | 릴라이트 — 이미지 조명 재설정, 그림자 조정 |
| elevenlabs-music-generation | skills.sh #137 | ElevenLabs 음악 생성 — AI 배경음악, BGM |

### 📊 마케팅 & 콘텐츠

| 스킬/도구 | 출처 | 설명 |
|----------|------|------|
| seo-audit | skills.sh #121 | SEO 감사 — 메타 태그, 구조화 데이터, 페이지 속도 |
| copywriting | skills.sh #142 | 마케팅 카피라이팅 — 헤드라인, CTA, 이메일 카피 |
| content-strategy | skills.sh #186 | 콘텐츠 전략 — 기획, 캘린더, 퍼널 설계 |
| marketing-psychology | skills.sh #178 | 소비자 심리학 — 설득, 신뢰, 행동 유도 패턴 |
| programmatic-seo | skills.sh #192 | 프로그래매틱 SEO — 대량 페이지 자동 생성 |
| marketing-ideas | skills.sh #196 | 마케팅 아이디어 — 창의적 캠페인, 프로모션 기획 |

### 📱 협업 & 메신저 (Lark/Feishu)

| 스킬/도구 | 출처 | 설명 |
|----------|------|------|
| lark-doc | skills.sh #37 | Lark 문서 생성·편집 — 리치 텍스트, 협업 문서 |
| lark-base | skills.sh #38 | Lark Base — 레코드 생성, 조회, 필터, 뷰 관리 |
| lark-im | skills.sh #39 | Lark 메신저 — 메시지 발송, 채팅방 관리, 봇 연동 |
| lark-drive | skills.sh #40 | Lark 드라이브 — 파일 업로드, 다운로드, 권한 관리 |
| lark-calendar | skills.sh #42 | Lark 캘린더 — 일정 생성, 조회, 초대, 회의실 예약 |
| lark-sheets | skills.sh #46 | Lark 시트 — 스프레드시트 생성, 수식, 차트 |
| lark-vc | skills.sh #49 | Lark 비디오 회의 — 회의 생성, 녹화, 참가자 관리 |
| lark-minutes | skills.sh #50 | Lark 회의록 — AI 회의 요약, 액션아이템 자동 추출 |
| lark-approval | skills.sh #75 | Lark 결재 — 결재선 생성, 승인/반려 |
| lark-slides | skills.sh #105 | Lark 슬라이드 — 프레젠테이션 생성·편집 |
| lark-okr | skills.sh #116 | Lark OKR — 목표·핵심결과 설정, 진행률 추적 |
| kakaotalk-mac | k-skill | macOS 카카오톡 대화 조회, 검색, 메시지 전송/삭제 |

### 🇰🇷 한국 특화 (MCP + k-skill)

| 분야 | 대표 도구 | 설명 | 링크 |
|------|----------|------|------|
| 법률 | korean-law-mcp | 국가법령정보 API 기반 법령·판례 검색 | [GitHub](https://github.com/chrisryugj/korean-law-mcp) |
| 법률 | mcp-kr-legislation | 법제처 API 법령·판례·자치법규·조약 | [GitHub](https://github.com/ChangooLee/mcp-kr-legislation) |
| 법률 | assembly-api-mcp | 국회 API 의원·의안·회의록·청원 | [GitHub](https://github.com/hollobit/assembly-api-mcp) |
| 금융 | KIS_MCP_Server | 한국투자증권 KIS API 트레이딩 | [GitHub](https://github.com/migusdn/KIS_MCP_Server) |
| 금융 | kiwoom-mcp | 키움증권 영웅문 API+ 트레이딩 | [GitHub](https://github.com/sactho/kiwoom-mcp) |
| 금융 | krecofin-mcp | 한국은행 경제통계 (금리, 환율, 물가) | [GitHub](https://github.com/MinBoo1104/krecofin-mcp) |
| 금융 | dart-mcp | 금융감독원 DART 공시·재무제표 | [GitHub](https://github.com/jaemanc/dart-mcp) |
| 금융 | ks-bank-mcp | 오픈뱅킹 API 은행 계좌 조회·이체 | [GitHub](https://github.com/nicekk/ks-bank-mcp) |
| 금융 | korea-tax (k-skill) | 홈택스 세금 신고·조회 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/korea-tax.md) |
| 금융 | kis-trading (k-skill) | 한국투자증권 주식 매수/매도 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/kis-trading.md) |
| 교통 | korail-mcp | 코레일 기차 예매, 운임, 시간표 | [GitHub](https://github.com/junwoo1211/korail-mcp) |
| 교통 | Odsay-MCP | 대중교통 경로 검색 (버스, 지하철) | [GitHub](https://github.com/RDWGit/Odsay-MCP) |
| 교통 | srt-booking (k-skill) | SRT 열차 조회, 예약, 취소 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/srt-booking.md) |
| 교통 | ktx-booking (k-skill) | KTX 열차 조회, 예약, 취소 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/ktx-booking.md) |
| 공공 | korea-public-data-mcp | 공공데이터포털 통합 검색 | [GitHub](https://github.com/yunjong0/ks-public-data-mcp) |
| 공공 | seoul-density (k-skill) | 서울 핫스팟 실시간 혼잡도 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/seoul-density.md) |
| 건강 | healthymap-mcp | 병원·약국 정보, 진료과별 검색 | [GitHub](https://github.com/nicekk/healthymap-mcp) |
| 건강 | hospital-search (k-skill) | 병원·약국 정보, 영업시간 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/hospital-search.md) |
| 쇼핑 | daiso-mcp | 다이소 매장 검색, 재고·가격 | [GitHub](https://github.com/hmmhmmhm/daiso-mcp) |
| 쇼핑 | coupang-search (k-skill) | 쿠팡 상품 검색, 가격 비교 | [docs](https://github.com/NomaDamas/k-skill/blob/main/docs/features/coupang-search.md) |
| 날씨 | korea-weather-mcp | 기상청 예보, 미세먼지 | [GitHub](https://github.com/nicekk/korea-weather-mcp) |
| 메신저 | kakaotalk-mcp | 카카오톡 메시지 전송 | [GitHub](https://github.com/nicekk/kakaotalk-mcp) |

### 🧪 테스트 & 품질관리

| 스킬/도구 | 출처 | 설명 |
|----------|------|------|
| webapp-testing | skills.sh #177 | 웹앱 테스트 — E2E, 유닛, 비주얼 회귀 테스트 |
| tdd | skills.sh #36 | TDD 레드-그린-리팩터 루프 (MattPocock) |
| test-driven-development | skills.sh #148 | TDD 패턴 — 테스트 주도 개발 (obra) |
| systematic-debugging | skills.sh #124 | 체계적 디버깅 — 재현, 격리, 가설검증, 회귀테스트 |
| diagnose | skills.sh #73 | 버그 진단 루프 — 재현→수정→회귀테스트 |

### 📄 문서 포맷

| 스킬/도구 | 출처 | 설명 |
|----------|------|------|
| pptx | skills.sh #115 | PowerPoint 자동 생성 — 슬라이드, 차트, 이미지, 템플릿 |
| docx | skills.sh #146 | Word 문서 자동 생성 — 표, 차트, 스타일, 템플릿 |
| xlsx | skills.sh #153 | Excel 스프레드시트 — 표, 차트, 수식, 피벗 |

---

## 참고 링크

- **skills.sh**: [https://www.skills.sh](https://www.skills.sh) — 에이전트 스킬 디렉토리 (설치: `npx skills add <source>`)
- **Vercel Labs**: [https://github.com/vercel-labs](https://github.com/vercel-labs) — Vercel 실험실 프로젝트
- **agent-skills**: [https://github.com/vercel-labs/agent-skills](https://github.com/vercel-labs/agent-skills) — 공식 스킬 컬렉션
- **skills CLI**: [https://github.com/vercel-labs/skills](https://github.com/vercel-labs/skills) — 스킬 관리 CLI
- **awesome-mcp-korea**: [https://github.com/darjeeling/awesome-mcp-korea](https://github.com/darjeeling/awesome-mcp-korea) — 한국 MCP 서버 모음
- **k-skill**: [https://github.com/NomaDamas/k-skill](https://github.com/NomaDamas/k-skill) — 한국 생활 자동화 스킬
