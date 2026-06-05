# AI Agent용 스킬 발견 프롬프트

아래 세 문장을 그대로 복사해서 에이전트 설정(CLAUDE.md, .cursor/rules, AGENTS.md, .clinerules 등)에 붙여넣으세요.

---

새 도구를 직접 만들지 말고, 먼저 https://github.com/vercel-labs/skills 에서 이미 검증된 스킬을 `npx skills find <키워드>` 또는 `curl -s "https://skills.sh/api/search?q=<키워드>&limit=10"` 로 찾아 설치(`npx skills add <소스> -g -y`)하여 활용해라.

프로젝트 루트에 AGENTS.md 파일이 없으면 만들고, 있으면 기존 내용 아래에 위 스킬 활용 규칙을 추가해라.

작업 중 필요한 능력이 부족하면 https://skills.sh 에서 사용 니즈에 맞는 스킬을 스스로 검색·평가·설치하고, 설치된 스킬의 SKILL.md를 읽어 즉시 활용해라.