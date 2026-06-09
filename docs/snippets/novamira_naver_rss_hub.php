/**
 * Novamira Naver RSS Hub
 * Code Snippets compatible WordPress snippet.
 */

if (!defined('ABSPATH')) {
    exit;
}

define('NOVAMIRA_NAVER_RSS_OPTION', 'novamira_naver_rss_settings');
define('NOVAMIRA_NAVER_RSS_LOG_OPTION', 'novamira_naver_rss_last_log');
define('NOVAMIRA_NAVER_RSS_CRON', 'novamira_naver_rss_cron_sync');
define('NOVAMIRA_NAVER_RSS_LOCK', 'novamira_naver_rss_sync_lock');
define('NOVAMIRA_NAVER_RSS_CPT', 'naver_blog');

add_action('init', 'novamira_register_naver_blog_cpt');
add_action('init', 'novamira_ensure_naver_rss_cron');
add_action('admin_menu', 'novamira_register_naver_rss_admin_menu');
add_action('admin_init', 'novamira_handle_naver_rss_admin_actions');
add_action('add_meta_boxes', 'novamira_add_naver_blog_meta_box');
add_action('save_post_' . NOVAMIRA_NAVER_RSS_CPT, 'novamira_save_naver_blog_meta_box');
add_action(NOVAMIRA_NAVER_RSS_CRON, 'novamira_sync_naver_rss');
add_shortcode('novamira_naver_blog', 'novamira_render_naver_blog_shortcode');
add_action('wp_head', 'novamira_output_naver_blog_noindex');
add_filter('pre_get_posts', 'novamira_exclude_naver_blog_from_search');
add_filter('the_content', 'novamira_render_naver_blog_single_content');
add_action('template_redirect', 'novamira_render_naver_blog_archive_directly');
add_filter('wp_sitemaps_post_types', 'novamira_remove_naver_blog_from_core_sitemap');
add_filter('wpseo_sitemap_exclude_post_type', 'novamira_remove_naver_blog_from_yoast_sitemap', 10, 2);
add_filter('rank_math/sitemap/exclude_post_type', 'novamira_remove_naver_blog_from_rank_math_sitemap', 10, 2);

function novamira_default_naver_rss_settings() {
    return array(
        'feed_url' => 'https://rss.blog.naver.com/naverofficial.xml',
        'sync_interval' => 'twicedaily',
        'post_status' => 'publish',
        'category_map' => array(),
    );
}

function novamira_get_naver_rss_settings() {
    $saved = get_option(NOVAMIRA_NAVER_RSS_OPTION, array());
    if (!is_array($saved)) {
        $saved = array();
    }
    return wp_parse_args($saved, novamira_default_naver_rss_settings());
}

function novamira_sanitize_naver_feed_url($url) {
    $url = esc_url_raw($url);
    $parts = wp_parse_url($url);

    if (!$parts || empty($parts['scheme']) || empty($parts['host'])) {
        return '';
    }

    if ($parts['scheme'] !== 'https' || $parts['host'] !== 'rss.blog.naver.com') {
        return '';
    }

    if (empty($parts['path']) || !preg_match('/\.xml$/', $parts['path'])) {
        return '';
    }

    return $url;
}

function novamira_register_naver_blog_cpt() {
    register_post_type(NOVAMIRA_NAVER_RSS_CPT, array(
        'labels' => array(
            'name' => '네이버 블로그',
            'singular_name' => '네이버 블로그 글',
            'add_new_item' => '네이버 블로그 글 추가',
            'edit_item' => '네이버 블로그 글 편집',
            'view_item' => '네이버 블로그 글 보기',
            'search_items' => '네이버 블로그 글 검색',
            'not_found' => '네이버 블로그 글이 없습니다',
        ),
        'public' => true,
        'has_archive' => 'naver-blog',
        'rewrite' => array('slug' => 'naver-blog'),
        'exclude_from_search' => true,
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-rss',
        'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'custom-fields'),
        'taxonomies' => array('category'),
    ));
}

function novamira_register_naver_rss_admin_menu() {
    add_menu_page(
        '노바미라 네이버 RSS',
        '노바미라 네이버 RSS',
        'manage_options',
        'novamira-naver-rss',
        'novamira_render_naver_rss_admin_page',
        'dashicons-rss',
        58
    );
}

function novamira_handle_naver_rss_admin_actions() {
    if (!current_user_can('manage_options') || !isset($_POST['novamira_naver_rss_action'])) {
        return;
    }

    check_admin_referer('novamira_naver_rss_admin');
    $action = sanitize_text_field(wp_unslash($_POST['novamira_naver_rss_action']));

    if ($action === 'save') {
        $settings = novamira_get_naver_rss_settings();
        $settings['feed_url'] = isset($_POST['feed_url']) ? novamira_sanitize_naver_feed_url(wp_unslash($_POST['feed_url'])) : '';
        $settings['sync_interval'] = isset($_POST['sync_interval']) ? sanitize_text_field(wp_unslash($_POST['sync_interval'])) : 'twicedaily';
        $settings['post_status'] = isset($_POST['post_status']) && $_POST['post_status'] === 'draft' ? 'draft' : 'publish';
        $settings['category_map'] = novamira_parse_category_map_from_post();
        update_option(NOVAMIRA_NAVER_RSS_OPTION, $settings, false);
        novamira_schedule_naver_rss_sync();
        add_settings_error('novamira_naver_rss', 'saved', '설정을 저장했습니다.', 'updated');
    }

    if ($action === 'sync') {
        $result = novamira_sync_naver_rss();
        add_settings_error(
            'novamira_naver_rss',
            'synced',
            sprintf('동기화 완료: 기존 %d개, 신규 %d개, 실패 %d개', intval($result['existing']), intval($result['created']), intval($result['failed'])),
            empty($result['last_error']) ? 'updated' : 'error'
        );
    }
}

function novamira_parse_category_map_from_post() {
    $map = array();
    $naver_categories = isset($_POST['map_naver_category']) ? (array) $_POST['map_naver_category'] : array();
    $wp_categories = isset($_POST['map_wp_category']) ? (array) $_POST['map_wp_category'] : array();

    foreach ($naver_categories as $index => $naver_category) {
        $naver_category = sanitize_text_field(wp_unslash($naver_category));
        $wp_category_id = isset($wp_categories[$index]) ? absint($wp_categories[$index]) : 0;
        if ($naver_category !== '' && $wp_category_id > 0) {
            $map[$naver_category] = $wp_category_id;
        }
    }

    return $map;
}

function novamira_schedule_naver_rss_sync() {
    $settings = novamira_get_naver_rss_settings();
    $interval = in_array($settings['sync_interval'], array('hourly', 'twicedaily', 'daily'), true) ? $settings['sync_interval'] : 'twicedaily';
    $next = wp_next_scheduled(NOVAMIRA_NAVER_RSS_CRON);

    if ($next) {
        wp_unschedule_event($next, NOVAMIRA_NAVER_RSS_CRON);
    }

    wp_schedule_event(time() + 300, $interval, NOVAMIRA_NAVER_RSS_CRON);
}

function novamira_ensure_naver_rss_cron() {
    if (!wp_next_scheduled(NOVAMIRA_NAVER_RSS_CRON)) {
        novamira_schedule_naver_rss_sync();
    }
}

function novamira_render_naver_rss_admin_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $settings = novamira_get_naver_rss_settings();
    $log = get_option(NOVAMIRA_NAVER_RSS_LOG_OPTION, array());
    $log = is_array($log) ? $log : array();
    $categories = get_categories(array('hide_empty' => false));

    settings_errors('novamira_naver_rss');
    ?>
    <div class="wrap novamira-rss-console">
        <h1>노바미라 네이버 RSS</h1>
        <style>
            .novamira-rss-console .novamira-layout{display:grid;grid-template-columns:240px 1fr;background:#f6f7f7;border:1px solid #dcdcde;border-radius:12px;overflow:hidden;max-width:1180px}
            .novamira-rss-console .novamira-side{background:#101517;color:#fff;padding:22px}
            .novamira-rss-console .novamira-side-title{font-size:26px;font-weight:800;line-height:1.15;margin:8px 0 28px}
            .novamira-rss-console .novamira-side-item{padding:10px 12px;border-radius:8px;margin-bottom:8px;color:#cbd5dc}
            .novamira-rss-console .novamira-side-item.active{background:#243036;color:#fff}
            .novamira-rss-console .novamira-main{padding:24px 28px}
            .novamira-rss-console .novamira-top{display:flex;justify-content:space-between;gap:16px;align-items:flex-start;margin-bottom:22px}
            .novamira-rss-console .novamira-title{font-size:30px;font-weight:800;letter-spacing:-.02em}
            .novamira-rss-console .novamira-subtitle{color:#64748b;margin-top:5px}
            .novamira-rss-console .novamira-grid{display:grid;grid-template-columns:1.45fr .8fr;gap:18px}
            .novamira-rss-console .novamira-panel{background:#fff;border:1px solid #dcdcde;border-radius:10px;padding:18px}
            .novamira-rss-console .novamira-status{background:#101517;color:#fff;border-radius:10px;padding:18px}
            .novamira-rss-console .novamira-status-number{font-size:34px;font-weight:800;margin:8px 0}
            .novamira-rss-console .novamira-field{margin-bottom:14px}
            .novamira-rss-console .novamira-field label{display:block;font-size:12px;color:#646970;margin-bottom:6px}
            .novamira-rss-console .novamira-field input,.novamira-rss-console .novamira-field select{width:100%;max-width:100%}
            .novamira-rss-console .novamira-map-row{display:grid;grid-template-columns:1fr 1fr 90px;gap:8px;margin-bottom:8px;align-items:center}
            .novamira-rss-console .novamira-wide{grid-column:1 / -1}
        </style>
        <div class="novamira-layout">
            <aside class="novamira-side">
                <div style="font-size:13px;color:#9ca3a8;">NOVAMIRA</div>
                <div class="novamira-side-title">네이버 RSS<br>콘솔</div>
                <div class="novamira-side-item active">피드 설정</div>
                <div class="novamira-side-item">카테고리 매핑</div>
                <div class="novamira-side-item">동기화 기록</div>
                <div class="novamira-side-item">표시 설정</div>
            </aside>
            <main class="novamira-main">
                <form method="post">
                    <?php wp_nonce_field('novamira_naver_rss_admin'); ?>
                    <div class="novamira-top">
                        <div>
                            <div class="novamira-title">피드 설정</div>
                            <div class="novamira-subtitle">네이버 블로그 RSS 글을 검색엔진 비노출 매거진으로 가져옵니다.</div>
                        </div>
                        <div>
                            <button class="button" name="novamira_naver_rss_action" value="save">설정 저장</button>
                            <button class="button button-primary" name="novamira_naver_rss_action" value="sync">바로 동기화</button>
                        </div>
                    </div>
                    <div class="novamira-grid">
                        <section class="novamira-panel">
                            <h2>RSS 원본</h2>
                            <div class="novamira-field">
                                <label for="feed_url">피드 URL</label>
                                <input id="feed_url" name="feed_url" type="url" value="<?php echo esc_attr($settings['feed_url']); ?>">
                            </div>
                            <div class="novamira-field">
                                <label for="sync_interval">자동 동기화</label>
                                <select id="sync_interval" name="sync_interval">
                                    <option value="hourly" <?php selected($settings['sync_interval'], 'hourly'); ?>>1시간마다</option>
                                    <option value="twicedaily" <?php selected($settings['sync_interval'], 'twicedaily'); ?>>12시간마다</option>
                                    <option value="daily" <?php selected($settings['sync_interval'], 'daily'); ?>>하루 한 번</option>
                                </select>
                            </div>
                            <div class="novamira-field">
                                <label for="post_status">가져온 글 상태</label>
                                <select id="post_status" name="post_status">
                                    <option value="publish" <?php selected($settings['post_status'], 'publish'); ?>>공개, noindex</option>
                                    <option value="draft" <?php selected($settings['post_status'], 'draft'); ?>>임시글</option>
                                </select>
                            </div>
                        </section>
                        <section class="novamira-status">
                            <div style="font-size:13px;color:#9ca3a8;">최근 동기화</div>
                            <div class="novamira-status-number"><?php echo esc_html(intval($log['total'] ?? 0)); ?>개 글</div>
                            <div style="color:#cbd5dc;">기존 <?php echo esc_html(intval($log['existing'] ?? 0)); ?>개, 신규 <?php echo esc_html(intval($log['created'] ?? 0)); ?>개, 실패 <?php echo esc_html(intval($log['failed'] ?? 0)); ?>개</div>
                            <hr style="border-color:#2f3b42;margin:18px 0;">
                            <div>최근 실행: <?php echo esc_html($log['last_synced_at'] ?? '기록 없음'); ?></div>
                            <div>피드 상태: <?php echo empty($log['last_error']) ? '정상' : '오류'; ?></div>
                            <?php if (!empty($log['last_error'])) : ?>
                                <p><?php echo esc_html($log['last_error']); ?></p>
                            <?php endif; ?>
                        </section>
                        <section class="novamira-panel novamira-wide">
                            <h2>카테고리 매핑</h2>
                            <p>네이버 카테고리를 워드프레스 카테고리에 연결합니다. 매핑이 없으면 네이버 카테고리명으로 자동 생성합니다.</p>
                            <?php novamira_render_category_mapping_rows($settings, $categories); ?>
                        </section>
                        <section class="novamira-panel novamira-wide">
                            <h2>표시 설정</h2>
                            <p>아카이브 URL: <code><?php echo esc_html(home_url('/naver-blog/')); ?></code></p>
                            <p>쇼트코드: <code>[novamira_naver_blog]</code></p>
                        </section>
                    </div>
                </form>
            </main>
        </div>
    </div>
    <?php
}

function novamira_render_category_mapping_rows($settings, $categories) {
    $map = is_array($settings['category_map']) ? $settings['category_map'] : array();
    $rows = max(3, count($map) + 1);
    $map_keys = array_keys($map);

    for ($i = 0; $i < $rows; $i++) {
        $naver_value = $map_keys[$i] ?? '';
        $wp_value = $naver_value !== '' ? intval($map[$naver_value]) : 0;
        ?>
        <div class="novamira-map-row">
            <input name="map_naver_category[]" type="text" placeholder="네이버 카테고리" value="<?php echo esc_attr($naver_value); ?>">
            <select name="map_wp_category[]">
                <option value="0">워드프레스 카테고리 선택</option>
                <?php foreach ($categories as $category) : ?>
                    <option value="<?php echo esc_attr($category->term_id); ?>" <?php selected($wp_value, $category->term_id); ?>>
                        <?php echo esc_html($category->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span><?php echo $naver_value === '' ? '대기' : '매핑됨'; ?></span>
        </div>
        <?php
    }
}

function novamira_sync_naver_rss() {
    if (get_transient(NOVAMIRA_NAVER_RSS_LOCK)) {
        return array('existing' => 0, 'created' => 0, 'failed' => 0, 'total' => 0, 'last_error' => '이미 동기화가 실행 중입니다.');
    }

    set_transient(NOVAMIRA_NAVER_RSS_LOCK, 1, 10 * MINUTE_IN_SECONDS);
    $result = array('existing' => 0, 'created' => 0, 'failed' => 0, 'total' => 0, 'last_error' => '');

    try {
        $settings = novamira_get_naver_rss_settings();
        $feed_url = novamira_sanitize_naver_feed_url($settings['feed_url']);

        if ($feed_url === '') {
            $result['last_error'] = 'RSS URL을 입력해 주세요. 네이버 블로그 RSS 주소만 사용할 수 있습니다.';
            delete_transient(NOVAMIRA_NAVER_RSS_LOCK);
            return novamira_finish_sync_result($result);
        }

        $response = wp_remote_get($feed_url, array(
            'timeout' => 20,
            'limit_response_size' => 5 * MB_IN_BYTES,
        ));
        if (is_wp_error($response)) {
            $result['last_error'] = $response->get_error_message();
            delete_transient(NOVAMIRA_NAVER_RSS_LOCK);
            return novamira_finish_sync_result($result);
        }

        $code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        if ($code < 200 || $code >= 300 || trim($body) === '') {
            $result['last_error'] = 'RSS 요청 실패: HTTP ' . intval($code);
            delete_transient(NOVAMIRA_NAVER_RSS_LOCK);
            return novamira_finish_sync_result($result);
        }

        if (strlen($body) > 5 * MB_IN_BYTES) {
            $result['last_error'] = 'RSS 응답이 너무 큽니다.';
            delete_transient(NOVAMIRA_NAVER_RSS_LOCK);
            return novamira_finish_sync_result($result);
        }

        $previous_libxml_state = libxml_use_internal_errors(true);
        $xml = simplexml_load_string($body);
        libxml_clear_errors();
        libxml_use_internal_errors($previous_libxml_state);
        if (!$xml || empty($xml->channel->item)) {
            $result['last_error'] = 'RSS 형식을 읽을 수 없습니다.';
            delete_transient(NOVAMIRA_NAVER_RSS_LOCK);
            return novamira_finish_sync_result($result);
        }

        foreach ($xml->channel->item as $item) {
            $result['total']++;
            $imported = novamira_import_naver_rss_item($item, $settings, $feed_url);
            if ($imported === 'created') {
                $result['created']++;
            } elseif ($imported === 'existing') {
                $result['existing']++;
            } else {
                $result['failed']++;
            }
        }
    } catch (Exception $exception) {
        $result['last_error'] = $exception->getMessage();
    }

    delete_transient(NOVAMIRA_NAVER_RSS_LOCK);
    return novamira_finish_sync_result($result);
}

function novamira_finish_sync_result($result) {
    $result['last_synced_at'] = current_time('mysql');
    update_option(NOVAMIRA_NAVER_RSS_LOG_OPTION, $result, false);
    return $result;
}

function novamira_import_naver_rss_item($item, $settings, $feed_url) {
    $guid = sanitize_text_field(novamira_xml_text($item->guid));
    $url = novamira_xml_text($item->link);
    $title = novamira_xml_text($item->title);
    $summary = wp_strip_all_tags(novamira_xml_text($item->description));
    $category = novamira_xml_text($item->category);
    $pub_date = novamira_xml_text($item->pubDate);
    $image = novamira_extract_image_from_item($item);

    if ($url === '' || $title === '') {
        return 'failed';
    }

    $post_id = novamira_find_naver_blog_post($guid, $url);
    $timestamp = $pub_date !== '' ? strtotime($pub_date) : false;
    $post_date = $timestamp ? date('Y-m-d H:i:s', $timestamp) : current_time('mysql');
    $post_data = array(
        'post_type' => NOVAMIRA_NAVER_RSS_CPT,
        'post_status' => $settings['post_status'] === 'draft' ? 'draft' : 'publish',
        'post_title' => $title,
        'post_excerpt' => wp_trim_words($summary, 42, '...'),
        'post_content' => wpautop(esc_html(wp_trim_words($summary, 90, '...'))),
        'post_date' => $post_date,
    );

    if ($post_id > 0) {
        $post_data['ID'] = $post_id;
        $saved_id = wp_update_post($post_data, true);
        $state = 'existing';
    } else {
        $saved_id = wp_insert_post($post_data, true);
        $state = 'created';
    }

    if (is_wp_error($saved_id) || intval($saved_id) <= 0) {
        return 'failed';
    }

    update_post_meta($saved_id, '_novamira_naver_guid', $guid);
    update_post_meta($saved_id, '_novamira_naver_url', esc_url_raw($url));
    update_post_meta($saved_id, '_novamira_naver_category', sanitize_text_field($category));
    update_post_meta($saved_id, '_novamira_naver_synced_at', current_time('mysql'));
    update_post_meta($saved_id, '_novamira_naver_source_feed', esc_url_raw($feed_url));
    update_post_meta($saved_id, '_novamira_naver_image', esc_url_raw($image));
    novamira_assign_naver_category($saved_id, $category, $settings);

    if ($image !== '' && !get_post_thumbnail_id($saved_id)) {
        novamira_download_and_attach_image($image, $saved_id);
    }

    return $state;
}

function novamira_xml_text($value) {
    return trim((string) $value);
}

function novamira_find_naver_blog_post($guid, $url) {
    $meta_queries = array('relation' => 'OR');

    if ($guid !== '') {
        $meta_queries[] = array('key' => '_novamira_naver_guid', 'value' => $guid, 'compare' => '=');
    }

    if ($url !== '') {
        $meta_queries[] = array('key' => '_novamira_naver_url', 'value' => $url, 'compare' => '=');
    }

    if (count($meta_queries) === 1) {
        return 0;
    }

    $posts = get_posts(array(
        'post_type' => NOVAMIRA_NAVER_RSS_CPT,
        'post_status' => array('publish', 'draft', 'private'),
        'posts_per_page' => 1,
        'fields' => 'ids',
        'meta_query' => $meta_queries,
    ));

    return empty($posts) ? 0 : intval($posts[0]);
}

function novamira_extract_image_from_item($item) {
    $description = novamira_xml_text($item->description);
    if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $description, $matches)) {
        return esc_url_raw(html_entity_decode($matches[1]));
    }

    $namespaces = $item->getNameSpaces(true);
    foreach ($namespaces as $namespace) {
        $children = $item->children($namespace);
        foreach (array('thumbnail', 'content') as $node) {
            if (isset($children->{$node})) {
                $attributes = $children->{$node}->attributes();
                if (isset($attributes['url'])) {
                    return esc_url_raw((string) $attributes['url']);
                }
            }
        }
    }

    return '';
}

function novamira_assign_naver_category($post_id, $naver_category, $settings) {
    $naver_category = sanitize_text_field($naver_category);
    if ($naver_category === '') {
        return;
    }

    $category_id = !empty($settings['category_map'][$naver_category]) ? absint($settings['category_map'][$naver_category]) : 0;
    if ($category_id <= 0) {
        $term = term_exists($naver_category, 'category');
        if (!$term) {
            $term = wp_insert_term($naver_category, 'category');
        }
        if (!is_wp_error($term)) {
            $category_id = is_array($term) ? absint($term['term_id']) : absint($term);
        }
    }

    if ($category_id > 0) {
        wp_set_post_terms($post_id, array($category_id), 'category', false);
    }
}

function novamira_add_naver_blog_meta_box() {
    add_meta_box('novamira_naver_blog_link', '네이버 원문 링크', 'novamira_render_naver_blog_meta_box', NOVAMIRA_NAVER_RSS_CPT, 'side', 'high');
}

function novamira_render_naver_blog_meta_box($post) {
    wp_nonce_field('novamira_naver_blog_meta', 'novamira_naver_blog_meta_nonce');
    $url = get_post_meta($post->ID, '_novamira_naver_url', true);
    $category = get_post_meta($post->ID, '_novamira_naver_category', true);
    ?>
    <p><label for="novamira_naver_url">원문 URL</label><input id="novamira_naver_url" name="novamira_naver_url" type="url" value="<?php echo esc_attr($url); ?>" style="width:100%;"></p>
    <p><label for="novamira_naver_category">네이버 카테고리</label><input id="novamira_naver_category" name="novamira_naver_category" type="text" value="<?php echo esc_attr($category); ?>" style="width:100%;"></p>
    <?php if ($url) : ?>
        <p><a class="button" href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener">네이버 원문 열기</a></p>
    <?php endif; ?>
    <?php
}

function novamira_save_naver_blog_meta_box($post_id) {
    if (!isset($_POST['novamira_naver_blog_meta_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['novamira_naver_blog_meta_nonce'])), 'novamira_naver_blog_meta')) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (isset($_POST['novamira_naver_url'])) {
        update_post_meta($post_id, '_novamira_naver_url', esc_url_raw(wp_unslash($_POST['novamira_naver_url'])));
    }
    if (isset($_POST['novamira_naver_category'])) {
        update_post_meta($post_id, '_novamira_naver_category', sanitize_text_field(wp_unslash($_POST['novamira_naver_category'])));
    }
}

function novamira_get_naver_blog_posts($limit = 12) {
    return get_posts(array(
        'post_type' => NOVAMIRA_NAVER_RSS_CPT,
        'post_status' => 'publish',
        'posts_per_page' => max(1, min(48, absint($limit))),
        'orderby' => 'date',
        'order' => 'DESC',
    ));
}

function novamira_render_naver_blog_cards($posts) {
    ob_start();
    ?>
    <div class="novamira-magazine">
        <style>
            .novamira-magazine{background:#1b1b1d;color:#f5f5f5;border-radius:20px;padding:clamp(22px,4vw,48px);font-family:inherit}
            .novamira-magazine *{box-sizing:border-box}.novamira-hero{background:#000;border-radius:24px;padding:clamp(34px,6vw,72px);display:grid;grid-template-columns:1fr 1.15fr;gap:46px;align-items:center;margin-bottom:24px}
            .novamira-hero-mark{font-size:clamp(60px,10vw,116px);line-height:1;font-weight:900}.novamira-hero h2{font-size:clamp(28px,4vw,48px);line-height:1.15;margin:0 0 14px;color:#fff}
            .novamira-hero p{font-size:16px;line-height:1.8;color:#aaa;margin:0}.novamira-section-title{font-size:clamp(32px,5vw,56px);font-weight:950;letter-spacing:-.03em;margin:0 0 24px}
            .novamira-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:24px}.novamira-card{text-decoration:none;color:#f5f5f5;display:block;transition:transform .2s ease}
            .novamira-card:hover{transform:translateY(-4px)}
            .novamira-card-image{background:#050505;border-radius:16px;aspect-ratio:1.45/1;display:flex;align-items:center;justify-content:center;overflow:hidden;margin-bottom:14px}
            .novamira-card-image img{width:100%;height:100%;object-fit:cover;display:block}.novamira-card-title{font-size:20px;line-height:1.38;font-weight:800;margin:0 0 9px;color:#fff;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;text-overflow:ellipsis}
            .novamira-card-meta{color:#8b8b8b;line-height:1.6;font-size:14px}@media (max-width:900px){.novamira-hero{grid-template-columns:1fr}.novamira-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media (max-width:560px){.novamira-grid{grid-template-columns:1fr}.novamira-magazine{border-radius:0}.novamira-hero{border-radius:18px}}
        </style>
        <section class="novamira-hero"><div class="novamira-hero-mark">N</div><div><h2>네이버 블로그 아카이브</h2><p>네이버 블로그에 올라온 글을 한 곳에서 확인합니다. 글을 선택하면 네이버 원문으로 이동합니다.</p></div></section>
        <h2 class="novamira-section-title">매거진</h2>
        <div class="novamira-grid">
            <?php foreach ($posts as $post) : ?>
                <?php
                $url = get_post_meta($post->ID, '_novamira_naver_url', true);
                $thumbnail_id = get_post_thumbnail_id($post->ID);
                $image_url = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'medium_large') : get_post_meta($post->ID, '_novamira_naver_image', true);
                $post_cats = get_the_category($post->ID);
                $category_display = !empty($post_cats) ? $post_cats[0]->name : '네이버 블로그';
                ?>
                <a class="novamira-card" href="<?php echo esc_url($url ? $url : get_permalink($post)); ?>" target="_blank" rel="noopener nofollow">
                    <div class="novamira-card-image">
                        <?php if ($image_url) : ?>
                            <img src="<?php echo esc_url($image_url); ?>" alt="">
                        <?php else : ?>
                            <span style="font-size:34px;font-weight:900;">NAVER</span>
                        <?php endif; ?>
                    </div>
                    <h3 class="novamira-card-title"><?php echo esc_html(get_the_title($post)); ?></h3>
                    <div class="novamira-card-meta"><?php echo esc_html(get_the_date('y. m. d', $post)); ?><br><?php echo esc_html($category_display); ?></div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

function novamira_render_naver_blog_shortcode($atts) {
    $atts = shortcode_atts(array('limit' => 12), $atts, 'novamira_naver_blog');
    return novamira_render_naver_blog_cards(novamira_get_naver_blog_posts(absint($atts['limit'])));
}

function novamira_render_naver_blog_single_content($content) {
    if (!is_singular(NOVAMIRA_NAVER_RSS_CPT) || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    $url = get_post_meta(get_the_ID(), '_novamira_naver_url', true);
    $category = get_post_meta(get_the_ID(), '_novamira_naver_category', true);
    ob_start();
    ?>
    <div class="novamira-single">
        <?php echo wp_kses_post($content); ?>
        <p>카테고리: <?php echo esc_html($category ? $category : '네이버 블로그'); ?></p>
        <?php if ($url) : ?>
            <p><a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener nofollow">네이버 원문 보러가기</a></p>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

function novamira_render_naver_blog_archive_directly() {
    if (!is_post_type_archive(NOVAMIRA_NAVER_RSS_CPT)) {
        return;
    }

    status_header(200);
    nocache_headers();
    get_header();

    $paged = get_query_var('paged') ? intval(get_query_var('paged')) : (get_query_var('page') ? intval(get_query_var('page')) : 1);
    $naver_cat = isset($_GET['naver_cat']) ? sanitize_text_field(wp_unslash($_GET['naver_cat'])) : '';
    $naver_s = isset($_GET['naver_s']) ? sanitize_text_field(wp_unslash($_GET['naver_s'])) : '';

    $args = array(
        'post_type' => NOVAMIRA_NAVER_RSS_CPT,
        'post_status' => 'publish',
        'posts_per_page' => 12,
        'paged' => $paged,
        'orderby' => 'date',
        'order' => 'DESC',
    );
    if ($naver_cat !== '') {
        $args['category_name'] = $naver_cat;
    }
    if ($naver_s !== '') {
        $args['s'] = $naver_s;
    }

    $query = new WP_Query($args);
    $categories = get_terms(array(
        'taxonomy' => 'category',
        'hide_empty' => true,
    ));

    echo novamira_render_naver_blog_archive_content($query, $categories, $naver_cat, $naver_s);
    get_footer();
    exit;
}

function novamira_render_naver_blog_archive_content($query, $categories, $naver_cat, $naver_s) {
    ob_start();
    ?>
    <div class="novamira-magazine">
        <style>
            .novamira-magazine{background:#1b1b1d;color:#f5f5f5;border-radius:20px;padding:clamp(22px,4vw,48px);font-family:inherit}
            .novamira-magazine *{box-sizing:border-box}
            .novamira-hero{background:#000;border-radius:24px;padding:clamp(34px,6vw,72px);display:grid;grid-template-columns:1fr 1.15fr;gap:46px;align-items:center;margin-bottom:24px}
            .novamira-hero-mark{font-size:clamp(60px,10vw,116px);line-height:1;font-weight:900}
            .novamira-hero h2{font-size:clamp(28px,4vw,48px);line-height:1.15;margin:0 0 14px;color:#fff}
            .novamira-hero p{font-size:16px;line-height:1.8;color:#aaa;margin:0}
            
            /* Search and Category Tabs Styles */
            .novamira-filters{margin-bottom:32px;display:flex;flex-direction:column;gap:20px}
            .novamira-cat-tabs{display:flex;flex-wrap:wrap;gap:10px;border-bottom:1px solid #2f3b42;padding-bottom:16px}
            .novamira-cat-tab{padding:8px 16px;font-size:14px;font-weight:600;border-radius:20px;background:#243036;color:#cbd5dc;text-decoration:none;transition:all .2s ease}
            .novamira-cat-tab:hover{background:#2f3b42;color:#fff}
            .novamira-cat-tab.active{background:#3182f6;color:#fff}
            
            .novamira-search-box{display:flex;gap:10px;max-width:480px;width:100%}
            .novamira-search-box input{flex:1;background:#243036;border:1px solid #2f3b42;color:#fff;border-radius:12px;padding:12px 16px;font-size:14px}
            .novamira-search-box input::placeholder{color:#64748b}
            .novamira-search-box input:focus{outline:none;border-color:#3182f6}
            .novamira-search-box button{background:#3182f6;color:#fff;border:none;border-radius:12px;padding:0 24px;font-weight:600;cursor:pointer;transition:background .2s ease}
            .novamira-search-box button:hover{background:#1b64da}
            
            .novamira-section-title{font-size:clamp(32px,5vw,56px);font-weight:950;letter-spacing:-.03em;margin:0 0 24px}
            .novamira-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:24px}
            .novamira-card{text-decoration:none;color:#f5f5f5;display:block;transition:transform .2s ease}
            .novamira-card:hover{transform:translateY(-4px)}
            .novamira-card-image{background:#050505;border-radius:16px;aspect-ratio:1.45/1;display:flex;align-items:center;justify-content:center;overflow:hidden;margin-bottom:14px}
            .novamira-card-image img{width:100%;height:100%;object-fit:cover;display:block}
            .novamira-card-title{font-size:20px;line-height:1.38;font-weight:800;margin:0 0 9px;color:#fff;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;text-overflow:ellipsis}
            .novamira-card-meta{color:#8b8b8b;line-height:1.6;font-size:14px}
            
            /* Pagination Styles */
            .novamira-pagination{display:flex;justify-content:center;gap:8px;margin-top:48px}
            .novamira-pagination .page-numbers{display:inline-flex;align-items:center;justify-content:center;min-width:40px;height:40px;padding:0 6px;font-size:15px;font-weight:600;color:#cbd5dc;background:#243036;border-radius:50%;text-decoration:none;transition:all 0.2s ease}
            .novamira-pagination .page-numbers:hover{background:#2f3b42;color:#fff}
            .novamira-pagination .page-numbers.current{background:#3182f6;color:#fff}
            .novamira-pagination .page-numbers.prev,
            .novamira-pagination .page-numbers.next{border-radius:8px;padding:0 12px}
            
            .novamira-no-results{padding:48px 0;text-align:center;color:#8b8b8b;font-size:16px}
            
            @media (max-width:900px){.novamira-hero{grid-template-columns:1fr}.novamira-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
            @media (max-width:560px){.novamira-grid{grid-template-columns:1fr}.novamira-magazine{border-radius:0}.novamira-hero{border-radius:18px}}
        </style>
        <section class="novamira-hero">
            <div class="novamira-hero-mark">N</div>
            <div>
                <h2>네이버 블로그 아카이브</h2>
                <p>네이버 블로그에 올라온 글을 한 곳에서 확인합니다. 글을 선택하면 네이버 원문으로 이동합니다.</p>
            </div>
        </section>
        
        <div class="novamira-filters">
            <?php if (!empty($categories)) : ?>
                <div class="novamira-cat-tabs">
                    <a href="<?php echo esc_url(remove_query_arg(array('naver_cat', 'paged'))); ?>" class="novamira-cat-tab <?php echo ($naver_cat === '') ? 'active' : ''; ?>">전체</a>
                    <?php foreach ($categories as $cat) : ?>
                        <?php
                        $is_active = ($naver_cat === $cat->slug);
                        $cat_url = add_query_arg(array('naver_cat' => $cat->slug, 'paged' => 1));
                        ?>
                        <a href="<?php echo esc_url($cat_url); ?>" class="novamira-cat-tab <?php echo $is_active ? 'active' : ''; ?>"><?php echo esc_html($cat->name); ?></a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <form class="novamira-search-box" method="get" action="">
                <?php if ($naver_cat !== '') : ?>
                    <input type="hidden" name="naver_cat" value="<?php echo esc_attr($naver_cat); ?>">
                <?php endif; ?>
                <input type="text" name="naver_s" placeholder="검색어를 입력해 주세요..." value="<?php echo esc_attr($naver_s); ?>">
                <button type="submit">검색</button>
            </form>
        </div>

        <h2 class="novamira-section-title">
            <?php echo ($naver_s !== '') ? '검색 결과' : '매거진'; ?>
        </h2>
        
        <?php if ($query->have_posts()) : ?>
            <div class="novamira-grid">
                <?php while ($query->have_posts()) : $query->the_post(); ?>
                    <?php
                    $post = get_post();
                    $url = get_post_meta($post->ID, '_novamira_naver_url', true);
                    $thumbnail_id = get_post_thumbnail_id($post->ID);
                    $image_url = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'medium_large') : get_post_meta($post->ID, '_novamira_naver_image', true);
                    $post_cats = get_the_category($post->ID);
                    $category_display = !empty($post_cats) ? $post_cats[0]->name : '네이버 블로그';
                    ?>
                    <a class="novamira-card" href="<?php echo esc_url($url ? $url : get_permalink($post)); ?>" target="_blank" rel="noopener nofollow">
                        <div class="novamira-card-image">
                            <?php if ($image_url) : ?>
                                <img src="<?php echo esc_url($image_url); ?>" alt="">
                            <?php else : ?>
                                <span style="font-size:34px;font-weight:900;">NAVER</span>
                            <?php endif; ?>
                        </div>
                        <h3 class="novamira-card-title"><?php echo esc_html(get_the_title($post)); ?></h3>
                        <div class="novamira-card-meta"><?php echo esc_html(get_the_date('y. m. d', $post)); ?><br><?php echo esc_html($category_display); ?></div>
                    </a>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
            
            <?php
            $pagination = paginate_links(array(
                'base' => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
                'format' => '?paged=%#%',
                'current' => max(1, $query->query_vars['paged']),
                'total' => $query->max_num_pages,
                'prev_text' => '이전',
                'next_text' => '다음',
                'type' => 'plain',
            ));
            if ($pagination) {
                echo '<div class="novamira-pagination">' . $pagination . '</div>';
            }
            ?>
        <?php else : ?>
            <div class="novamira-no-results">검색 결과가 없습니다.</div>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

function novamira_output_naver_blog_noindex() {
    if (is_singular(NOVAMIRA_NAVER_RSS_CPT) || is_post_type_archive(NOVAMIRA_NAVER_RSS_CPT)) {
        echo "<meta name=\"robots\" content=\"noindex,nofollow\">\n";
    }
}

function novamira_exclude_naver_blog_from_search($query) {
    if (is_admin() || !$query->is_main_query() || !$query->is_search()) {
        return;
    }

    $post_type = $query->get('post_type');
    if (empty($post_type) || $post_type === 'any') {
        $query->set('post_type', array('post', 'page'));
        return;
    }

    if (is_array($post_type)) {
        $query->set('post_type', array_diff($post_type, array(NOVAMIRA_NAVER_RSS_CPT)));
    }
}

function novamira_remove_naver_blog_from_core_sitemap($post_types) {
    unset($post_types[NOVAMIRA_NAVER_RSS_CPT]);
    return $post_types;
}

function novamira_remove_naver_blog_from_yoast_sitemap($exclude, $post_type) {
    return $post_type === NOVAMIRA_NAVER_RSS_CPT ? true : $exclude;
}

function novamira_remove_naver_blog_from_rank_math_sitemap($exclude, $post_type) {
    return $post_type === NOVAMIRA_NAVER_RSS_CPT ? true : $exclude;
}

function novamira_download_and_attach_image($url, $post_id) {
    if (empty($url)) {
        return;
    }

    require_once(ABSPATH . 'wp-admin/includes/image.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');

    $referer_filter = function($args, $request_url) {
        if (strpos($request_url, 'pstatic.net') !== false || strpos($request_url, 'naver.com') !== false) {
            $args['headers']['Referer'] = 'https://blog.naver.com/';
            $args['headers']['User-Agent'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';
        }
        return $args;
    };
    add_filter('http_request_args', $referer_filter, 10, 2);

    $tmp = download_url($url, 15);

    remove_filter('http_request_args', $referer_filter, 10);

    if (is_wp_error($tmp)) {
        return;
    }

    $clean_url = strtok($url, '?');
    $filename = basename($clean_url);
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    $name = pathinfo($filename, PATHINFO_FILENAME);

    if (empty($ext) || !in_array(strtolower($ext), array('jpg', 'jpeg', 'gif', 'png', 'webp'), true)) {
        $ext = 'jpg';
    }

    $unique_filename = sanitize_file_name($name . '-' . $post_id . '-' . time() . '.' . $ext);

    $file_array = array(
        'name'     => $unique_filename,
        'tmp_name' => $tmp
    );

    $thumb_id = media_handle_sideload($file_array, $post_id);
    if (!is_wp_error($thumb_id)) {
        set_post_thumbnail($post_id, $thumb_id);
    } else {
        @unlink($tmp);
    }
}
