# Notes DB Hermes Cleanup Report

* **작성일**: 2026-06-09
* **상태**: 1차 정리 실행 완료
* **대상**: Notion Notes DB

---

## 1. 목적

Notes DB에서 `Hermes`, `hermess`, `헤르메스`, `Life OS` 관련 자료를 찾아 `헤르메스 Life OS` Project와 Notebook으로 묶는다.

---

## 2. 실행 전제

1. Notes DB 전체 1,524개를 제목, 설명, URL, writer, genre, tag 기준으로 검색했다.
2. 검색 키워드는 `Hermes`, `hermess`, `헤르메스`, `Life OS`, `LifeOS`, `라이프 OS`, `헤르메스 OS`였다.
3. 후보는 13개였다.
4. Project relation은 기존 relation을 보존하고 `헤르메스 Life OS`만 추가했다.
5. Notebook relation은 `헤르메스 Life OS` Notebook을 새로 만든 뒤 연결했다.

---

## 3. 생성한 Notebook

| Notebook | URL |
| --- | --- |
| `헤르메스 Life OS` | `https://app.notion.com/p/37aeb9be4dd3816eb774f372923e6734` |

---

## 4. 연결한 Notes

다음 13개 Notes를 `헤르메스 Life OS` Project 및 `헤르메스 Life OS` Notebook에 연결했다.

1. `This is the EASIEST way to setup Hermes Agent`
2. `New NotebookLM + Hermes is INSANE! (FREE)`
3. `AI 활용 수준을 바꾸는, Hermes 에이전트 입문 전 개념 & 시스템 해부하기`
4. `헤르메스 에이전트 처음 써보는 분도 이 영상 하나로 끝납니다, 설치부터 회사 운영 자동화까지`
5. `99%가 모르는 Hermes 에이전트로 성장하는 AI 직원 만드는 법!`
6. `Every Hermes Concept explained for Normal People`
7. `헤르메스 에이전트 처음 써보는 분도 이 영상 하나로 끝납니다, 설치부터 회사 운영 자동화까지`
8. `AI 비서(hermes, openclaw) 세팅 완벽(?) 가이드 2편 - 오픈클로를 헤르메스 부하로 & mem0 메모리 & paperclip 작업판 #ai #llm #agent`
9. `현시점 최고의 24시간 AI 비서? Hermes Agent 꼭 써보세요!`
10. `6 Hermes Agent use cases I promise will change your life`
11. `Hermes Agent: Zero to Personal AI Assistant (1 Hour Course)`
12. `헤르메스 에이전트 20분 총정리`
13. `라이프 OS (Free)`

---

## 5. 검증 결과

Project relation:

1. 13개 Notes 모두 `헤르메스 Life OS` Project와 연결됨.

Notebook relation:

1. `헤르메스 Life OS` Notebook의 `Note` relation에 13개 Notes가 연결됨.
2. MCP fetch 기준으로 Notebook relation이 확인됨.

주의:

1. 로컬 Notion API 통합은 Notebook DB 또는 새 Notebook 페이지를 직접 조회하지 못했다.
2. Notebook 생성과 relation 검증은 Notion MCP 기준으로 수행했다.
3. Notes DB와 Project DB relation 업데이트는 Notion API로 수행했다.

---

## 6. 다음 작업

Notes DB 전체를 Notebook으로 자동 분류하기 전에 다음 기준을 먼저 확정한다.

1. Notebook은 모든 Note에 강제하지 않는다.
2. 자료가 충분히 쌓인 소주제만 Notebook으로 만든다.
3. Topic은 기본 분류, Notebook은 소주제 컬렉션으로 둔다.
4. 대량 자동 분류는 후보 목록을 먼저 만든 뒤 실행한다.
