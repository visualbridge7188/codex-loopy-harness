# Notion DB Schema Inventory

* **작성일**: 2026-06-09
* **상태**: fetch 기반 스키마 스냅샷
* **목표**: 실제 Notion DB 스키마를 가져와 헌법 문서와 비교할 기준선을 만든다.

---

## 1. 조회 범위

조회한 DB:

1. Task DB
2. Project DB
3. Notes DB
4. Topics DB
5. Notebook DB
6. Posting Calendar DB
7. Areas DB

주의:

1. 이번 작업은 스키마 조회만 수행했다.
2. 페이지 데이터 대량 조회나 DB 수정은 하지 않았다.
3. Notion query data source 도구는 내부 오류로 집계 실행에 실패했다.

---

## 2. Data Source IDs

| DB | Data source |
| --- | --- |
| Task DB | `collection://f6982172-411f-4438-8e95-d29a3f1eac0c` |
| Project DB | `collection://6a268c42-374d-4d7b-b41d-d064c5987751` |
| Notes DB | `collection://27bb1dd1-a1a3-4241-969b-2100db26d0ca` |
| Topics DB | `collection://23feb9be-4dd3-8140-ae3f-000b8d7ccb2e` |
| Notebook DB | `collection://23feb9be-4dd3-813b-a63f-000bfd775026` |
| Posting Calendar DB | `collection://231eb9be-4dd3-81f2-9793-000b78ed606d` |
| Areas DB | `collection://23feb9be-4dd3-8101-b00f-000bbc654676` |

---

## 3. Schema Summary

### Task DB

핵심 속성:

1. `Title`: title
2. `Date`: date
3. `상태`: status
4. `Complete`: checkbox
5. `Project`: relation to Project DB
6. `Notes`: relation to Notes DB
7. `Areas`: relation to Areas DB
8. `📆 Posting Calendar`: relation to Posting Calendar DB
9. `상위 작업`: self relation, limit 1
10. `하위 작업`: self relation
11. `프로젝트 진행상황`: rollup from Project status
12. `인박스 체크`: checkbox
13. `체크 표시됨`: checkbox

해석:

Task DB는 실행 작업 원장이다. Project, Notes, Areas, Posting Calendar와 모두 연결되어 있어 실행 작업이 여러 맥락에서 들어올 수 있는 구조다.

### Project DB

핵심 속성:

1. `프로젝트 명`: title
2. `진행 상황`: status
3. `Task`: relation to Task DB
4. `Notes`: relation to Notes DB
5. `Areas`: relation to Areas DB
6. `상위 프로젝트`: self relation, limit 1
7. `하위 프로젝트`: self relation
8. `Related Projects`: self relation
9. `기간`: date
10. `롤업`: Task status rollup
11. `Hide`: checkbox
12. `Drive URL`: url

해석:

Project DB는 목표형 Project와 컨테이너성 Project가 섞여 있다. `상위 프로젝트/하위 프로젝트`가 실제로 존재하므로, 상위 Project를 분류/폴더로 인정한 헌법과 일치한다.

### Notes DB

핵심 속성:

1. `Title`: title
2. `Topics`: relation to Topics DB
3. `Areas`: relation to Areas DB
4. `Notebook`: relation to Notebook DB
5. `Projects`: relation to Project DB
6. `Task`: relation to Task DB
7. `Drive`: relation to Drive DB
8. `Task's project`: rollup from Task -> Project
9. `genres`: select
10. `tag`: multi_select
11. `URL`: url
12. `Archive`: checkbox
13. `Favorite`: checkbox
14. `stats`: status

해석:

Notes DB는 지식 원장 역할과 일치한다. 다만 실제 분류 축이 `Topics`, `Areas`, `tag`, `genres`, `Notebook`으로 많아 분류 중복이 발생할 수 있다.

### Topics DB

핵심 속성:

1. `Name`: title
2. `Note`: relation to Notes DB
3. `Area`: relation to Areas DB

해석:

Topics DB는 Notes의 주제 분류이며, 실제로는 Areas 아래의 하위 주제에 가깝다.

### Notebook DB

핵심 속성:

1. `Name`: title
2. `Note`: relation to Notes DB
3. `Archive`: checkbox

해석:

Notebook DB는 단순한 소주제 컬렉션이다. 필수 분류가 아니라 선택적 보조 DB로 두는 것이 맞다.

### Posting Calendar DB

핵심 속성:

1. `키워드`: title
2. `일정`: date
3. `Task DB`: relation to Task DB, limit 1
4. `To task`: checkbox
5. `Posted`: checkbox
6. `Status`: select
7. `SNS`: multi_select
8. `키워드 성격`: select
9. `키워드 유형`: select
10. `월`: relation to Month DB
11. `발행 링크`: url
12. `프롬프트`: formula

해석:

Posting Calendar DB는 콘텐츠 원장 역할과 일치한다. Task DB relation은 limit 1이라 현재 구조는 포스팅 항목 하나가 대표 Task 하나와 연결되는 형태다.

### Areas DB

핵심 속성:

1. `Name`: title
2. `Type`: select
3. `Topic`: relation to Topics DB
4. `Notes`: relation to Notes DB
5. `Projects`: relation to Project DB
6. `Task`: relation to Task DB
7. `Archive`: checkbox
8. `Cover`: file

해석:

Areas DB는 실제 스키마상 최상위 영역 DB다. Notes뿐 아니라 Project와 Task에도 연결되어 있어, Topic보다 넓은 맥락 축으로 봐야 한다.

---

## 4. 발견한 정리 포인트

### 1. Areas DB가 헌법에 포함되어야 한다

처음 설명에서는 Topics가 대주제처럼 보였지만, 실제 스키마에는 Areas DB가 있고 Topics는 Area와 연결되어 있다.

정리:

1. Area = 최상위 영역
2. Topic = Area 아래 주제
3. Notebook = 선택적 소주제 컬렉션
4. Note = 지식 원장

### 2. Notes DB의 분류 축이 많다

Notes DB에는 `Topics`, `Areas`, `Notebook`, `tag`, `genres`가 함께 있다.

정리 원칙:

1. `Topics`: 기본 주제 분류
2. `Areas`: 상위 영역
3. `Notebook`: 선택적 소주제 컬렉션
4. `genres`: 자료 형식
5. `tag`: 임시/세부 태그

### 3. Project DB의 상위/하위 구조는 실제로 존재한다

`상위 프로젝트`와 `하위 프로젝트`가 있고, `상위 프로젝트`는 limit 1이다.

정리 원칙:

1. 상위 Project는 분류/폴더/맥락 보관함으로 인정한다.
2. 하위 Project가 실제 결과물을 만들면 목표형 Project로 둔다.
3. 깊은 계층화는 피한다.

### 4. Posting Calendar는 콘텐츠 원장이다

`키워드`, `일정`, `키워드 성격`, `키워드 유형`, `발행 링크`, `프롬프트`가 있어 콘텐츠 기획/추적용 DB로 명확하다.

정리 원칙:

1. Posting Calendar 항목은 Task가 아니다.
2. Task DB relation은 실행 작업 연결이다.
3. 현재 자동화 개선은 보류한다.

---

## 5. 다음 정리 순서

1. Areas, Topics, Notebook의 역할을 최종 확정한다.
2. Notes DB의 `tag`와 `genres`가 각각 무엇을 담당하는지 정한다.
3. Project DB의 상위 Project 사용 규칙을 사례 기준으로 정한다.
4. 필요하면 소량 샘플 조회로 실제 사용 패턴을 확인한다.
5. 수정이 필요하면 속성 추가보다 운영 규칙과 view 정리부터 검토한다.
