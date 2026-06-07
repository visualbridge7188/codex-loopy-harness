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

        // 5. SEO & Canonical hooks
        add_action( 'wp_head', [ $this, 'inject_canonical_and_noindex' ], 1 );
        add_filter( 'wp_sitemaps_post_types', [ $this, 'exclude_from_sitemaps' ] );
        add_filter( 'wpseo_canonical', [ $this, 'override_canonical' ] );
        add_filter( 'rank_math/frontend/canonical', [ $this, 'override_canonical' ] );

        // 6. Shortcode registration
        add_shortcode( 'naver_rss_archive', [ $this, 'render_archive_shortcode' ] );
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
            'archive_style'       => 'toss',
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

        // Archive Style selection validation
        $output['archive_style'] = ( isset( $input['archive_style'] ) && in_array( $input['archive_style'], [ 'toss', 'magazine' ], true ) ) ? $input['archive_style'] : 'toss';

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
            $content = wp_kses_post( (string) $item->description );
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
        if ( ! function_exists( 'media_handle_sideload' ) ) {
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';
        }

        // Add filter to bypass Naver CDN hotlinking protection (Referer and User-Agent check)
        $referer_filter = function( $args, $url ) {
            $args['headers']['Referer'] = 'https://blog.naver.com/';
            $args['headers']['User-Agent'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';
            return $args;
        };
        add_filter( 'http_request_args', $referer_filter, 10, 2 );

        // Download the image to a temp file
        $tmp_file = download_url( $clean_url, 15 );

        // Remove filter immediately after download
        remove_filter( 'http_request_args', $referer_filter, 10 );

        if ( is_wp_error( $tmp_file ) ) {
            return $tmp_file;
        }

        // Determine correct file name/extension
        $url_path = parse_url( $clean_url, PHP_URL_PATH );
        $filename = basename( $url_path );
        
        // Default to jpg extension if none exists in the filename
        if ( ! pathinfo( $filename, PATHINFO_EXTENSION ) ) {
            $filename .= '.jpg';
        }

        $file_array = [
            'tmp_name' => $tmp_file,
            'name'     => $filename,
        ];

        // Sideload the image and create attachment
        $attachment_id = media_handle_sideload( $file_array, $post_id );

        if ( is_wp_error( $attachment_id ) ) {
            @unlink( $tmp_file );
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
                    $('.nrsu-radio-group input[type="radio"]').on('change', function() {
                        $(this).closest('.nrsu-radio-group').find('.nrsu-radio-box').removeClass('selected');
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
                        <?php $this->render_compatibility_settings( $v, $c, $s ); ?>
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
    private function render_compatibility_settings( $v, $c, $s ) {
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

            <div class="nrsu-field">
                <label class="nrsu-label">기본 아카이브 스타일 (Default Archive Style)</label>
                <div class="nrsu-radio-group">
                    <!-- Toss Style -->
                    <label class="nrsu-radio-box <?php echo ( $v('archive_style') === 'toss' ) ? 'selected' : ''; ?>">
                        <input type="radio" name="naver_rss_sync_ultimate_settings[archive_style]" value="toss" <?php echo $c('archive_style', 'toss'); ?>>
                        <div>
                            <span class="nrsu-radio-label-title">Toss 스타일 (Responsive Card)</span>
                            <span class="nrsu-radio-label-desc">라이트 모드 친화적인 미니멀 반응형 카드 디자인으로 가독성을 높입니다.</span>
                        </div>
                    </label>

                    <!-- Magazine Style -->
                    <label class="nrsu-radio-box <?php echo ( $v('archive_style') === 'magazine' ) ? 'selected' : ''; ?>">
                        <input type="radio" name="naver_rss_sync_ultimate_settings[archive_style]" value="magazine" <?php echo $c('archive_style', 'magazine'); ?>>
                        <div>
                            <span class="nrsu-radio-label-title">Magazine 스타일 (Dark Grid)</span>
                            <span class="nrsu-radio-label-desc">노바미라 스타일의 고급스러운 어두운 그리드 디자인을 적용합니다.</span>
                        </div>
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

    /**
     * Inject canonical tag and noindex robots meta for imported posts
     */
    public function inject_canonical_and_noindex() {
        if ( ! is_singular() ) {
            return;
        }

        $post_id = get_the_ID();
        if ( ! $post_id ) {
            return;
        }

        $naver_url = get_post_meta( $post_id, '_naver_link', true );
        if ( empty( $naver_url ) ) {
            $naver_url = get_post_meta( $post_id, '_naver_original_url', true );
        }

        if ( empty( $naver_url ) ) {
            return;
        }

        // Disable WordPress default canonical link
        remove_action( 'wp_head', 'rel_canonical' );

        // Inject canonical tag
        echo '<link rel="canonical" href="' . esc_url( $naver_url ) . '" />' . "\n";

        // Inject noindex robots meta tag
        echo '<meta name="robots" content="noindex, nofollow" />' . "\n";
    }

    /**
     * Exclude the custom post type from WordPress default sitemaps to prevent indexing
     * 
     * @param array $post_types
     * @return array
     */
    public function exclude_from_sitemaps( $post_types ) {
        $target_post_type = $this->options['post_type_selection'] ?? 'naver_blog';
        if ( 'naver_blog' === $target_post_type && isset( $post_types['naver_blog'] ) ) {
            unset( $post_types['naver_blog'] );
        }
        return $post_types;
    }

    /**
     * Override canonical URL for Yoast SEO and Rank Math
     * 
     * @param string $canonical
     * @return string
     */
    public function override_canonical( $canonical ) {
        if ( is_singular() ) {
            $post_id = get_the_ID();
            if ( $post_id ) {
                $naver_url = get_post_meta( $post_id, '_naver_link', true );
                if ( empty( $naver_url ) ) {
                    $naver_url = get_post_meta( $post_id, '_naver_original_url', true );
                }
                if ( ! empty( $naver_url ) ) {
                    return $naver_url;
                }
            }
        }
        return $canonical;
    }

    /**
     * Render the Naver RSS archive shortcode.
     * 
     * @param array $atts
     * @return string
     */
    public function render_archive_shortcode( $atts ) {
        $atts = shortcode_atts(
            [
                'style' => $this->options['archive_style'] ?? 'toss',
                'limit' => 10,
            ],
            $atts,
            'naver_rss_archive'
        );

        $limit = intval( $atts['limit'] );
        $style = in_array( $atts['style'], [ 'toss', 'magazine' ], true ) ? $atts['style'] : 'toss';

        $post_type = $this->options['post_type_selection'] ?? 'naver_blog';

        $args = [
            'post_type'      => $post_type,
            'post_status'    => 'publish',
            'posts_per_page' => $limit,
            'meta_query'     => [
                [
                    'key'     => '_naver_guid',
                    'compare' => 'EXISTS',
                ],
            ],
            'no_found_rows'  => true,
        ];

        $query = new WP_Query( $args );

        if ( ! $query->have_posts() ) {
            wp_reset_postdata();
            return '<p class="nrsu-no-posts">' . esc_html__( '등록된 네이버 블로그 글이 없습니다.', 'naver-rss-sync-ultimate' ) . '</p>';
        }

        ob_start();
        if ( 'magazine' === $style ) {
            $this->render_magazine_html( $query );
        } else {
            $this->render_toss_html( $query );
        }
        $html = ob_get_clean();

        wp_reset_postdata();
        return $html;
    }

    /**
     * Render Toss style layout HTML.
     * 
     * @param WP_Query $query
     */
    private function render_toss_html( $query ) {
        ?>
        <style>
            .nrsu-toss-archive {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                gap: 24px;
                padding: 16px 0;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            }
            .nrsu-toss-card {
                background: #ffffff;
                border-radius: 18px;
                overflow: hidden;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
                transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.3s ease;
                border: 1px solid rgba(0, 0, 0, 0.04);
                display: flex;
                flex-direction: column;
                height: 100%;
            }
            .nrsu-toss-card:hover {
                transform: translateY(-6px);
                box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
            }
            .nrsu-toss-thumb-link {
                display: block;
                width: 100%;
                padding-top: 56.25%;
                position: relative;
                background: #f1f3f5;
                overflow: hidden;
            }
            .nrsu-toss-thumb {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.5s ease;
            }
            .nrsu-toss-card:hover .nrsu-toss-thumb {
                transform: scale(1.05);
            }
            .nrsu-toss-content {
                padding: 24px;
                display: flex;
                flex-direction: column;
                flex-grow: 1;
            }
            .nrsu-toss-meta {
                font-size: 13px;
                color: #868e96;
                margin-bottom: 8px;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .nrsu-toss-badge {
                background: rgba(3, 199, 90, 0.08);
                color: #03C75A;
                font-weight: 600;
                padding: 2px 8px;
                border-radius: 4px;
                font-size: 11px;
                text-transform: uppercase;
            }
            .nrsu-toss-title {
                font-size: 18px;
                font-weight: 700;
                line-height: 1.4;
                margin: 0 0 12px 0;
                color: #191f28;
            }
            .nrsu-toss-title a {
                color: inherit;
                text-decoration: none;
                transition: color 0.2s ease;
            }
            .nrsu-toss-title a:hover {
                color: #03C75A;
            }
            .nrsu-toss-excerpt {
                font-size: 14px;
                line-height: 1.6;
                color: #4e5968;
                margin-bottom: 20px;
                flex-grow: 1;
                display: -webkit-box;
                -webkit-line-clamp: 3;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            .nrsu-toss-footer {
                border-top: 1px solid #f1f3f5;
                padding-top: 16px;
                margin-top: auto;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .nrsu-toss-readmore {
                font-size: 13px;
                font-weight: 600;
                color: #03C75A;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 4px;
            }
            .nrsu-toss-readmore:hover {
                color: #02a64b;
            }
            .nrsu-toss-svg-placeholder {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                color: #dee2e6;
            }
        </style>
        <div class="nrsu-toss-archive css-toss-wrap">
            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                <?php
                $permalink = get_permalink();
                $title     = get_the_title();
                $date      = get_the_date( 'Y.m.d' );
                $excerpt   = get_the_excerpt();
                if ( empty( $excerpt ) ) {
                    $excerpt = wp_strip_all_tags( get_the_content() );
                }
                $excerpt = wp_html_excerpt( $excerpt, 120, '...' );
                ?>
                <article class="nrsu-toss-card toss-card-item">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <a href="<?php echo esc_url( $permalink ); ?>" class="nrsu-toss-thumb-link">
                            <?php the_post_thumbnail( 'medium_large', [ 'class' => 'nrsu-toss-thumb' ] ); ?>
                        </a>
                    <?php else : ?>
                        <a href="<?php echo esc_url( $permalink ); ?>" class="nrsu-toss-thumb-link">
                            <div class="nrsu-toss-svg-placeholder">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                    <polyline points="21 15 16 10 5 21"></polyline>
                                </svg>
                            </div>
                        </a>
                    <?php endif; ?>
                    <div class="nrsu-toss-content">
                        <div class="nrsu-toss-meta">
                            <span class="nrsu-toss-badge"><?php esc_html_e( 'Naver Blog', 'naver-rss-sync-ultimate' ); ?></span>
                            <span class="nrsu-toss-date"><?php echo esc_html( $date ); ?></span>
                        </div>
                        <h3 class="nrsu-toss-title">
                            <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
                        </h3>
                        <p class="nrsu-toss-excerpt"><?php echo esc_html( $excerpt ); ?></p>
                        <div class="nrsu-toss-footer">
                            <a href="<?php echo esc_url( $permalink ); ?>" class="nrsu-toss-readmore">
                                <?php esc_html_e( '자세히 보기', 'naver-rss-sync-ultimate' ); ?>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                            </a>
                        </div>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
        <?php
    }

    /**
     * Render Magazine style layout HTML.
     * 
     * @param WP_Query $query
     */
    private function render_magazine_html( $query ) {
        ?>
        <style>
            .nrsu-magazine-archive {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                gap: 30px;
                padding: 30px;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                background: #0b0f19;
                border-radius: 20px;
                border: 1px solid #1f293d;
            }
            .nrsu-magazine-card {
                background: #111827;
                border: 1px solid #1f2937;
                border-radius: 16px;
                overflow: hidden;
                transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
                display: flex;
                flex-direction: column;
                height: 100%;
                position: relative;
            }
            .nrsu-magazine-card:hover {
                transform: translateY(-8px);
                border-color: #3b82f6;
                box-shadow: 0 20px 40px rgba(59, 130, 246, 0.15);
            }
            .nrsu-magazine-thumb-link {
                display: block;
                width: 100%;
                padding-top: 60%;
                position: relative;
                overflow: hidden;
                background: #1f2937;
            }
            .nrsu-magazine-thumb {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.7s cubic-bezier(0.16, 1, 0.3, 1);
                filter: brightness(0.9);
            }
            .nrsu-magazine-card:hover .nrsu-magazine-thumb {
                transform: scale(1.08);
                filter: brightness(1.05);
            }
            .nrsu-magazine-content {
                padding: 26px;
                display: flex;
                flex-direction: column;
                flex-grow: 1;
            }
            .nrsu-magazine-meta {
                font-size: 12px;
                font-weight: 500;
                color: #6b7280;
                margin-bottom: 12px;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .nrsu-magazine-badge {
                background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
                color: #ffffff;
                font-weight: 700;
                padding: 3px 10px;
                border-radius: 6px;
                font-size: 10px;
            }
            .nrsu-magazine-title {
                font-size: 20px;
                font-weight: 800;
                line-height: 1.35;
                margin: 0 0 14px 0;
                color: #f3f4f6;
                letter-spacing: -0.02em;
            }
            .nrsu-magazine-title a {
                color: inherit;
                text-decoration: none;
                transition: color 0.3s ease;
            }
            .nrsu-magazine-title a:hover {
                color: #3b82f6;
            }
            .nrsu-magazine-excerpt {
                font-size: 14px;
                line-height: 1.6;
                color: #9ca3af;
                margin-bottom: 24px;
                flex-grow: 1;
                display: -webkit-box;
                -webkit-line-clamp: 3;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            .nrsu-magazine-footer {
                border-top: 1px solid #1f2937;
                padding-top: 18px;
                margin-top: auto;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .nrsu-magazine-date {
                color: #4b5563;
                font-size: 12px;
            }
            .nrsu-magazine-readmore {
                font-size: 13px;
                font-weight: 700;
                color: #3b82f6;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 4px;
                transition: gap 0.2s ease;
            }
            .nrsu-magazine-readmore:hover {
                color: #60a5fa;
                gap: 6px;
            }
            .nrsu-magazine-svg-placeholder {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                color: #4b5563;
            }
        </style>
        <div class="nrsu-magazine-archive css-magazine-wrap">
            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                <?php
                $permalink = get_permalink();
                $title     = get_the_title();
                $date      = get_the_date( 'Y.m.d' );
                $excerpt   = get_the_excerpt();
                if ( empty( $excerpt ) ) {
                    $excerpt = wp_strip_all_tags( get_the_content() );
                }
                $excerpt = wp_html_excerpt( $excerpt, 120, '...' );
                ?>
                <article class="nrsu-magazine-card magazine-card-item">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <a href="<?php echo esc_url( $permalink ); ?>" class="nrsu-magazine-thumb-link">
                            <?php the_post_thumbnail( 'medium_large', [ 'class' => 'nrsu-magazine-thumb' ] ); ?>
                        </a>
                    <?php else : ?>
                        <a href="<?php echo esc_url( $permalink ); ?>" class="nrsu-magazine-thumb-link">
                            <div class="nrsu-magazine-svg-placeholder">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                    <polyline points="21 15 16 10 5 21"></polyline>
                                </svg>
                            </div>
                        </a>
                    <?php endif; ?>
                    <div class="nrsu-magazine-content">
                        <div class="nrsu-magazine-meta">
                            <span class="nrsu-magazine-badge"><?php esc_html_e( 'Naver Blog', 'naver-rss-sync-ultimate' ); ?></span>
                            <span class="nrsu-magazine-date"><?php echo esc_html( $date ); ?></span>
                        </div>
                        <h3 class="nrsu-magazine-title">
                            <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
                        </h3>
                        <p class="nrsu-magazine-excerpt"><?php echo esc_html( $excerpt ); ?></p>
                        <div class="nrsu-magazine-footer">
                            <span class="nrsu-magazine-date"><?php echo esc_html( $date ); ?></span>
                            <a href="<?php echo esc_url( $permalink ); ?>" class="nrsu-magazine-readmore">
                                <?php esc_html_e( '자세히 보기', 'naver-rss-sync-ultimate' ); ?>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                            </a>
                        </div>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
        <?php
    }

    /**
     * Clear scheduled cron hook on plugin deactivation.
     */
    public static function deactivate() {
        wp_clear_scheduled_hook( 'naver_rss_sync_cron_hook' );
    }
}

// Initialize Singleton
add_action( 'plugins_loaded', [ 'Naver_RSS_Sync_Ultimate', 'get_instance' ] );
register_deactivation_hook( __FILE__, [ 'Naver_RSS_Sync_Ultimate', 'deactivate' ] );
