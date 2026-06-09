# Project DB Cleanup Report

* **작성일**: 2026-06-09
* **상태**: 읽기 전용 분석 완료
* **대상**: Notion Project DB
* **Data source**: `collection://6a268c42-374d-4d7b-b41d-d064c5987751`

---

## 1. 조회 결과

Notion API로 Project DB 전체 페이지를 조회했다. Notion DB 자체는 수정하지 않았다.

요약:

| 항목 | 개수 |
| --- | ---: |
| 전체 Project | 137 |
| Visible (`Hide=false`) | 115 |
| Hidden (`Hide=true`) | 22 |
| `Inbox` | 5 |
| `진행 중` | 21 |
| `중요O / 긴급X` | 5 |
| `완료` | 95 |
| `중지/대기/SKIP` | 11 |
| `완료`인데 visible | 86 |
| `중지/대기/SKIP`인데 visible | 4 |
| active인데 Task 0개 | 1 |
| 하위 Project가 있는 컨테이너 | 8 |
| visible이고 parent/child/task가 모두 없는 항목 | 30 |

---

## 2. 핵심 진단

Project DB가 지저분해 보이는 가장 큰 이유는 완료된 Project가 숨겨지지 않고 남아 있기 때문이다.

현재 `Project DB`에는 별도 `Archive` 속성이 없고, `Hide` 체크박스가 있다. 따라서 Project DB에서는 `Hide`를 archive-lite로 쓰는 것이 가장 단순하다.

정리 원칙:

1. `진행 상황 = 완료`이고 `Hide=false`인 항목은 기본적으로 `Hide=true` 후보.
2. `진행 상황 = 중지/대기/SKIP`이고 장기 보관 대상이면 `Hide=true` 후보.
3. `Inbox`는 상태 판단이 필요한 미분류 Project다.
4. `진행 중` 또는 `Next`인데 Task가 0개인 항목은 실제 진행 여부를 확인한다.
5. 상위 Project는 목표형 Project가 아니라 폴더/컨테이너로 인정한다.

---

## 3. 즉시 검토할 Inbox

현재 visible Inbox:

1. `사이트제작 프로젝트 템플릿` - Task 0
2. `정부지원사업` - Task 0
3. `헤르메스 Life OS` - Task 3
4. `AI 에이전트` - Task 10
5. `Wordpress & Agent` - Task 1

권장 판단:

1. 실제 진행 예정이면 `Next` 또는 `진행 중`.
2. 중요하지만 지금 하지 않으면 `중요O / 긴급X`.
3. 템플릿/보관용이면 `중지/대기/SKIP` 또는 `완료` 후 `Hide=true`.

---

## 4. 현재 active visible

현재 visible active:

1. `042 셀리나 쇼핑몰`
2. `051 : 올스테이트 추가페이지`
3. `052 듀오 : 추가 기능`
4. `053 탑재활시니어타운`
5. `054 TSS 페이지 제작`
6. `26송정 - PAPS`
7. `26송정-학생선수관리`
8. `결혼 준비`
9. `사직아시아드청약`
10. `쓰레드 수익화 마스터 1기 (플랫폼트리)`
11. `온디 브랜딩`
12. `왕초보가 하루 5분 쓰레드로 월 2억 찍은 방법 (프드프, 김지훈)`
13. `체험단&온디 글쓰기`
14. `CRM`
15. `N블로그`
16. `WEB`

주의:

1. `쓰레드 수익화 마스터 1기 (플랫폼트리)`는 `진행 중`인데 Task가 0개다.
2. `N블로그`는 기간이 2025-06-23 ~ 2025-09-23으로 오래되었지만 아직 `진행 중`이다.
3. `WEB`은 하위 Project 16개와 Task 22개가 있어 목표형 Project보다 컨테이너에 가깝다.

---

## 5. 컨테이너성 Project

하위 Project가 있는 항목:

1. `교사 크리에이터`
2. `그로쓰/연구`
3. `내 집 마련 프로젝트 (with 성장독서)`
4. `웹에이전시`
5. `웹에이전시 : 브랜딩 + 마케팅`
6. `학교School`
7. `N블로그`
8. `WEB`

권장 원칙:

1. 컨테이너성 Project는 완료율 관리 대상이 아니라 폴더/맥락 보관함으로 본다.
2. 컨테이너가 active로 남아 있어도 괜찮지만, 메인 실행 목록에는 너무 많이 노출하지 않는다.
3. 실제 실행은 하위 Project 또는 Task에서 관리한다.

---

## 6. 1차 정리 배치 후보

가장 안전한 1차 정리:

1. `진행 상황 = 완료` AND `Hide=false`인 86개를 `Hide=true`로 변경.
2. `진행 상황 = 중지/대기/SKIP` AND `Hide=false`인 4개를 검토 후 `Hide=true`로 변경.
3. `Inbox` 5개는 자동 변경하지 않고 수동 판단.
4. active 16개는 자동 변경하지 않고 현재 실행 목록으로 검토.

이 배치는 속성 추가 없이 기존 `Hide`만 사용한다. 삭제나 Archive 이동은 하지 않는다.

---

## 7. 다음 액션

권장 순서:

1. 완료 visible 86개를 `Hide=true`로 일괄 변경한다.
2. 중지/대기/SKIP visible 4개를 확인 후 숨긴다.
3. Inbox 5개를 하나씩 상태 결정한다.
4. active 16개 중 실제 진행 중이 아닌 것을 `중요O / 긴급X` 또는 `중지/대기/SKIP`로 내린다.
5. `WEB`, `N블로그`, `학교School`, `웹에이전시` 같은 컨테이너는 메인 실행 목록에서 보일 필요가 있는지 따로 판단한다.

---

## 8. 보류

이번 분석에서는 다음 작업을 하지 않았다.

1. Project DB 속성 추가
2. Project 삭제
3. Relation 변경
4. Task/Notes 수정
5. Posting Calendar 수정

---

## 9. 실행 기록

2026-06-09에 1차 정리를 실행했다.

실행한 변경:

1. `진행 상황 = 완료`
2. `Hide = false`
3. 위 조건을 모두 만족한 86개 Project를 `Hide = true`로 변경

검증 결과:

| 항목 | 변경 후 |
| --- | ---: |
| 전체 Project | 137 |
| Visible | 29 |
| Hidden | 108 |
| `완료` + visible | 0 |
| `완료` + hidden | 95 |
| `중지/대기/SKIP` + visible | 4 |
| `Inbox` + visible | 5 |
| active visible | 16 |

수정하지 않은 항목:

1. `Inbox` 5개
2. active visible 16개
3. `중지/대기/SKIP` visible 4개
4. 모든 Task, Notes, relation, page content
