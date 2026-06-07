<?php
/**
 * Plugin Name: Naver RSS Sync Ultimate
 * Description: 네이버 RSS -> 워드프레스 포스팅 동기화 얼티밋 플러그인 (CPT & 일반 포스트 동적 지원)
 * Version: 1.0.0
 * Author: Advanced Agentic Coding
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Naver_RSS_Sync_Ultimate {
    
    /**
     * Singleton instance
     * @var Naver_RSS_Sync_Ultimate|null
     */
    private static $instance = null;
    
    /**
     * Option name in WP options table
     * @var string
     */
    private $option_name = 'naver_rss_sync_ultimate_settings';
    
    /**
     * Cache for options
     * @var array
     */
    private $options = [];

    /**
     * Get singleton instance
     * 
     * @return Naver_RSS_Sync_Ultimate
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor (Private to enforce Singleton)
     */
    private function __construct() {
        // Load options safely with wp_parse_args to prevent undefined index warnings
        $raw_options = get_option( $this->option_name );
        $raw_options = is_array( $raw_options ) ? $raw_options : [];
        $this->options = wp_parse_args( $raw_options, $this->get_default_options() );

        // 1. CPT Registration
        add_action( 'init', [ $this, 'register_naver_blog_cpt' ] );

        // 2. Admin Logic & Actions
        if ( is_admin() ) {
            add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
            add_action( 'admin_init', [ $this, 'register_settings' ] );
            add_action( 'admin_head', [ $this, 'enqueue_admin_styles' ] );
            add_action( 'admin_footer', [ $this, 'print_admin_scripts' ] );
            add_action( 'admin_post_nrsu_run_sync', [ $this, 'handle_manual_sync' ] );
        }

        // 3. Classic Editor Override Filter (Selective Gutenberg Block Editor bypass)
        add_filter( 'use_block_editor_for_post', [ $this, 'disable_gutenberg_editor_for_post' ], 10, 2 );

        // 4. Cron hook & Setup
        add_action( 'naver_rss_sync_cron_hook', [ $this, 'run_sync' ] );
        add_action( 'init', [ $this, 'setup_cron_schedule' ] );
    }

    /**
     * Default options structure
     * 
     * @return array
     */
    private function get_default_options() {
        return [
            'rss_url'             => 'https://rss.blog.naver.com/your_naver_id.xml',
            'post_type_selection' => 'naver_blog', // 'naver_blog' (CPT) or 'post' (General post)
            'use_classic_editor'  => 1,            // Force classic editor (1 = true, 0 = false)
            'sync_interval'       => 'twicedaily',
            'post_status'         => 'draft',
        ];
    }

    /**
     * Register Custom Post Type: naver_blog (Dynamic registration)
     */
    public function register_naver_blog_cpt() {
        // 🔴 CPT 동적 등록 분기 로직: 선택 값이 'naver_blog'가 아닐 경우 CPT를 등록하지 않음
        $post_type_selection = isset( $this->options['post_type_selection'] ) ? $this->options['post_type_selection'] : 'naver_blog';
        if ( 'naver_blog' !== $post_type_selection ) {
            return;
        }

        $labels = [
            'name'               => '네이버 블로그',
            'singular_name'      => '네이버 블로그 글',
            'menu_name'          => '네이버 블로그',
            'add_new'            => '새 글 추가',
            'add_new_item'       => '새 네이버 블로그 글 추가',
            'edit_item'          => '네이버 블로그 글 편집',
            'new_item'           => '새 네이버 블로그 글',
            'view_item'          => '네이버 블로그 글 보기',
            'search_items'       => '네이버 블로그 글 검색',
            'not_found'          => '등록된 네이버 블로그 글이 없습니다.',
            'not_found_in_trash' => '휴지통에 네이버 블로그 글이 없습니다.',
        ];

        $args = [
            'labels'              => $labels,
            'public'              => true,
            'has_archive'         => 'naver-blog',
            'rewrite'             => [ 'slug' => 'naver-blog' ],
            'exclude_from_search' => false,
            'show_in_rest'        => true, // Required for block editor (Gutenberg) compatibility if active
            'menu_icon'           => 'dashicons-rss',
            'supports'            => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions' ],
            'taxonomies'          => [ 'category', 'post_tag' ],
        ];

        register_post_type( 'naver_blog', $args );
    }

    /**
     * Add admin menu page
     */
    public function add_admin_menu() {
        add_menu_page(
            'Naver RSS Sync Ultimate',
            '네이버 RSS 연동',
            'manage_options',
            'naver-rss-sync-ultimate',
            [ $this, 'render_admin_page' ],
            'dashicons-rss',
            80
        );
    }

    /**
     * Register settings in WordPress
     */
    public function register_settings() {
        register_setting( 'naver_rss_sync_ultimate_group', $this->option_name, [ $this, 'sanitize_options' ] );
    }

    /**
     * Sanitize settings input
     * 
     * @param array $input
     * @return array
     */
    public function sanitize_options( $input ) {
        $output = [];
        
        $output['rss_url'] = isset( $input['rss_url'] ) ? esc_url_raw( trim( $input['rss_url'] ) ) : '';
        
        // CPT vs Post option validation
        $output['post_type_selection'] = ( isset( $input['post_type_selection'] ) && in_array( $input['post_type_selection'], [ 'naver_blog', 'post' ], true ) ) ? $input['post_type_selection'] : 'naver_blog';
        
        // Gutenberg override checkbox
        $output['use_classic_editor'] = isset( $input['use_classic_editor'] ) ? 1 : 0;
        
        $output['sync_interval'] = isset( $input['sync_interval'] ) ? sanitize_text_field( $input['sync_interval'] ) : 'twicedaily';
        $output['post_status'] = isset( $input['post_status'] ) && in_array( $input['post_status'], [ 'draft', 'publish' ], true ) ? $input['post_status'] : 'draft';

        return $output;
    }

    /**
     * Filter use_block_editor_for_post_type to block Gutenberg if configured
     * 
     * @param bool $use_block_editor
     * @param string $post_type
     * @return bool
     */
    /**
     * Disable block editor for specific posts to prevent white-screens/errors
     * 
     * @param bool $use_block_editor
     * @param WP_Post $post
     * @return bool
     */
    public function disable_gutenberg_editor_for_post( $use_block_editor, $post ) {
        if ( ! $post instanceof WP_Post ) {
            return $use_block_editor;
        }

        $force_classic = $this->options['use_classic_editor'] ?? 1;
        if ( ! $force_classic ) {
            return $use_block_editor;
        }

        $target_post_type = $this->options['post_type_selection'] ?? 'naver_blog';

        // Always disable block editor for CPT naver_blog
        if ( 'naver_blog' === $post->post_type ) {
            return false;
        }

        // For target post type (e.g. post), disable Gutenberg ONLY if it was imported from Naver RSS
        if ( $post->post_type === $target_post_type ) {
            $guid = get_post_meta( $post->ID, '_naver_guid', true );
            if ( ! empty( $guid ) ) {
                return false;
            }
        }

        return $use_block_editor;
    }

    /**
     * Setup or reschedule WP-Cron based on settings
     */
    public function setup_cron_schedule() {
        $interval = $this->options['sync_interval'] ?? 'twicedaily';
        $hook = 'naver_rss_sync_cron_hook';

        if ( 'manual' === $interval ) {
            wp_clear_scheduled_hook( $hook );
        } else {
            if ( ! wp_next_scheduled( $hook ) ) {
                wp_schedule_event( time(), $interval, $hook );
            } else {
                $schedule = wp_get_schedule( $hook );
                if ( $schedule !== $interval ) {
                    wp_clear_scheduled_hook( $hook );
                    wp_schedule_event( time(), $interval, $hook );
                }
            }
        }
    }

    /**
     * Handle manual sync button request from admin dashboard
     */
    public function handle_manual_sync() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( '권한이 없습니다.' );
        }

        check_admin_referer( 'nrsu_run_sync_action', 'nrsu_sync_nonce' );

        $result = $this->run_sync();

        if ( is_wp_error( $result ) ) {
            $redirect_url = add_query_arg(
                [
                    'nrsu_sync_status' => 'error',
                    'nrsu_sync_msg'    => urlencode( $result->get_error_message() ),
                ],
                menu_page_url( 'naver-rss-sync-ultimate', false )
            );
        } else {
            $redirect_url = add_query_arg(
                [
                    'nrsu_sync_status' => 'success',
                    'nrsu_sync_count'  => intval( $result ),
                ],
                menu_page_url( 'naver-rss-sync-ultimate', false )
            );
        }

        wp_safe_redirect( $redirect_url );
        exit;
    }

    /**
     * Core synchronization runner
     * Runs RSS fetching, parsing, and post generation with concurrency locks and duplicate checks
     * 
     * @return int|WP_Error Number of synced posts on success, WP_Error on failure
     */
    public function run_sync() {
        $rss_url = $this->options['rss_url'] ?? '';
        if ( empty( $rss_url ) || ! filter_var( $rss_url, FILTER_VALIDATE_URL ) ) {
            return new WP_Error( 'invalid_url', '유효한 네이버 RSS 주소가 설정되지 않았습니다.' );
        }

        // Concurrency Lock using WP Transient
        $lock_key = 'naver_rss_sync_lock';
        if ( get_transient( $lock_key ) ) {
            return new WP_Error( 'locked', '이미 동기화 작업이 실행 중입니다. 잠시 후 다시 시도해 주세요.' );
        }
        // Acquire lock for 5 minutes
        set_transient( $lock_key, 1, 5 * MINUTE_IN_SECONDS );

        // Fetch RSS feed content
        $response = wp_remote_get( $rss_url, [ 'timeout' => 30 ] );
        if ( is_wp_error( $response ) ) {
            delete_transient( $lock_key );
            return $response;
        }

        $body = wp_remote_retrieve_body( $response );
        if ( empty( $body ) ) {
            delete_transient( $lock_key );
            return new WP_Error( 'empty_feed', 'RSS 피드 응답 본문이 비어 있습니다.' );
        }

        // Suppress XML errors during parsing
        libxml_use_internal_errors( true );
        $xml = simplexml_load_string( $body, 'SimpleXMLElement', LIBXML_NOCDATA );
        if ( ! $xml ) {
            delete_transient( $lock_key );
            return new WP_Error( 'xml_parse_error', 'RSS XML 파싱에 실패했습니다.' );
        }
        libxml_clear_errors();

        $items = isset( $xml->channel->item ) ? $xml->channel->item : [];
        if ( empty( $items ) ) {
            delete_transient( $lock_key );
            $this->log_sync_status( 0, 'No items' );
            return 0;
        }

        $target_post_type = $this->options['post_type_selection'] ?? 'naver_blog';
        $post_status = $this->options['post_status'] ?? 'draft';
        $synced_count = 0;

        foreach ( $items as $item ) {
            $guid = sanitize_text_field( (string) $item->guid );
            $link = esc_url_raw( (string) $item->link );
            if ( empty( $guid ) ) {
                $guid = $link;
            }

            if ( empty( $guid ) ) {
                continue;
            }

            // Check duplicate via meta key _naver_guid
            global $wpdb;
            $existing_post_id = $wpdb->get_var( $wpdb->prepare(
                "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_naver_guid' AND meta_value = %s LIMIT 1",
                $guid
            ) );

            if ( $existing_post_id ) {
                // Duplicate found, skip
                continue;
            }

            $title = sanitize_text_field( (string) $item->title );
            $content = (string) $item->description;
            $pub_date = (string) $item->pubDate;

            $post_date = '';
            if ( ! empty( $pub_date ) ) {
                $timestamp = strtotime( $pub_date );
                if ( $timestamp ) {
                    $post_date = date( 'Y-m-d H:i:s', $timestamp );
                }
            }

            // Prepare post arguments
            $post_arr = [
                'post_title'   => $title,
                'post_content' => $content,
                'post_status'  => $post_status,
                'post_type'    => $target_post_type,
            ];

            if ( ! empty( $post_date ) ) {
                $post_arr['post_date'] = $post_date;
            }

            // Insert post
            $new_post_id = wp_insert_post( $post_arr );

            if ( ! is_wp_error( $new_post_id ) && $new_post_id > 0 ) {
                // Save meta keys
                update_post_meta( $new_post_id, '_naver_guid', $guid );
                update_post_meta( $new_post_id, '_naver_link', $link );

                // Try to extract first image and set as featured thumbnail
                $first_image_url = $this->extract_first_image_url( $content );
                if ( ! empty( $first_image_url ) ) {
                    $this->sideload_featured_image( $new_post_id, $first_image_url );
                }

                $synced_count++;
            }
        }

        // Release lock
        delete_transient( $lock_key );

        // Log status
        $this->log_sync_status( $synced_count, 'Success' );

        return $synced_count;
    }

    /**
     * Log the status and time of the last sync run
     * 
     * @param int $count
     * @param string $status
     */
    private function log_sync_status( $count, $status ) {
        update_option( 'naver_rss_sync_ultimate_last_sync', current_time( 'mysql' ) );
        update_option( 'naver_rss_sync_ultimate_last_status', sprintf( '%s (신규 추가: %d개)', $status, $count ) );
    }

    /**
     * Extract first image URL from content HTML
     * 
     * @param string $content
     * @return string
     */
    private function extract_first_image_url( $content ) {
        if ( preg_match( '/<img[^>]+src=["\']([^"\']+)["\']/i', $content, $matches ) ) {
            return $matches[1];
        }
        return '';
    }

    /**
     * Sideload an image and set it as the featured thumbnail for a post
     * Strips query parameters, checks for existing downloads to prevent duplication
     * 
     * @param int $post_id
     * @param string $image_url
     * @return int|WP_Error Attachment ID on success, WP_Error on failure
     */
    public function sideload_featured_image( $post_id, $image_url ) {
        if ( empty( $image_url ) ) {
            return new WP_Error( 'empty_url', 'Image URL is empty.' );
        }

        // Clean URL to strip query parameters (strtok URL 클렌징)
        $clean_url = strtok( $image_url, '?' );
        $clean_url = esc_url_raw( trim( $clean_url ) );

        if ( empty( $clean_url ) ) {
            return new WP_Error( 'invalid_url', 'Cleaned image URL is invalid.' );
        }

        // 1. Check if post already has a thumbnail (get_post_thumbnail_id 체크)
        $existing_thumbnail_id = get_post_thumbnail_id( $post_id );
        if ( $existing_thumbnail_id ) {
            return $existing_thumbnail_id;
        }

        // 2. Check if the image has already been downloaded (prevent duplicate attachments)
        global $wpdb;
        $attachment_id = $wpdb->get_var( $wpdb->prepare(
            "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_naver_source_image_url' AND meta_value = %s LIMIT 1",
            $clean_url
        ) );

        if ( $attachment_id ) {
            set_post_thumbnail( $post_id, $attachment_id );
            return (int) $attachment_id;
        }

        // 3. Sideload the image
        if ( ! function_exists( 'media_sideload_image' ) ) {
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';
        }

        // media_sideload_image with 'id' parameter returns the attachment ID
        $attachment_id = media_sideload_image( $clean_url, $post_id, null, 'id' );

        if ( is_wp_error( $attachment_id ) ) {
            return $attachment_id;
        }

        if ( is_numeric( $attachment_id ) && $attachment_id > 0 ) {
            update_post_meta( $attachment_id, '_naver_source_image_url', $clean_url );
            set_post_thumbnail( $post_id, $attachment_id );
            return (int) $attachment_id;
        }

        return new WP_Error( 'sideload_failed', 'Sideloading image failed.' );
    }

    /**
     * Enqueue admin styles in the head section (Separated from render)
     */
    public function enqueue_admin_styles() {
        $screen = get_current_screen();
        if ( $screen && strpos( $screen->id, 'naver-rss-sync-ultimate' ) !== false ) {
            ?>
            <style>
                .nrsu-dashboard-wrap {
                    max-width: 1200px;
                    margin: 30px auto 30px 20px;
                    font-family: 'Outfit', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                    color: #e2e8f0;
                }
                .nrsu-header {
                    background: linear-gradient(135deg, #03C75A 0%, #028a3e 100%);
                    padding: 30px 40px;
                    border-radius: 16px;
                    box-shadow: 0 10px 25px -5px rgba(3, 199, 90, 0.3);
                    margin-bottom: 30px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    position: relative;
                    overflow: hidden;
                }
                .nrsu-header::before {
                    content: '';
                    position: absolute;
                    top: -50%;
                    right: -20%;
                    width: 300px;
                    height: 300px;
                    background: rgba(255, 255, 255, 0.1);
                    border-radius: 50%;
                    pointer-events: none;
                }
                .nrsu-header h1 {
                    color: #ffffff;
                    font-size: 28px;
                    font-weight: 800;
                    margin: 0;
                    letter-spacing: -0.03em;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }
                .nrsu-header-badge {
                    background: rgba(255, 255, 255, 0.2);
                    padding: 4px 12px;
                    border-radius: 99px;
                    font-size: 12px;
                    font-weight: 600;
                    color: #ffffff;
                    text-transform: uppercase;
                }
                .nrsu-grid {
                    display: grid;
                    grid-template-columns: 2fr 1fr;
                    gap: 30px;
                }
                @media (max-width: 960px) {
                    .nrsu-grid {
                        grid-template-columns: 1fr;
                    }
                }
                .nrsu-card {
                    background: #1e293b;
                    border: 1px solid #334155;
                    border-radius: 16px;
                    padding: 30px;
                    margin-bottom: 30px;
                    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                    transition: transform 0.2s ease, box-shadow 0.2s ease;
                }
                .nrsu-card:hover {
                    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                }
                .nrsu-card h2 {
                    color: #f8fafc;
                    margin-top: 0;
                    margin-bottom: 25px;
                    font-size: 20px;
                    font-weight: 700;
                    border-bottom: 1px solid #334155;
                    padding-bottom: 15px;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }
                .nrsu-field {
                    margin-bottom: 25px;
                }
                .nrsu-field label.nrsu-label {
                    display: block;
                    font-weight: 600;
                    margin-bottom: 10px;
                    color: #cbd5e1;
                    font-size: 14px;
                }
                .nrsu-input-text, .nrsu-select {
                    width: 100%;
                    background: #0f172a !important;
                    border: 1px solid #475569 !important;
                    color: #f8fafc !important;
                    padding: 12px 16px !important;
                    border-radius: 8px !important;
                    font-size: 14px !important;
                    transition: border-color 0.2s, box-shadow 0.2s;
                    box-shadow: none !important;
                    height: auto !important;
                }
                .nrsu-input-text:focus, .nrsu-select:focus {
                    border-color: #03C75A !important;
                    box-shadow: 0 0 0 3px rgba(3, 199, 90, 0.2) !important;
                    outline: none;
                }
                
                /* Styled Radio Group for CPT/Post Selection */
                .nrsu-radio-group {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 15px;
                    margin-top: 10px;
                }
                .nrsu-radio-box {
                    background: #0f172a;
                    border: 1px solid #334155;
                    border-radius: 10px;
                    padding: 15px 20px;
                    cursor: pointer;
                    transition: all 0.2s ease;
                    display: flex;
                    align-items: flex-start;
                    gap: 12px;
                }
                .nrsu-radio-box:hover {
                    border-color: #475569;
                    background: #1e293b;
                }
                .nrsu-radio-box.selected {
                    border-color: #03C75A;
                    background: rgba(3, 199, 90, 0.05);
                    box-shadow: 0 0 0 1px #03C75A;
                }
                .nrsu-radio-box input[type="radio"] {
                    margin-top: 3px;
                    accent-color: #03C75A;
                }
                .nrsu-radio-label-title {
                    font-weight: 700;
                    color: #f8fafc;
                    display: block;
                    margin-bottom: 4px;
                }
                .nrsu-radio-label-desc {
                    font-size: 12px;
                    color: #94a3b8;
                    line-height: 1.4;
                }

                /* Styled Toggle Switch for Gutenberg Override */
                .nrsu-switch-container {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    background: #0f172a;
                    padding: 20px;
                    border-radius: 10px;
                    border: 1px solid #334155;
                }
                .nrsu-switch-info {
                    max-width: 80%;
                }
                .nrsu-switch-title {
                    font-weight: 700;
                    color: #f8fafc;
                    display: block;
                    margin-bottom: 4px;
                }
                .nrsu-switch-desc {
                    font-size: 12px;
                    color: #94a3b8;
                    line-height: 1.4;
                }
                .nrsu-switch {
                    position: relative;
                    display: inline-block;
                    width: 50px;
                    height: 26px;
                }
                .nrsu-switch input {
                    opacity: 0;
                    width: 0;
                    height: 0;
                    margin: 0;
                }
                .nrsu-slider {
                    position: absolute;
                    cursor: pointer;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background-color: #475569;
                    transition: .4s;
                    border-radius: 34px;
                }
                .nrsu-slider:before {
                    position: absolute;
                    content: "";
                    height: 18px;
                    width: 18px;
                    left: 4px;
                    bottom: 4px;
                    background-color: white;
                    transition: .4s;
                    border-radius: 50%;
                }
                .nrsu-switch input:checked + .nrsu-slider {
                    background-color: #03C75A;
                }
                .nrsu-switch input:focus + .nrsu-slider {
                    box-shadow: 0 0 1px #03C75A;
                }
                .nrsu-switch input:checked + .nrsu-slider:before {
                    transform: translateX(24px);
                }

                /* General Helper Elements */
                .nrsu-hint {
                    display: block;
                    font-size: 13px;
                    color: #94a3b8;
                    margin-top: 10px;
                    line-height: 1.5;
                }
                .nrsu-hint-box {
                    background: rgba(3, 199, 90, 0.05);
                    border-left: 4px solid #03C75A;
                    padding: 15px 20px;
                    border-radius: 0 8px 8px 0;
                    font-size: 13px;
                    color: #cbd5e1;
                    line-height: 1.6;
                    margin-top: 10px;
                }
                
                /* Buttons and Actions */
                .nrsu-btn-save {
                    background: #03C75A !important;
                    border: none !important;
                    color: #ffffff !important;
                    padding: 14px 28px !important;
                    border-radius: 8px !important;
                    font-size: 15px !important;
                    font-weight: 700 !important;
                    cursor: pointer !important;
                    width: 100%;
                    box-shadow: 0 4px 14px rgba(3, 199, 90, 0.4) !important;
                    transition: all 0.2s ease !important;
                    text-align: center;
                    line-height: 1 !important;
                    height: auto !important;
                }
                .nrsu-btn-save:hover {
                    background: #02a64b !important;
                    box-shadow: 0 6px 20px rgba(3, 199, 90, 0.6) !important;
                    transform: translateY(-1px);
                }
                .nrsu-btn-action {
                    background: #3b82f6 !important;
                    border: none !important;
                    color: #ffffff !important;
                    padding: 12px 20px !important;
                    border-radius: 8px !important;
                    font-size: 14px !important;
                    font-weight: 600 !important;
                    cursor: pointer !important;
                    width: 100%;
                    transition: all 0.2s ease !important;
                    text-align: center;
                    line-height: 1 !important;
                    height: auto !important;
                    margin-top: 15px;
                }
                .nrsu-btn-action:hover {
                    background: #2563eb !important;
                }
                .nrsu-status-pill {
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    font-size: 12px;
                    font-weight: 600;
                    padding: 4px 10px;
                    border-radius: 99px;
                    background: rgba(3, 199, 90, 0.1);
                    color: #03C75A;
                }
                .nrsu-status-pill.inactive {
                    background: rgba(148, 163, 184, 0.1);
                    color: #94a3b8;
                }
            </style>
            <?php
        }
    }

    /**
     * Print jQuery helper scripts in footer (Separated from render)
     */
    public function print_admin_scripts() {
        $screen = get_current_screen();
        if ( $screen && strpos( $screen->id, 'naver-rss-sync-ultimate' ) !== false ) {
            ?>
            <script>
                jQuery(document).ready(function($) {
                    // Update CSS class on radio boxes based on selection
                    $('input[name="naver_rss_sync_ultimate_settings[post_type_selection]"]').on('change', function() {
                        $('.nrsu-radio-box').removeClass('selected');
                        $(this).closest('.nrsu-radio-box').addClass('selected');
                    });
                });
            </script>
            <?php
        }
    }

    /**
     * Render the admin management dashboard
     */
    public function render_admin_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $opt = wp_parse_args( get_option( $this->option_name, [] ), $this->get_default_options() );

        $v = function( $key, $default = '' ) use ( $opt ) {
            return isset( $opt[ $key ] ) ? esc_attr( $opt[ $key ] ) : $default;
        };

        $c = function( $key, $val ) use ( $opt ) {
            return checked( $val, isset( $opt[ $key ] ) ? $opt[ $key ] : '', false );
        };

        $s = function( $key, $val ) use ( $opt ) {
            return selected( $val, isset( $opt[ $key ] ) ? $opt[ $key ] : '', false );
        };

        ?>
        <div class="wrap nrsu-dashboard-wrap">
            <div class="nrsu-header">
                <h1>
                    <span class="dashicons dashicons-rss" style="font-size: 28px; width: 28px; height: 28px;"></span>
                    Naver RSS Sync Ultimate
                </h1>
                <span class="nrsu-header-badge">Console Skeleton v1.0</span>
            </div>

            <?php if ( isset( $_GET['nrsu_sync_status'] ) ) : ?>
                <?php if ( 'success' === $_GET['nrsu_sync_status'] ) : ?>
                    <div style="background: rgba(3, 199, 90, 0.1); border-left: 4px solid #03C75A; padding: 15px; margin-bottom: 25px; border-radius: 4px; color: #cbd5e1; font-weight: 500;">
                        ✅ <b>동기화 성공:</b> 네이버 블로그 글 수집이 정상적으로 완료되었습니다. (새로 추가된 글: <?php echo isset( $_GET['nrsu_sync_count'] ) ? intval( $_GET['nrsu_sync_count'] ) : 0; ?>개)
                    </div>
                <?php elseif ( 'error' === $_GET['nrsu_sync_status'] ) : ?>
                    <div style="background: rgba(239, 68, 68, 0.1); border-left: 4px solid #ef4444; padding: 15px; margin-bottom: 25px; border-radius: 4px; color: #cbd5e1; font-weight: 500;">
                        ❌ <b>동기화 실패:</b> 오류가 발생했습니다: <?php echo esc_html( urldecode( $_GET['nrsu_sync_msg'] ?? '' ) ); ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="nrsu-grid">
                <div class="nrsu-main">
                    <form id="nrsu-settings-form" method="post" action="options.php">
                        <?php settings_fields( 'naver_rss_sync_ultimate_group' ); ?>
                        
                        <!-- Card 1: Data Source & Destination -->
                        <?php $this->render_basic_settings( $v, $c ); ?>

                        <!-- Card 2: Compatibility & Editor Override -->
                        <?php $this->render_compatibility_settings( $c, $s ); ?>
                    </form>
                </div>

                <!-- Sidebar for save & immediate actions -->
                <div class="nrsu-sidebar">
                    <?php $this->render_sidebar_panel(); ?>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Render Card 1: Data Source Settings
     */
    private function render_basic_settings( $v, $c ) {
        ?>
        <div class="nrsu-card">
            <h2>
                <span class="dashicons dashicons-database-add"></span>
                1. 📡 데이터 소스 및 발행 대상 설정
            </h2>
            
            <div class="nrsu-field">
                <label class="nrsu-label" for="nrsu_rss_url">네이버 블로그 RSS 주소</label>
                <input type="url" id="nrsu_rss_url" class="nrsu-input-text" name="naver_rss_sync_ultimate_settings[rss_url]" value="<?php echo $v('rss_url'); ?>" placeholder="https://rss.blog.naver.com/naver_id.xml">
                <div class="nrsu-hint-box">
                    💡 <b>네이버 블로그 RSS 형식</b>: 반드시 뒤에 <b>.xml</b>이 붙는 주소여야 합니다.<br>
                    예시: <code>https://rss.blog.naver.com/[네이버아이디].xml</code>
                </div>
            </div>

            <div class="nrsu-field">
                <label class="nrsu-label">발행 대상 포스트 타입 (Post Type Destination)</label>
                <div class="nrsu-radio-group">
                    <!-- Options choice CPT -->
                    <label class="nrsu-radio-box <?php echo ( $v('post_type_selection') === 'naver_blog' ) ? 'selected' : ''; ?>">
                        <input type="radio" name="naver_rss_sync_ultimate_settings[post_type_selection]" value="naver_blog" <?php echo $c('post_type_selection', 'naver_blog'); ?>>
                        <div>
                            <span class="nrsu-radio-label-title">네이버 블로그 CPT (naver_blog)</span>
                            <span class="nrsu-radio-label-desc">독립된 '네이버 블로그' 전용 메뉴로 글을 관리하며, 기본 포스트 영역과 완벽히 격리시킵니다.</span>
                        </div>
                    </label>

                    <!-- Options choice Post -->
                    <label class="nrsu-radio-box <?php echo ( $v('post_type_selection') === 'post' ) ? 'selected' : ''; ?>">
                        <input type="radio" name="naver_rss_sync_ultimate_settings[post_type_selection]" value="post" <?php echo $c('post_type_selection', 'post'); ?>>
                        <div>
                            <span class="nrsu-radio-label-title">일반 글 (post)</span>
                            <span class="nrsu-radio-label-desc">기존 워드프레스 블로그 글(Post) 영역에 바로 발행하여 테마 및 플러그인과 손쉽게 연동합니다.</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Render Card 2: Compatibility Settings
     */
    private function render_compatibility_settings( $c, $s ) {
        ?>
        <div class="nrsu-card">
            <h2>
                <span class="dashicons dashicons-admin-settings"></span>
                2. ⚙️ 호환성 및 에디터 강제 제어
            </h2>

            <div class="nrsu-field">
                <div class="nrsu-switch-container">
                    <div class="nrsu-switch-info">
                        <span class="nrsu-switch-title">[오류 방지] 클래식(구버전) 에디터 강제 활성화</span>
                        <span class="nrsu-switch-desc">네이버 블로그 글의 복잡한 HTML 스타일이 최신 블록 에디터(Gutenberg)와 충돌하여 관리자 페이지가 하얗게 깨지거나 경고가 발생하는 현상을 100% 방지합니다.</span>
                    </div>
                    <label class="nrsu-switch">
                        <input type="checkbox" name="naver_rss_sync_ultimate_settings[use_classic_editor]" value="1" <?php echo $c('use_classic_editor', 1); ?>>
                        <span class="nrsu-slider"></span>
                    </label>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="nrsu-field">
                    <label class="nrsu-label" for="nrsu_post_status">기본 발행 상태</label>
                    <select id="nrsu_post_status" class="nrsu-select" name="naver_rss_sync_ultimate_settings[post_status]">
                        <option value="draft" <?php echo $s('post_status', 'draft'); ?>>임시저장 (권장)</option>
                        <option value="publish" <?php echo $s('post_status', 'publish'); ?>>즉시 공개 발행</option>
                    </select>
                </div>

                <div class="nrsu-field">
                    <label class="nrsu-label" for="nrsu_sync_interval">자동 동기화 주기 (WP-Cron)</label>
                    <select id="nrsu_sync_interval" class="nrsu-select" name="naver_rss_sync_ultimate_settings[sync_interval]">
                        <option value="manual" <?php echo $s('sync_interval', 'manual'); ?>>수동 실행 (자동화 비활성)</option>
                        <option value="hourly" <?php echo $s('sync_interval', 'hourly'); ?>>1시간마다</option>
                        <option value="twicedaily" <?php echo $s('sync_interval', 'twicedaily'); ?>>12시간마다</option>
                        <option value="daily" <?php echo $s('sync_interval', 'daily'); ?>>하루에 한 번</option>
                    </select>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Render Card 3: Sidebar Actions Card
     */
    private function render_sidebar_panel() {
        $last_sync_time = get_option( 'naver_rss_sync_ultimate_last_sync' );
        $last_sync_status = get_option( 'naver_rss_sync_ultimate_last_status' );
        $is_active = ! empty( $this->options['rss_url'] );
        ?>
        <div class="nrsu-card" style="border-color: rgba(3, 199, 90, 0.4);">
            <h2>
                <span class="dashicons dashicons-yes-alt" style="color: #03C75A;"></span>
                💾 구성 저장
            </h2>
            <p style="font-size: 13px; color: #94a3b8; margin-top: 0; line-height: 1.5;">
                입력된 네이버 RSS 피드 주소 및 발행 타입, 편집기 호환성 설정을 즉시 저장하고 코어에 적용합니다.
            </p>
            <button type="submit" form="nrsu-settings-form" class="nrsu-btn-save">설정 저장 및 적용</button>
        </div>

        <div class="nrsu-card" style="border-color: #334155;">
            <h2>
                <span class="dashicons dashicons-update" style="color: #3b82f6;"></span>
                ⚡ 수동 작업
            </h2>
            <p style="font-size: 13px; color: #94a3b8; margin-top: 0; line-height: 1.5;">
                설정된 네이버 RSS 피드를 즉시 크롤링하고 워드프레스 DB 포스팅 연동 모듈을 직접 실행합니다.
            </p>
            <div style="margin-bottom: 15px;">
                <div class="nrsu-status-pill <?php echo $is_active ? '' : 'inactive'; ?>">
                    <span class="dashicons <?php echo $is_active ? 'dashicons-yes' : 'dashicons-no'; ?>" style="font-size: 14px; width: 14px; height: 14px; margin-top: 3px;"></span>
                    <?php echo $is_active ? '동기화 모듈 작동 가능' : 'RSS 주소 미설정'; ?>
                </div>
                <?php if ( $last_sync_time ) : ?>
                    <div style="font-size: 12px; color: #94a3b8; margin-top: 8px; line-height: 1.4;">
                         <b>최근 실행:</b> <?php echo esc_html( $last_sync_time ); ?><br>
                         <b>결과:</b> <?php echo esc_html( $last_sync_status ); ?>
                    </div>
                <?php endif; ?>
            </div>
            <form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">
                <input type="hidden" name="action" value="nrsu_run_sync">
                <?php wp_nonce_field( 'nrsu_run_sync_action', 'nrsu_sync_nonce' ); ?>
                <button type="submit" class="nrsu-btn-action" <?php echo $is_active ? '' : 'disabled style="opacity: 0.5; cursor: not-allowed;"'; ?>>즉시 동기화 실행</button>
            </form>
        </div>
        <?php
    }
}

// Initialize Singleton
add_action( 'plugins_loaded', [ 'Naver_RSS_Sync_Ultimate', 'get_instance' ] );
register_deactivation_hook( __FILE__, [ 'Naver_RSS_Sync_Ultimate', 'deactivate' ] );
