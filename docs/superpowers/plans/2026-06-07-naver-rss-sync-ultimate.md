# 네이버 RSS 자동 발행 통합 시스템 (Ultimate) 구현 계획서

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** 네이버 블로그 RSS 글을 수집하여 포스트 타입 선택(CPT vs Post), 하이브리드 아카이브 스타일 분기, 완벽한 SEO 패널티 우회 및 썸네일/미디어 중복 방지 로직이 결합된 싱글톤 구조의 플러그인을 구현합니다.

**Architecture:** 싱글톤 패턴(`Naver_RSS_Sync_Ultimate`)을 핵심으로 하며, Cron 기반 스케줄러, DB 마이그레이션 방지 수집 로직, Canonical/robots SEO 제어기, Toss/Magazine 프론트엔드 아카이브 렌더링 엔진을 하나의 파일로 캡슐화합니다.

**Tech Stack:** WordPress Core API, PHP, WP-Cron, WP Transient API, XPath/SimpleXML.

---

### Task 1: 수집 및 이미지 중복 방지 유닛 테스트 환경 구축

**Files:**
- Create: `tests/mock-rss.xml`
- Create: `tests/test-runner.php`

- [ ] **Step 1: Mock용 네이버 RSS XML 파일 작성**
  
  ```xml
  <!-- File: tests/mock-rss.xml -->
  <?xml version="1.0" encoding="utf-8" ?>
  <rss version="2.0">
    <channel>
      <title>네이버 블로그 RSS 테스트</title>
      <link>https://blog.naver.com/test</link>
      <item>
        <title>샘플 글 제목 1</title>
        <link>https://blog.naver.com/test/12345</link>
        <description><![CDATA[본문 내용에 이미지가 들어있습니다. <img src="https://postfiles.pstatic.net/test_img1.jpg?type=w80_r80">]]></description>
        <category>일상</category>
        <pubDate>Sun, 07 Jun 2026 12:00:00 +0900</pubDate>
        <guid>https://blog.naver.com/test/12345</guid>
      </item>
    </channel>
  </rss>
  ```

- [ ] **Step 2: 핵심 파싱 및 중복 검증을 위한 목킹 테스트 러너 작성**
  
  ```php
  // File: tests/test-runner.php
  <?php
  // 워드프레스 최소 로드 모방 및 파싱 로직 단위 테스트
  function test_image_url_cleansing() {
      $url = "https://postfiles.pstatic.net/test_img1.jpg?type=w80_r80";
      $clean_url = strtok($url, '?');
      $filename = basename($clean_url);
      
      assert($clean_url === "https://postfiles.pstatic.net/test_img1.jpg");
      assert($filename === "test_img1.jpg");
      echo "PASS: Image URL Cleansing Test\n";
  }

  test_image_url_cleansing();
  ```

- [ ] **Step 3: 테스트 실행 및 검증**
  
  Run: `php tests/test-runner.php`
  Expected: `PASS: Image URL Cleansing Test`가 출력되어야 함.

- [ ] **Step 4: Commit**
  
  ```bash
  git add tests/mock-rss.xml tests/test-runner.php
  git commit -m "test: set up mock rss and url parsing test"
  ```

---

### Task 2: CPT 및 어드민 설정 콘솔 뼈대 구현

**Files:**
- Create: `docs/snippets/naver_rss_sync_ultimate.php`

- [ ] **Step 1: 싱글톤 뼈대 및 CPT 동적 등록 로직 작성**
  
  ```php
  <?php
  /**
   * Snippet Name: Naver RSS Sync Ultimate
   * Description: 통합 네이버 RSS 동기화 및 하이브리드 아카이브 제어 시스템
   */
  if ( ! defined( 'ABSPATH' ) ) exit;

  if ( ! class_exists( 'Naver_RSS_Sync_Ultimate' ) ) {
  class Naver_RSS_Sync_Ultimate {
      private static $instance = null;
      private $option_name = 'naver_rss_sync_ultimate_settings';

      public static function get_instance() {
          if ( null === self::$instance ) {
              self::$instance = new self();
          }
          return self::$instance;
      }

      private function __construct() {
          add_action( 'init', [ $this, 'register_post_type_if_enabled' ] );
          add_action( 'admin_menu', [ $this, 'register_admin_menu' ] );
          add_action( 'admin_init', [ $this, 'initialize_settings' ] );
          add_filter( 'use_block_editor_for_post_type', [ $this, 'disable_gutenberg_for_naver' ], 10, 2 );
      }

      public function get_settings() {
          $default = [
              'rss_url' => '',
              'fetch_count' => 5,
              'post_status' => 'draft',
              'post_type_selection' => 'naver_blog',
              'archive_style' => 'toss',
              'category_mapping' => '',
              'auto_create_category' => 1,
              'default_thumb_id' => 0,
              'use_classic_editor' => 1,
              'sync_interval' => 'manual',
          ];
          return get_option( $this->option_name, $default );
      }

      public function register_post_type_if_enabled() {
          $settings = $this->get_settings();
          if ( $settings['post_type_selection'] === 'naver_blog' ) {
              register_post_type( 'naver_blog', [
                  'labels' => [ 'name' => '네이버 블로그', 'singular_name' => '네이버 글' ],
                  'public' => true,
                  'has_archive' => 'naver-blog',
                  'rewrite' => [ 'slug' => 'naver-blog' ],
                  'exclude_from_search' => true,
                  'show_in_rest' => true,
                  'menu_icon' => 'dashicons-rss',
                  'supports' => [ 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ],
                  'taxonomies' => [ 'category' ],
              ]);
          }
      }

      public function disable_gutenberg_for_naver( $status, $type ) {
          $settings = $this->get_settings();
          if ( $settings['use_classic_editor'] && ($type === 'naver_blog' || $type === 'post') ) {
              return false;
          }
          return $status;
      }

      public function register_admin_menu() {
          add_options_page( '네이버 RSS 얼티밋', '네이버 RSS 설정', 'manage_options', 'naver-rss-ultimate', [ $this, 'render_admin_page' ] );
      }

      public function initialize_settings() {
          register_setting( 'naver_rss_ultimate_group', $this->option_name );
      }

      public function render_admin_page() {
          echo "<h2>네이버 RSS 설정반</h2>";
      }
  }
  Naver_RSS_Sync_Ultimate::get_instance();
  }
  ```

- [ ] **Step 2: Commit**
  
  ```bash
  git add docs/snippets/naver_rss_sync_ultimate.php
  git commit -m "feat: init ultimate plugin skeleton and cpt selection"
  ```

---

### Task 3: 중복 방지 기반 수집(Sync) 코어 개발

**Files:**
- Modify: `docs/snippets/naver_rss_sync_ultimate.php`

- [ ] **Step 1: WP Transient 락킹 및 원본 체크 중복 방지 수집 핵심 로직 추가**
  
  ```php
  // docs/snippets/naver_rss_sync_ultimate.php 내에 아래 메소드 추가
      public function run_sync() {
          if ( get_transient( 'naver_rss_sync_lock' ) ) {
              return new WP_Error( 'locked', '이미 동기화 작업이 실행 중입니다.' );
          }
          set_transient( 'naver_rss_sync_lock', 1, 10 * MINUTE_IN_SECONDS );

          $settings = $this->get_settings();
          $rss_url = $settings['rss_url'];
          if ( empty($rss_url) ) {
              delete_transient( 'naver_rss_sync_lock' );
              return new WP_Error( 'empty_url', 'RSS 주소를 입력하세요.' );
          }

          $response = wp_remote_get( $rss_url, [ 'timeout' => 20 ] );
          if ( is_wp_error( $response ) ) {
              delete_transient( 'naver_rss_sync_lock' );
              return $response;
          }

          $xml_body = wp_remote_retrieve_body( $response );
          $xml = @simplexml_load_string( $xml_body, 'SimpleXMLElement', LIBXML_NOCDATA );
          if ( ! $xml ) {
              delete_transient( 'naver_rss_sync_lock' );
              return new WP_Error( 'invalid_xml', 'XML 파싱에 실패했습니다.' );
          }

          $imported = 0;
          foreach ( $xml->channel->item as $item ) {
              $guid = trim( (string) $item->guid );
              $link = trim( (string) $item->link );
              $title = trim( (string) $item->title );
              $description = (string) $item->description;

              // 1. 중복 방지: GUID 대조로 이미 수집된 글인지 체크
              $existing = get_posts([
                  'post_type' => [ 'post', 'naver_blog' ],
                  'meta_key' => '_naver_guid',
                  'meta_value' => $guid,
                  'post_status' => 'any',
                  'posts_per_page' => 1,
                  'fields' => 'ids'
              ]);
              if ( ! empty($existing) ) continue;

              if ( $imported >= $settings['fetch_count'] ) break;

              // 포스트 타입 분기 결정
              $target_post_type = $settings['post_type_selection'];

              $pid = wp_insert_post([
                  'post_title' => $title,
                  'post_content' => wp_kses_post($description),
                  'post_status' => $settings['post_status'],
                  'post_type' => $target_post_type,
                  'meta_input' => [
                      '_naver_guid' => $guid,
                      '_naver_original_url' => $link
                  ]
              ]);

              if ( ! is_wp_error($pid) && $pid > 0 ) {
                  $imported++;
                  // 2. 특성 이미지 및 중복 썸네일 방지 로직 적용
                  $this->sideload_featured_image( $description, $pid );
              }
          }

          delete_transient( 'naver_rss_sync_lock' );
          return $imported;
      }

      private function sideload_featured_image( $description, $post_id ) {
          // 3. 특성 이미지가 이미 존재한다면 스킵
          if ( get_post_thumbnail_id($post_id) ) return;

          preg_match( '/<img[^>]+src="([^">]+)"/', $description, $matches );
          if ( empty($matches[1]) ) {
              // 기본 썸네일 적용
              $settings = $this->get_settings();
              if ( $settings['default_thumb_id'] > 0 ) {
                  set_post_thumbnail( $post_id, $settings['default_thumb_id'] );
              }
              return;
          }

          $url = html_entity_decode( $matches[1], ENT_QUOTES, 'UTF-8' );
          // 4. 쿼리 파라미터를 뗀 순수 파일명 획득
          $clean_url = strtok($url, '?');
          
          require_once( ABSPATH . 'wp-admin/includes/image.php' );
          require_once( ABSPATH . 'wp-admin/includes/file.php' );
          require_once( ABSPATH . 'wp-admin/includes/media.php' );

          $referer_filter = function( $args, $request_url ) {
              if ( strpos( $request_url, 'pstatic.net' ) !== false || strpos( $request_url, 'naver.com' ) !== false ) {
                  $args['headers']['Referer'] = 'https://blog.naver.com/';
                  $args['headers']['User-Agent'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36';
              }
              return $args;
          };
          add_filter( 'http_request_args', $referer_filter, 10, 2 );
          $tmp = download_url( $clean_url, 15 );
          remove_filter( 'http_request_args', $referer_filter, 10 );

          if ( is_wp_error($tmp) ) return;

          $filename = basename( $clean_url );
          $file_array = [ 'name' => $filename, 'tmp_name' => $tmp ];
          $thumb_id = media_handle_sideload( $file_array, $post_id );
          if ( ! is_wp_error($thumb_id) ) {
              set_post_thumbnail( $post_id, $thumb_id );
          } else {
              @unlink($tmp);
          }
      }
  ```

- [ ] **Step 2: Commit**
  
  ```bash
  git add docs/snippets/naver_rss_sync_ultimate.php
  git commit -m "feat: add sync runner with duplicate checks and sanitization"
  ```

---

### Task 4: 완벽한 SEO 패널티 차단 및 Canonical 주입

**Files:**
- Modify: `docs/snippets/naver_rss_sync_ultimate.php`

- [ ] **Step 1: Canonical 태그 주입 및 noindex robots 필터 로직 탑재**
  
  ```php
  // docs/snippets/naver_rss_sync_ultimate.php 생성자에 후킹 로직 추가 및 함수 구현
      // 생성자 내에 아래 코드 주입
      // add_action( 'wp_head', [ $this, 'inject_canonical_and_noindex' ], 1 );
      // add_filter( 'wp_sitemaps_post_types', [ $this, 'exclude_from_sitemaps' ] );

      public function inject_canonical_and_noindex() {
          if ( is_single() ) {
              global $post;
              $naver_url = get_post_meta( $post->ID, '_naver_original_url', true );
              if ( ! empty($naver_url) ) {
                  // 1. WP 기본 Canonical 비활성화
                  remove_action( 'wp_head', 'rel_canonical' );
                  // 2. Yoast 및 Rank Math 호환
                  add_filter( 'wpseo_canonical', function() use ($naver_url) { return $naver_url; } );
                  add_filter( 'rank_math/frontend/canonical', function() use ($naver_url) { return $naver_url; } );
                  // 3. 직접 출력
                  echo "\n" . '<link rel="canonical" href="' . esc_url($naver_url) . '" />' . "\n";
                  echo '<meta name="robots" content="noindex,nofollow" />' . "\n";
              }
          }
      }

      public function exclude_from_sitemaps( $post_types ) {
          unset( $post_types['naver_blog'] );
          return $post_types;
      }
  ```

- [ ] **Step 2: Commit**
  
  ```bash
  git add docs/snippets/naver_rss_sync_ultimate.php
  git commit -m "feat: add canonical injector and sitemap exclusions for seo"
  ```

---

### Task 5: 하이브리드 아카이브 및 숏코드 렌더링 엔진 구축

**Files:**
- Modify: `docs/snippets/naver_rss_sync_ultimate.php`

- [ ] **Step 1: 숏코드와 설정에 반응하는 분기형 렌더러 추가**
  
  ```php
  // docs/snippets/naver_rss_sync_ultimate.php 생성자에 숏코드 훅 추가 및 렌더링 로직 추가
      // 생성자 내부: add_shortcode( 'naver_rss_archive', [ $this, 'render_archive_shortcode' ] );

      public function render_archive_shortcode( $atts ) {
          $settings = $this->get_settings();
          $atts = shortcode_atts([
              'style' => $settings['archive_style'], // 기본값은 어드민 설정값
              'limit' => 12
          ], $atts, 'naver_rss_archive');

          $post_type = $settings['post_type_selection'];
          $query = new WP_Query([
              'post_type' => $post_type,
              'post_status' => 'publish',
              'posts_per_page' => intval($atts['limit']),
              'meta_key' => '_naver_guid',
              'orderby' => 'date',
              'order' => 'DESC'
          ]);

          if ( ! $query->have_posts() ) {
              return '<p style="text-align:center;color:#8b95a1;">수집된 네이버 글이 없습니다.</p>';
          }

          ob_start();
          if ( $atts['style'] === 'toss' ) {
              $this->render_toss_html( $query );
          } else {
              $this->render_magazine_html( $query );
          }
          wp_reset_postdata();
          return ob_get_clean();
      }

      private function render_toss_html( $query ) {
          // Toss 반응형 카드/리스트 아웃풋 렌더링 (CSS 임베드)
          ?>
          <style>
              .toss-wrap { font-family: -apple-system, sans-serif; color: #191f28; max-width:960px; margin:0 auto; }
              .toss-card { display: flex; gap: 20px; padding: 16px; border-bottom: 1px solid #f2f4f6; text-decoration: none; color: inherit; }
              .toss-thumb { width: 120px; height: 80px; object-fit: cover; border-radius: 8px; background: #f2f4f6; }
              .toss-title { font-size: 16px; font-weight: 700; margin: 0 0 6px 0; }
          </style>
          <div class="toss-wrap">
              <?php while( $query->have_posts() ): $query->the_post(); 
                  $url = get_post_meta(get_the_ID(), '_naver_original_url', true) ?: get_permalink();
                  $thumb = get_the_post_thumbnail_url(get_the_ID(), 'medium') ?: '';
              ?>
              <a href="<?php echo esc_url($url); ?>" class="toss-card" target="_blank" rel="noopener">
                  <?php if($thumb): ?><img src="<?php echo esc_url($thumb); ?>" class="toss-thumb" /><?php endif; ?>
                  <div>
                      <h4 class="toss-title"><?php the_title(); ?></h4>
                      <span style="color:#8b95a1; font-size:12px;"><?php echo get_the_date('Y.m.d'); ?></span>
                  </div>
              </a>
              <?php endwhile; ?>
          </div>
          <?php
      }

      private function render_magazine_html( $query ) {
          // 다크 매거진 그리드 렌더링
          ?>
          <style>
              .mag-wrap { background: #1b1b1d; padding: 24px; border-radius: 12px; display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; }
              .mag-card { background: #050505; border-radius: 8px; overflow: hidden; text-decoration: none; color: #f5f5f5; display: block; }
              .mag-thumb { width: 100%; height: 120px; object-fit: cover; }
              .mag-title { padding: 12px; font-size: 14px; font-weight: bold; margin: 0; }
          </style>
          <div class="mag-wrap">
              <?php while( $query->have_posts() ): $query->the_post(); 
                  $url = get_post_meta(get_the_ID(), '_naver_original_url', true) ?: get_permalink();
                  $thumb = get_the_post_thumbnail_url(get_the_ID(), 'medium') ?: '';
              ?>
              <a href="<?php echo esc_url($url); ?>" class="mag-card" target="_blank" rel="noopener">
                  <?php if($thumb): ?><img src="<?php echo esc_url($thumb); ?>" class="mag-thumb" /><?php endif; ?>
                  <h4 class="mag-title"><?php the_title(); ?></h4>
              </a>
              <?php endwhile; ?>
          </div>
          <?php
      }
  ```

- [ ] **Step 2: Commit**
  
  ```bash
  git add docs/snippets/naver_rss_sync_ultimate.php
  git commit -m "feat: support hybrid shortcode rendering engine"
  ```
