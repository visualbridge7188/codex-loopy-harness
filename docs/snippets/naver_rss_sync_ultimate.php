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
        // Load options
        $this->options = get_option( $this->option_name ) ?: $this->get_default_options();

        // 1. CPT Registration
        add_action( 'init', [ $this, 'register_naver_blog_cpt' ] );

        // 2. Admin Logic
        if ( is_admin() ) {
            add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
            add_action( 'admin_init', [ $this, 'register_settings' ] );
            add_action( 'admin_head', [ $this, 'enqueue_admin_styles' ] );
            add_action( 'admin_footer', [ $this, 'print_admin_scripts' ] );
        }

        // 3. Classic Editor Override Filter (Gutenberg Block Editor bypass)
        add_filter( 'use_block_editor_for_post_type', [ $this, 'disable_gutenberg_editor' ], 10, 2 );
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
    public function disable_gutenberg_editor( $use_block_editor, $post_type ) {
        $force_classic = $this->options['use_classic_editor'] ?? 1;
        $target_post_type = $this->options['post_type_selection'] ?? 'naver_blog';

        if ( $force_classic ) {
            // Apply classic editor block to the configured target post type, and always to 'naver_blog' CPT
            if ( $post_type === $target_post_type || $post_type === 'naver_blog' ) {
                return false;
            }
        }
        return $use_block_editor;
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

        $opt = get_option( $this->option_name ) ?: $this->get_default_options();

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
                (뼈대 단계) 향후 구현될 RSS 즉시 크롤링 및 워드프레스 DB 포스팅 연동 모듈을 실행합니다.
            </p>
            <div class="nrsu-status-pill inactive">
                <span class="dashicons dashicons-no" style="font-size: 14px; width: 14px; height: 14px;"></span>
                동기화 모듈 미작동 (뼈대 단계)
            </div>
            <button type="button" class="nrsu-btn-action" disabled style="opacity: 0.5; cursor: not-allowed;">즉시 동기화 실행 (비활성)</button>
        </div>
        <?php
    }
}

// Initialize Singleton
add_action( 'plugins_loaded', [ 'Naver_RSS_Sync_Ultimate', 'get_instance' ] );
