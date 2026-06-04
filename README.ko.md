# Antigravity gptaku-plugins Bridge

이 프로젝트는 Claude Code 전용 플러그인 마켓플레이스인 **gptaku-plugins**의 플러그인들을 Google Antigravity/Codex 환경에 맞게 변환하여 설치해주는 브릿지 도구입니다.

## 주요 기능
- **플러그인 자동 변환 설치**: `.claude-plugin/plugin.json`을 `plugin.json`으로 이식하고, `installed_version.json`을 생성하여 Antigravity에 올바르게 연동합니다.
- **슬래시 명령어 스킬 이식**: Claude Code 전용 명령어(`commands/*.md`)를 Antigravity가 이해할 수 있는 개별 스킬(`skills/`) 형태로 변환하여, 사용자가 자연어로 해당 명령어를 실행할 수 있도록 호환성을 부여합니다.
- **자체 연동 스킬 제공**: Antigravity 내에서 `install-gptaku` 스킬을 탑재하여, 대화 중에 즉시 다른 플러그인을 설치할 수 있게 돕습니다.

## 설치 및 사용법

### 1. 개별 플러그인 설치
터미널에서 아래 스크립트를 직접 실행하여 원하는 플러그인을 설치할 수 있습니다:
```bash
node scripts/install-gptaku.mjs <플러그인 Git 저장소 주소>
```

**예시:**
```bash
node scripts/install-gptaku.mjs https://github.com/fivetaku/show-me-the-prd.git
```

### 2. Antigravity에서 대화로 설치하기
이 프로젝트의 스킬이 로드된 상태에서는 다음과 같이 에이전트에게 설치를 요청할 수 있습니다:
- *"show-me-the-prd 플러그인 설치해줘"*
- *"gptaku vibe-sunsang 설치"*

에이전트가 알아서 해당 레포지토리를 찾아 설치 스크립트를 구동하고 로드합니다.
