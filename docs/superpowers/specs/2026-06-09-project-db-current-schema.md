# Project DB Current Schema

* **작성일**: 2026-06-09
* **상태**: 현재 운영 스키마 기록
* **대상**: Notion Project DB

---

## 1. 목적

Project DB에 `컨테이너` status가 추가된 현재 상태를 기준선으로 저장한다.

---

## 2. 주요 속성

| 속성 | 타입 | 역할 |
| --- | --- | --- |
| `프로젝트 명` | title | Project 이름 |
| `진행 상황` | status | Project 운영 상태 |
| `Hide` | checkbox | 보관/숨김 처리 |
| `기간` | date | 참고용 Project 기간 |
| `Task` | relation | 연결된 실행 작업 |
| `Notes` | relation | 연결된 자료/리서치 |
| `Areas` | relation | 상위 영역 |
| `상위 프로젝트` | self relation, limit 1 | 컨테이너/상위 Project |
| `하위 프로젝트` | self relation | 하위 Project |
| `Related Projects` | self relation | 느슨한 관련 Project |
| `롤업` | rollup | Task status 참고값 |
| `Drive URL` | url | 외부 Drive 링크 |
| `설명` | text | 설명 |
| `완료` | button | 완료 처리 버튼 |
| `End date` | formula | 기간 기반 보조 날짜 |
| `최종 편집 일시` | last edited time | 시스템 관리 |

---

## 3. 진행 상황 Status

현재 status option:

1. `Inbox`
2. `중요O / 긴급X`
3. `Next`
4. `진행 중`
5. `완료`
6. `중지/대기/SKIP`
7. `컨테이너`

Status group:

| Group | Options |
| --- | --- |
| `To-do` | `Inbox`, `중요O / 긴급X`, `Next`, `컨테이너` |
| `In progress` | `진행 중` |
| `Complete` | `중지/대기/SKIP`, `완료` |

---

## 4. 운영 규칙

1. 목표형 Project는 `Inbox`, `Next`, `진행 중`, `완료`, `중지/대기/SKIP`, `중요O / 긴급X`로 관리한다.
2. 상위/폴더/운영 묶음 Project는 `컨테이너`로 관리한다.
3. `컨테이너` Project는 일반 목표형 Project처럼 완료 처리하지 않는다.
4. Project DB에는 별도 `Archive` 속성이 없으므로, 보관/숨김은 `Hide`로 처리한다.
5. `기간`은 참고값이다. 실제 실행 날짜는 Task DB가 담당한다.

---

## 5. 현재 컨테이너 Project

1. `WEB`
2. `그로쓰/연구`
3. `온디`
4. `웹에이전시`
5. `웹에이전시 : 브랜딩 + 마케팅`
6. `학교School`
7. `Life`
8. `이벤트/일지`
