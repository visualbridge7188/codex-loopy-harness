# 네이버 RSS -> 워드프레스 하이브리드 자동 발행 시스템 설계서 (v1.0)

본 문서는 네이버 블로그 RSS 피드를 워드프레스에 수집 및 동기화하고, 모던 프론트엔드 아카이브 레이아웃과 완벽한 SEO 패널티 차단 기능을 제공하는 플러그인(스니펫)의 기술 사양서입니다.

---

## 1. 요구사항 명세 (Requirements & Specs)

### 1.1 데이터 제어 (CPT 및 저장 구조)
* **포스트 타입 선택 기능**: 
  - 어드민 설정 페이지에서 수집될 포스트 타입을 **'일반 글 (post)'** 또는 **'커스텀 포스트 타입 (naver_blog)'** 중 선택 가능합니다.
  - **신규 수집 적용 정책**: 사용자가 도중에 포스트 타입을 변경하더라도 기존 수집된 포스트의 타입은 유지되며, 변경 설정 이후 수집되는 신규 글부터 선택된 타입으로 발행됩니다. (DB 안정성 확보)
* **카테고리 자동 매핑 및 생성**:
  - 네이버 카테고리를 워드프레스 카테고리 ID와 매핑하는 설정을 지원합니다.
  - 매핑이 지정되지 않은 새로운 카테고리가 발견될 경우, '미분류'로 보내지 않고 동명의 워드프레스 카테고리를 자동으로 생성합니다.

### 1.2 아카이브 뷰 (하이브리드 아카이브 선택)
* **Toss 스타일**: 모던한 HSL 컬러 기반의 탭 인터페이스 및 카드/리스트 형태의 레이아웃.
* **Novamira 매거진 스타일**: 어두운 다크 모드 그리드 템플릿 레이아웃.
* **하이브리드 숏코드 매핑**:
  - 어드민 설정에서 전체 기본 아카이브 스타일을 지정합니다.
  - 개별 페이지에 숏코드를 배치할 때 매개변수(`[naver_rss style="toss"]` 또는 `[naver_rss style="magazine"]`)를 주면 설정된 기본 스타일과 무관하게 개별 영역의 레이아웃을 덮어씌웁니다 (Override).

### 1.3 이미지 수집 및 중복 방지 (Anti-Duplicate Media)
* **guid 기반 글 수집 스킵**: 네이버 고유 ID(`_naver_guid`) 메타 데이터가 이미 존재하는 글은 동기화 실행 시 수집을 건너뜁니다.
* **특성 이미지 존재 체크**: 동일한 글이 업데이트되거나 재동기화되더라도, 포스트에 이미 특성 이미지(attachment ID)가 연결되어 있다면 추가 다운로드 및 라이브러리 등록 프로세스를 완전히 건너뜁니다.
* **파일명 클렌징**: 다운로드 URL 뒤에 붙는 불필요한 쿼리 스트링(`?type=w80_r80` 등)을 제거한 순수 이미지 파일 경로명(`strtok($url, '?')`)을 추출하여 파일명 중복 생성 및 리소스 충돌을 차단합니다.
* **Referer 우회**: 네이버 이미지 핫링크 제한을 회피하기 위해 `Referer: https://blog.naver.com/` 헤더와 모바일 User-Agent를 동적으로 주입하여 sideload를 완벽하게 통과시킵니다.
* **기본 썸네일 지원**: 네이버 원문에 이미지가 한 장도 없을 경우를 대비하여, 워드프레스 미디어 라이브러리 연동을 통해 지정한 기본 안내/로고 이미지를 자동 썸네일로 연결합니다.

### 1.4 SEO 패널티 원천 차단 (SEO Protection)
* **Canonical URL 자동 주입**: 모드 A(본문 체류형) 이용 시, 소스코드의 `<head>` 영역에 원본 네이버 링크를 Canonical 태그로 명시하여 중복 문서 패널티를 완전히 방지합니다. Yoast SEO, Rank Math 등 주요 플러그인과 필터(`wpseo_canonical`, `rank_math/frontend/canonical`)를 통해 연동됩니다.
* **noindex, nofollow 설정**: 검색엔진 크롤러가 수집한 네이버 글을 아카이브나 상세 글에서 인덱싱하지 못하도록 `<meta name="robots" content="noindex,nofollow">` 태그를 주입합니다.
* **사이트맵 강제 제외**: 커스텀 포스트 타입(`naver_blog`) 사용 시, 워드프레스 코어 XML 사이트맵을 비롯해 Yoast, Rank Math 등의 플러그인 사이트맵 노출 대상에서 완전히 제외시킵니다.

### 1.5 시스템 호환성 및 오버헤드 방지
* **Classic Editor 강제 사용**: 네이버 본문에서 파싱된 다량의 복잡한 HTML 마크업이 구텐베르크(블록 에디터) 인터페이스와 충돌하여 어드민 화면을 손상시키는 현상을 막기 위해, 수집 포스트 유형에 대해 클래식 편집기 필터를 강제 활성화합니다.
* **수집 락(Lock) 메커니즘**: WP Transient API를 사용해 10분 만료의 동기화 락(`naver_rss_sync_lock`)을 걸어 중복 동기화 실행으로 인한 DB 락과 트래픽 과부하를 예방합니다.

---

## 2. 데이터 흐름 및 아키텍처 (Architecture)

### 2.1 주요 옵션 데이터 키 (`wp_options`)
* `naver_rss_settings`: 플러그인 환경 설정을 배열로 래핑하여 보관.
  - `rss_url` (string): 네이버 RSS 주소.
  - `fetch_count` (int): 1회 수집 최대 포스트 개수.
  - `post_status` (string): `publish` (즉시 공개, noindex) vs `draft` (임시저장).
  - `post_type_selection` (string): `post` (일반 글) vs `naver_blog` (CPT).
  - `archive_style` (string): `toss` vs `magazine`.
  - `category_mapping` (string): `네이버카테고리:워드프레스ID` 형태의 맵.
  - `auto_create_category` (bool): 카테고리 자동 생성 여부.
  - `default_thumb_id` (int): 기본 썸네일 Attachment ID.
  - `use_classic_editor` (bool): 클래식 에디터 강제 사용 여부.
  - `sync_interval` (string): `hourly`, `twicedaily`, `daily`, `manual`.

### 2.2 메타데이터 키 (`wp_postmeta`)
* `_naver_guid`: 네이버 RSS 아이템의 고유 ID (수집 중복 확인용).
* `_naver_original_url`: 네이버 블로그 원문 URL.
* `_naver_source_category`: 원본 네이버 카테고리 명.

---

## 3. 검증 계획 (Verification Plan)
* **스케줄링 등록 검증**: `wp_next_scheduled`를 사용하여 WP-Cron 스케줄이 비중복 등록되고 저장 변경 시 정상 리스케줄링되는지 확인합니다.
* **이미지 중복 확인**: 동일한 글 수집 시 미디어 라이브러리에 `image-1.jpg`, `image-2.jpg` 등 동일 리소스의 꼬리표 번호가 붙은 중복 미디어가 생성되지 않고 썸네일 재지정이 방지되는지 검증합니다.
* **SEO Canonical 주입 검증**: 상세 글 페이지 소스코드 뷰어에서 `<link rel="canonical" href="원본네이버주소">` 태그와 `<meta name="robots" content="noindex,nofollow">`가 헤드 1순위로 올바르게 렌더링되는지 테스트합니다.
