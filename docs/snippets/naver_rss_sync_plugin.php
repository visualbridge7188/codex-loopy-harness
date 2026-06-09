/**
 * Snippet Name: Naver RSS Sync Plugin
 * Description: Naver Blog RSS Sync and Admin Settings with Toss-style Archive Page
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Naver_RSS_Sync' ) ) {
class Naver_RSS_Sync {
    private static $instance = null;
    private $option_name = 'naver_rss_sync_settings';

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'init', array( $this, 'init_plugin' ) );
        add_action( 'naver_rss_cron_sync_event', array( $this, 'sync_rss_feed' ) );
        add_filter( 'wp_robots', array( $this, 'add_robots_directives' ) );
        add_action( 'template_redirect', array( $this, 'redirect_to_naver_original' ) );
        add_filter( 'post_link', array( $this, 'override_post_link' ), 10, 2 );
        add_action( 'add_meta_boxes', array( $this, 'add_naver_link_metabox' ) );
        add_action( 'save_post', array( $this, 'save_naver_link_metabox' ) );
        add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
        add_action( 'wp_ajax_naver_rss_sync_now', array( $this, 'ajax_sync_rss' ) );
        add_filter( 'template_include', array( $this, 'override_blog_archive_template' ) );
    }

    public function init_plugin() {
        // Fallback safety check on init (lightweight checking)
        $settings = $this->get_settings();
        $interval = isset( $settings['sync_interval'] ) ? $settings['sync_interval'] : 'hourly';
        if ( $interval !== 'manual' ) {
            if ( ! wp_next_scheduled( 'naver_rss_cron_sync_event' ) ) {
                wp_schedule_event( time(), $interval, 'naver_rss_cron_sync_event' );
            }
        } else {
            if ( wp_next_scheduled( 'naver_rss_cron_sync_event' ) ) {
                wp_clear_scheduled_hook( 'naver_rss_cron_sync_event' );
            }
        }
    }

    public function get_settings() {
        $default = array(
            'rss_url' => 'https://rss.blog.naver.com/naverofficial.xml',
            'sync_interval' => 'hourly',
            'category_mapping' => array()
        );
        return get_option( $this->option_name, $default );
    }

    public function update_settings( $new_settings ) {
        $old_settings = $this->get_settings();
        $new_settings = array_merge( $old_settings, $new_settings );
        
        // Reschedule cron if sync interval changed
        if ( $old_settings['sync_interval'] !== $new_settings['sync_interval'] ) {
            $this->reschedule_cron( $new_settings['sync_interval'] );
        }
        
        update_option( $this->option_name, $new_settings );
    }

    public function reschedule_cron( $new_interval ) {
        wp_clear_scheduled_hook( 'naver_rss_cron_sync_event' );
        if ( $new_interval !== 'manual' ) {
            wp_schedule_event( time(), $new_interval, 'naver_rss_cron_sync_event' );
        }
    }

    public function sync_rss_feed() {
        $settings = $this->get_settings();
        $rss_url = isset( $settings['rss_url'] ) ? $settings['rss_url'] : '';
        if ( empty( $rss_url ) ) {
            return new WP_Error( 'empty_url', 'RSS URL이 지정되지 않았습니다.' );
        }

        $response = wp_remote_get( $rss_url, array( 'timeout' => 15 ) );
        if ( is_wp_error( $response ) ) {
            return $response;
        }

        if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
            return new WP_Error( 'http_error', '피드 요청에 실패했습니다. HTTP 상태 코드: ' . wp_remote_retrieve_response_code( $response ) );
        }

        $xml_body = wp_remote_retrieve_body( $response );
        $xml = @simplexml_load_string( $xml_body, 'SimpleXMLElement', LIBXML_NOCDATA );
        if ( ! $xml ) {
            return new WP_Error( 'invalid_xml', '올바르지 않은 XML 형식이거나 파싱에 실패했습니다.' );
        }

        $synced_count = 0;
        $detected_categories = array();

        foreach ( $xml->channel->item as $item ) {
            $link = (string) $item->link;
            $guid = (string) $item->guid;
            $title = (string) $item->title;
            $description = (string) $item->description;
            $pubDate = (string) $item->pubDate;
            $naver_category = trim( (string) $item->category );

            if ( ! empty( $naver_category ) ) {
                $detected_categories[$naver_category] = $naver_category;
            }

            $existing = get_posts( array(
                'post_type'      => 'post',
                'meta_key'       => '_naver_guid',
                'meta_value'     => $guid,
                'posts_per_page' => 1,
                'post_status'    => 'any',
                'fields'         => 'ids',
            ) );

            if ( ! empty( $existing ) ) {
                continue;
            }

            if ( $synced_count >= 15 ) {
                break;
            }

            if ( empty( $naver_category ) ) {
                $wp_category_id = (int) get_option( 'default_category', 1 );
            } else {
                $wp_category_id = isset( $settings['category_mapping'][$naver_category] ) ? (int)$settings['category_mapping'][$naver_category] : 0;
                if ( ! $wp_category_id ) {
                    $term = term_exists( $naver_category, 'category' );
                    if ( ! $term ) {
                        $new_term = wp_insert_term( $naver_category, 'category' );
                        $wp_category_id = ( ! is_wp_error( $new_term ) ) ? $new_term['term_id'] : 0;
                    } else {
                        $wp_category_id = (int)$term['term_id'];
                    }
                }
            }

            $pub_time = strtotime( $pubDate );
            $post_data = array(
                'post_title'    => $title,
                'post_content'  => $description,
                'post_status'   => 'publish',
                'post_type'     => 'post',
                'post_category' => array( $wp_category_id ),
                'post_date'     => wp_date( 'Y-m-d H:i:s', $pub_time ),
                'post_date_gmt' => gmdate( 'Y-m-d H:i:s', $pub_time )
            );

            $post_id = wp_insert_post( $post_data );

            if ( ! is_wp_error( $post_id ) && $post_id > 0 ) {
                $synced_count++;
                update_post_meta( $post_id, '_naver_guid', $guid );
                update_post_meta( $post_id, '_naver_original_url', esc_url_raw( $link ) );
                update_post_meta( $post_id, '_is_naver_synced', '1' );

                preg_match_all( '/<img[^>]+src="([^">]+)"/', $description, $matches );
                if ( isset( $matches[1] ) && is_array( $matches[1] ) ) {
                    $featured_image_url = '';
                    foreach ( $matches[1] as $img_url ) {
                        if ( 
                            strpos( $img_url, 'sticker' ) !== false || 
                            strpos( $img_url, 'emoji' ) !== false || 
                            strpos( $img_url, 'sticker-phinf' ) !== false || 
                            strpos( $img_url, 'storep-phinf.pstatic.net' ) !== false 
                        ) {
                            continue;
                        }
                        $featured_image_url = html_entity_decode( $img_url, ENT_QUOTES, 'UTF-8' );
                        break;
                    }

                    if ( ! empty( $featured_image_url ) ) {
                        $this->download_and_set_featured_image( $featured_image_url, $post_id );
                    }
                }
            }
        }

        if ( ! empty( $detected_categories ) ) {
            $saved_categories = get_option( 'naver_rss_detected_categories', array() );
            $merged_categories = array_unique( array_merge( $saved_categories, array_values( $detected_categories ) ) );
            update_option( 'naver_rss_detected_categories', $merged_categories );
        }

        return array( 'success' => true, 'count' => $synced_count );
    }

    private function download_and_set_featured_image( $url, $post_id ) {
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/media.php' );

        // Bypassing Naver blog image hotlinking by supplying Referer and User-Agent
        $referer_filter = function( $args, $request_url ) {
            if ( strpos( $request_url, 'pstatic.net' ) !== false || strpos( $request_url, 'naver.com' ) !== false ) {
                $args['headers']['Referer'] = 'https://blog.naver.com/';
                $args['headers']['User-Agent'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36';
            }
            return $args;
        };
        add_filter( 'http_request_args', $referer_filter, 10, 2 );

        $tmp = download_url( $url, 15 );

        remove_filter( 'http_request_args', $referer_filter, 10 );

        if ( is_wp_error( $tmp ) ) {
            return;
        }

        $clean_url = strtok( $url, '?' );
        $filename = basename( $clean_url );
        if ( ! preg_match( '/\.(jpe?g|gif|png|webp)$/i', $filename ) ) {
            $filename .= '.jpg';
        }

        $file_array = array(
            'name'     => $filename,
            'tmp_name' => $tmp
        );

        $thumb_id = media_handle_sideload( $file_array, $post_id );
        if ( ! is_wp_error( $thumb_id ) ) {
            set_post_thumbnail( $post_id, $thumb_id );
        } else {
            @unlink( $tmp );
        }
    }

    public function add_robots_directives( array $robots ) {
        if ( is_single() ) {
            global $post;
            if ( $post ) {
                $is_synced = get_post_meta( $post->ID, '_is_naver_synced', true );
                if ( $is_synced === '1' ) {
                    $robots['noindex']  = true;
                    $robots['nofollow'] = true;
                }
            }
        }
        return $robots;
    }

    public function redirect_to_naver_original() {
        if ( is_single() && ! is_preview() ) {
            global $post;
            if ( $post ) {
                $original_url = get_post_meta( $post->ID, '_naver_original_url', true );
                if ( ! empty( $original_url ) ) {
                    wp_redirect( $original_url, 302 );
                    exit;
                }
            }
        }
    }

    public function override_post_link( $url, $post ) {
        if ( is_object( $post ) ) {
            $original_url = get_post_meta( $post->ID, '_naver_original_url', true );
            if ( ! empty( $original_url ) ) {
                return $original_url;
            }
        }
        return $url;
    }

    public function add_naver_link_metabox() {
        add_meta_box(
            'naver_original_link_box',
            '네이버 연동 원본 링크',
            array( $this, 'render_naver_link_metabox' ),
            'post',
            'side',
            'high'
        );
    }

    public function render_naver_link_metabox( $post ) {
        $original_url = get_post_meta( $post->ID, '_naver_original_url', true );
        wp_nonce_field( 'save_naver_original_link_nonce', 'naver_original_link_nonce' );
        echo '<input type="url" name="naver_original_url" value="' . esc_attr( $original_url ) . '" style="width:100%;" placeholder="https://blog.naver.com/..."/>';
        echo '<p class="description">기입 시 글 목록에서 해당 주소로 새 창 이동 및 상세 접속 시 리다이렉트 처리됩니다.</p>';
    }

    public function save_naver_link_metabox( $post_id ) {
        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }
        if ( ! isset( $_POST['naver_original_link_nonce'] ) || ! wp_verify_nonce( $_POST['naver_original_link_nonce'], 'save_naver_original_link_nonce' ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
        if ( isset( $_POST['naver_original_url'] ) ) {
            $url = esc_url_raw( $_POST['naver_original_url'] );
            update_post_meta( $post_id, '_naver_original_url', $url );
            if ( ! empty( $url ) ) {
                update_post_meta( $post_id, '_is_naver_synced', '1' );
            } else {
                delete_post_meta( $post_id, '_is_naver_synced' );
            }
        }
    }

    public function register_admin_menu() {
        add_menu_page(
            '네이버 RSS 연동',
            '네이버 연동',
            'manage_options',
            'naver-rss-sync',
            array( $this, 'render_admin_settings_page' ),
            'dashicons-rss',
            80
        );
    }

    public function ajax_sync_rss() {
        check_ajax_referer( 'naver_rss_sync_nonce', 'security' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
        }
        
        $result = $this->sync_rss_feed();
        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }
        
        wp_send_json_success( array( 'message' => sprintf( '네이버 RSS 피드 수집 및 동기화가 완료되었습니다! (동기화된 새 글: %d개)', $result['count'] ) ) );
    }

    public function render_admin_settings_page() {
        if ( isset( $_POST['submit_naver_rss_settings'] ) ) {
            if ( ! current_user_can( 'manage_options' ) ) {
                wp_die( '권한이 없습니다.' );
            }
            check_admin_referer( 'save_naver_rss_settings_action', 'naver_rss_settings_nonce' );
            
            $rss_url = isset( $_POST['rss_url'] ) ? esc_url_raw( $_POST['rss_url'] ) : '';
            $sync_interval = isset( $_POST['sync_interval'] ) ? sanitize_text_field( $_POST['sync_interval'] ) : 'hourly';
            $category_mapping = isset( $_POST['category_mapping'] ) ? $_POST['category_mapping'] : array();
            
            $sanitized_mapping = array();
            if ( is_array( $category_mapping ) ) {
                foreach ( $category_mapping as $rss_cat => $wp_cat_id ) {
                    $sanitized_mapping[ sanitize_text_field( $rss_cat ) ] = intval( $wp_cat_id );
                }
            }

            $old_settings = $this->get_settings();
            $updated_settings = array(
                'rss_url'          => $rss_url,
                'sync_interval'    => $sync_interval,
                'category_mapping' => $sanitized_mapping
            );
            
            if ( $old_settings['rss_url'] !== $rss_url ) {
                delete_option( 'naver_rss_detected_categories' );
            }
            
            $this->update_settings( $updated_settings );
            echo '<div class="notice notice-success is-dismissible"><p>설정이 저장되었습니다.</p></div>';
        }

        $settings = $this->get_settings();
        $categories = get_categories( array( 'hide_empty' => 0 ) );
        
        $rss_categories = get_option( 'naver_rss_detected_categories', array() );
        
        if ( empty( $rss_categories ) && ! empty( $settings['rss_url'] ) ) {
            $response = wp_remote_get( $settings['rss_url'], array( 'timeout' => 15 ) );
            if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
                $xml_body = wp_remote_retrieve_body( $response );
                $xml = @simplexml_load_string( $xml_body, 'SimpleXMLElement', LIBXML_NOCDATA );
                if ( $xml ) {
                    $detected = array();
                    foreach ( $xml->channel->item as $item ) {
                        $cat = trim( (string) $item->category );
                        if ( ! empty( $cat ) ) {
                            $detected[$cat] = $cat;
                        }
                    }
                    if ( ! empty( $detected ) ) {
                        $rss_categories = array_values( $detected );
                        update_option( 'naver_rss_detected_categories', $rss_categories );
                    }
                }
            }
        }
        
        wp_nonce_field( 'naver_rss_sync_nonce', 'naver_rss_sync_security' );
        ?>
        <style>
            .naver-rss-admin-body {
                padding: 24px;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                color: #191f28;
            }
            .naver-rss-admin-card {
                background: rgba(255, 255, 255, 0.75);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border-radius: 20px;
                border: 1px solid rgba(255, 255, 255, 0.4);
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
                padding: 40px;
                max-width: 800px;
                margin: 0 auto;
            }
            .naver-rss-header {
                margin-bottom: 32px;
            }
            .naver-rss-title {
                font-size: 28px;
                font-weight: 700;
                color: #191f28;
                margin: 0 0 8px 0;
            }
            .naver-rss-subtitle {
                font-size: 15px;
                color: #8b95a1;
                margin: 0;
            }
            .naver-rss-section {
                margin-bottom: 36px;
                padding-bottom: 36px;
                border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            }
            .naver-rss-section:last-child {
                border-bottom: none;
                margin-bottom: 0;
                padding-bottom: 0;
            }
            .naver-rss-section-title {
                font-size: 18px;
                font-weight: 600;
                color: #333d4b;
                margin: 0 0 20px 0;
            }
            .naver-rss-form-group {
                margin-bottom: 24px;
            }
            .naver-rss-label {
                display: block;
                font-size: 15px;
                font-weight: 600;
                color: #4e5968;
                margin-bottom: 10px;
            }
            .naver-rss-input, .naver-rss-select {
                width: 100%;
                max-width: 100%;
                padding: 14px 16px;
                font-size: 15px;
                border-radius: 12px;
                border: 1px solid #e5e8eb;
                background-color: #f9fafb;
                color: #191f28;
                box-sizing: border-box;
                transition: all 0.2s ease;
            }
            .naver-rss-input:focus, .naver-rss-select:focus {
                outline: none;
                border-color: #3182f6;
                background-color: #fff;
                box-shadow: 0 0 0 3px rgba(49, 130, 246, 0.15);
            }
            .naver-rss-table-container {
                border-radius: 12px;
                overflow: hidden;
                border: 1px solid #e5e8eb;
                margin-top: 10px;
            }
            .naver-rss-table {
                width: 100%;
                border-collapse: collapse;
                background: #fff;
            }
            .naver-rss-table th {
                background: #f9fafb;
                padding: 14px 20px;
                font-size: 14px;
                font-weight: 600;
                color: #4e5968;
                text-align: left;
                border-bottom: 1px solid #e5e8eb;
            }
            .naver-rss-table td {
                padding: 16px 20px;
                font-size: 15px;
                color: #333d4b;
                border-bottom: 1px solid #e5e8eb;
            }
            .naver-rss-table tr:last-child td {
                border-bottom: none;
            }
            .naver-rss-btn-group {
                display: flex;
                gap: 12px;
                margin-top: 40px;
            }
            .naver-rss-btn {
                padding: 16px 24px;
                font-size: 16px;
                font-weight: 600;
                border-radius: 12px;
                border: none;
                cursor: pointer;
                transition: all 0.2s ease;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                text-decoration: none;
            }
            .naver-rss-btn-primary {
                background-color: #3182f6;
                color: #fff;
                flex: 1;
            }
            .naver-rss-btn-primary:hover {
                background-color: #1b64da;
                box-shadow: 0 4px 12px rgba(49, 130, 246, 0.3);
            }
            .naver-rss-btn-secondary {
                background-color: #e5e8eb;
                color: #4e5968;
            }
            .naver-rss-btn-secondary:hover {
                background-color: #d1d6db;
            }
            .naver-rss-btn:disabled {
                background-color: #e5e8eb;
                color: #b0b8c1;
                cursor: not-allowed;
                box-shadow: none;
            }
            .naver-rss-spinner {
                display: inline-block;
                width: 16px;
                height: 16px;
                border: 2px solid rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                border-top-color: #fff;
                animation: naver-rss-spin 1s linear infinite;
                margin-right: 8px;
            }
            @keyframes naver-rss-spin {
                to { transform: rotate(360deg); }
            }
        </style>
        <div class="naver-rss-admin-body">
            <div class="naver-rss-admin-card">
                <div class="naver-rss-header">
                    <h2 class="naver-rss-title">네이버 블로그 RSS 연동 설정</h2>
                    <p class="naver-rss-subtitle">네이버 블로그 RSS 피드를 연동하여 워드프레스에 수집 및 동기화합니다.</p>
                </div>
                <form method="post">
                    <?php wp_nonce_field( 'save_naver_rss_settings_action', 'naver_rss_settings_nonce' ); ?>
                    <div class="naver-rss-section">
                        <h3 class="naver-rss-section-title">기본 연동 설정</h3>
                        <div class="naver-rss-form-group">
                            <label class="naver-rss-label">네이버 블로그 RSS URL</label>
                            <input type="url" name="rss_url" class="naver-rss-input" value="<?php echo esc_url( $settings['rss_url'] ); ?>" required />
                        </div>
                        <div class="naver-rss-form-group">
                            <label class="naver-rss-label">자동 수집 주기</label>
                            <select name="sync_interval" class="naver-rss-select">
                                <option value="hourly" <?php selected( $settings['sync_interval'], 'hourly' ); ?>>1시간마다 (매시간)</option>
                                <option value="twicedaily" <?php selected( $settings['sync_interval'], 'twicedaily' ); ?>>12시간마다 (하루에 두 번)</option>
                                <option value="daily" <?php selected( $settings['sync_interval'], 'daily' ); ?>>24시간마다 (하루에 한 번)</option>
                                <option value="manual" <?php selected( $settings['sync_interval'], 'manual' ); ?>>수동 수집만</option>
                            </select>
                        </div>
                    </div>

                    <div class="naver-rss-section">
                        <h3 class="naver-rss-section-title">카테고리 매핑 설정</h3>
                        <div class="naver-rss-table-container">
                            <table class="naver-rss-table">
                                <thead>
                                    <tr>
                                        <th>네이버 카테고리</th>
                                        <th>워드프레스 매핑 카테고리 (미선택 시 자동 생성)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ( empty( $rss_categories ) ): ?>
                                        <tr>
                                            <td colspan="2" style="color:#8b95a1; text-align:center;">피드에서 감지된 네이버 카테고리가 없습니다. RSS URL을 저장한 후 확인해 주세요.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ( $rss_categories as $rss_cat ): ?>
                                            <tr>
                                                <td><strong><?php echo esc_html( $rss_cat ); ?></strong></td>
                                                <td>
                                                    <select name="category_mapping[<?php echo esc_attr( $rss_cat ); ?>]" class="naver-rss-select" style="width: auto;">
                                                        <option value="">-- 네이버 카테고리명 그대로 생성 --</option>
                                                        <?php foreach ( $categories as $wp_cat ): ?>
                                                            <?php 
                                                            $current_mapped = isset( $settings['category_mapping'][$rss_cat] ) ? $settings['category_mapping'][$rss_cat] : '';
                                                            ?>
                                                            <option value="<?php echo absint( $wp_cat->term_id ); ?>" <?php selected( $current_mapped, $wp_cat->term_id ); ?>><?php echo esc_html( $wp_cat->name ); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="naver-rss-btn-group">
                        <button type="submit" name="submit_naver_rss_settings" class="naver-rss-btn naver-rss-btn-primary">설정 저장</button>
                        <button type="button" id="sync-now-btn" class="naver-rss-btn naver-rss-btn-secondary">지금 즉시 동기화</button>
                    </div>
                </form>
            </div>
        </div>
        <script>
            jQuery(document).ready(function($) {
                $('#sync-now-btn').on('click', function(e) {
                    e.preventDefault();
                    var btn = $(this);
                    btn.prop('disabled', true).html('<span class="naver-rss-spinner"></span>동기화 중...');
                    
                    $.post(ajaxurl, {
                        action: 'naver_rss_sync_now',
                        security: $('#naver_rss_sync_security').val()
                    }, function(response) {
                        if (response.success) {
                            alert(response.data.message);
                        } else {
                            alert('오류 발생: ' + response.data.message);
                        }
                        btn.prop('disabled', false).html('지금 즉시 동기화');
                    });
                });
            });
        </script>
        <?php
    }

    public function override_blog_archive_template( $template ) {
        if ( ( is_home() || is_category() || is_tag() || is_author() || is_date() || is_search() ) && ! is_feed() ) {
            $this->render_toss_style_archive();
            exit;
        }
        return $template;
    }

    public function render_toss_style_archive() {
        get_header();
        
        $categories = get_categories( array( 'hide_empty' => 1 ) );
        ?>
        <style>
            .toss-archive-container {
                max-width: 960px;
                margin: 0 auto;
                padding: 60px 24px;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Apple SD Gothic Neo", sans-serif;
                color: #191f28;
                background-color: #ffffff;
            }
            .toss-category-tabs {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin-bottom: 40px;
                border-bottom: 1px solid #e5e8eb;
                padding-bottom: 20px;
            }
            .toss-category-tab {
                padding: 10px 18px;
                font-size: 15px;
                font-weight: 600;
                border-radius: 20px;
                border: none;
                background-color: #f2f4f6;
                color: #4e5968;
                cursor: pointer;
                transition: background-color 0.2s ease, color 0.2s ease, transform 0.1s ease;
                text-decoration: none;
                display: inline-block;
            }
            .toss-category-tab:hover {
                background-color: #e5e8eb;
            }
            .toss-category-tab.active {
                background-color: #3182f6;
                color: #ffffff;
            }
            .toss-category-tab:active {
                transform: scale(0.96);
            }
            .toss-post-list {
                display: flex;
                flex-direction: column;
                gap: 32px;
            }
            .toss-post-card {
                display: flex;
                gap: 24px;
                text-decoration: none;
                color: inherit;
                border-radius: 16px;
                padding: 16px;
                margin: -16px;
                transition: background-color 0.2s ease, opacity 0.2s ease, transform 0.2s ease;
                opacity: 1;
                transform: translateY(0);
            }
            .toss-post-card:hover {
                background-color: #f9fafb;
            }
            .toss-post-card:hover .toss-post-title {
                color: #3182f6;
            }
            .toss-post-thumbnail-wrapper {
                flex-shrink: 0;
                width: 220px;
                aspect-ratio: 16/9;
                border-radius: 12px;
                overflow: hidden;
                background-color: #f2f4f6;
            }
            .toss-post-thumbnail {
                width: 100%;
                height: 100%;
                object-fit: cover;
                aspect-ratio: 16/9;
                transition: transform 0.3s ease;
            }
            .toss-post-card:hover .toss-post-thumbnail {
                transform: scale(1.03);
            }
            .toss-post-thumbnail-fallback {
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #b0b8c1;
                background-color: #f2f4f6;
            }
            .toss-post-content {
                display: flex;
                flex-direction: column;
                justify-content: center;
                flex-grow: 1;
                min-width: 0;
            }
            .toss-post-title {
                font-size: 20px;
                font-weight: 700;
                line-height: 1.4;
                margin: 0 0 8px 0;
                color: #191f28;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                transition: color 0.2s ease;
            }
            .toss-post-desc {
                font-size: 15px;
                line-height: 1.6;
                color: #4e5968;
                margin: 0 0 12px 0;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .toss-post-date {
                font-size: 13px;
                color: #8b95a1;
            }
            .toss-no-posts {
                padding: 60px 0;
                text-align: center;
                font-size: 16px;
                color: #8b95a1;
            }
            .toss-pagination {
                display: flex;
                justify-content: center;
                gap: 8px;
                margin-top: 50px;
            }
            .toss-pagination .page-numbers {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 40px;
                height: 40px;
                padding: 0 6px;
                font-size: 15px;
                font-weight: 600;
                color: #4e5968;
                background-color: #f2f4f6;
                border-radius: 50%;
                text-decoration: none;
                transition: background-color 0.2s ease, color 0.2s ease;
            }
            .toss-pagination .page-numbers:hover {
                background-color: #e5e8eb;
            }
            .toss-pagination .page-numbers.current {
                background-color: #3182f6;
                color: #ffffff;
            }
            .toss-pagination .page-numbers.prev,
            .toss-pagination .page-numbers.next {
                border-radius: 8px;
                padding: 0 12px;
            }
            @media (max-width: 768px) {
                .toss-archive-container {
                    padding: 40px 16px;
                }
                .toss-post-card {
                    flex-direction: column;
                    gap: 16px;
                    padding: 12px;
                    margin: -12px;
                }
                .toss-post-thumbnail-wrapper {
                    width: 100%;
                    height: auto;
                    aspect-ratio: 16/9;
                }
                .toss-post-title {
                    font-size: 18px;
                    white-space: normal;
                    display: -webkit-box;
                    -webkit-line-clamp: 2;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                }
            }
        </style>
        <div class="toss-archive-container">
            <?php if ( ! empty( $categories ) ): ?>
                <div class="toss-category-tabs">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="toss-category-tab<?php echo is_home() ? ' active' : ''; ?>">전체</a>
                    <?php foreach ( $categories as $cat ): ?>
                        <?php 
                        $active_class = ( is_category() && get_queried_object_id() === $cat->term_id ) ? ' active' : '';
                        ?>
                        <a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>" class="toss-category-tab<?php echo $active_class; ?>"><?php echo esc_html( $cat->name ); ?></a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if ( have_posts() ): ?>
                <div class="toss-post-list">
                    <?php while ( have_posts() ): the_post(); 
                        $post_id = get_the_ID();
                        $naver_url = get_post_meta( $post_id, '_naver_original_url', true );
                        $href = ! empty( $naver_url ) ? esc_url( $naver_url ) : get_permalink();
                        $target = ! empty( $naver_url ) ? ' target="_blank" rel="noopener noreferrer"' : '';
                        $thumb_url = get_the_post_thumbnail_url( $post_id, 'medium_large' );
                        
                        $excerpt = wp_strip_all_tags( get_the_excerpt() );
                        $excerpt = mb_strimwidth( $excerpt, 0, 120, '...' );
                        ?>
                        <a href="<?php echo esc_url( $href ); ?>"<?php echo $target; ?> class="toss-post-card">
                            <div class="toss-post-thumbnail-wrapper">
                                <?php if ( $thumb_url ): ?>
                                    <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php the_title_attribute(); ?>" class="toss-post-thumbnail" />
                                <?php else: ?>
                                    <div class="toss-post-thumbnail-fallback">
                                        <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-image"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="toss-post-content">
                                <h3 class="toss-post-title"><?php echo esc_html( get_the_title() ); ?></h3>
                                <p class="toss-post-desc"><?php echo esc_html( $excerpt ); ?></p>
                                <span class="toss-post-date"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></span>
                            </div>
                        </a>
                    <?php endwhile; ?>
                </div>
                <?php
                global $wp_query;
                $pagination = paginate_links( array(
                    'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
                    'format'    => '?paged=%#%',
                    'current'   => max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) ),
                    'total'     => $wp_query->max_num_pages,
                    'prev_text' => esc_html__( '이전', 'naver-rss-sync' ),
                    'next_text' => esc_html__( '다음', 'naver-rss-sync' ),
                    'type'      => 'plain',
                ) );
                if ( $pagination ) {
                    echo '<div class="toss-pagination">' . $pagination . '</div>';
                }
                ?>
            <?php else: ?>
                <p class="toss-no-posts">등록된 글이 없습니다.</p>
            <?php endif; ?>
        </div>
        <?php
        get_footer();
    }
}

// Instantiate singleton pattern
Naver_RSS_Sync::get_instance();
}
