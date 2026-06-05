---
description: "Insane Search — 차단된 웹사이트 자동 우회 (X/Reddit/YouTube/GitHub 등)"
argument-hint: "[URL 또는 키워드]"
---

# /insane-search

URL 접근이 차단될 때, 사이트 무관한 우회 전략을 자동 선택합니다. WAF/bot protection이 있는 사이트도 뚫어줍니다.

## 사용법

```bash
/insane-search https://twitter.com/...    # URL 직접 우회
/insane-search X에서 AI 검색               # 키워드 검색 후 우회
```

## 실행 방법

1. Read `.codex/skills/insane-search/SKILL.md`
2. 의도 분류:
   - URL 제공 → Phase 0 검사 후 Phase 1 (generic fetch chain)
   - 핸들 제공 (@username) → Phase 0 syndication/API
   - 키워드만 → WebSearch로 URL 확보 후 재진입
3. Phase 0: 플랫폼 공식 API 시도
4. Phase 1: Generic fetch chain (curl_cffi TLS impersonation)
5. 필요시 Phase 2: 수동 개입 (사용자 힌트)

## 지원 플랫폼
X/Twitter, Reddit, YouTube, GitHub, Mastodon, Medium, Substack, Stack Overflow, Threads, Naver, Coupang, LinkedIn, Bluesky, Hacker News, arXiv 등