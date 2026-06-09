/**
 * 네이버 RSS -> 워드프레스 자동 발행 통합 시스템 (v9.3 SEO & UI Update)
 * * [업데이트 사항]
 * 1. 설정 저장 버튼 우측 사이드바로 이동 (UX 개선)
 * 2. 검색엔진 중복문서 페널티 방지를 위한 Canonical 태그 자동 주입 (Yoast, RankMath 호환)
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Naver_RSS_Auto_Poster_V9_3 {
    private $option_name = 'naver_rss_settings';
    private $options;

    public function __construct() {
        $this->options = get_option( $this->option_name ) ?: [];

        if ( is_admin() ) {
            add_action( 'admin_menu', [ $this, 'add_plugin_page' ] );
            add_action( 'admin_init', [ $this, 'page_init' ] );
            add_action( 'update_option_' . $this->option_name, [ $this, 'update_cron_schedule' ], 10, 3 );
            add_action( 'admin_enqueue_scripts', [ $this, 'load_media_script' ] );
            add_action( 'add_meta_boxes', [ $this, 'add_custom_meta_box' ] );
            add_action( 'save_post', [ $this, 'save_custom_meta_box' ] );
        }

        $this->options = get_option( $this->option_name ) ?: [];

        add_filter( 'use_block_editor_for_post_type', [ $this, 'disable_gutenberg' ], 10, 2 );
        add_action( 'admin_post_naver_rss_manual_sync', [ $this, 'manual_sync_trigger' ] );
        add_action( 'naver_rss_cron_hook', [ $this, 'run_sync' ] );
        add_action( 'template_redirect', [ $this, 'mode_b_redirect' ] );
        
        // [신규] SEO Canonical 태그 주입 훅
        add_action( 'wp_head', [ $this, 'inject_canonical_url' ], 1 );
    }

    public function load_media_script($hook) {
        if ( $hook === 'settings_page_naver-rss-sync' ) wp_enqueue_media();
    }

    public function add_plugin_page() {
        add_options_page( '네이버 RSS 자동 발행', '네이버 RSS 설정', 'manage_options', 'naver-rss-sync', [ $this, 'create_admin_page' ] );
    }

    public function create_admin_page() {
        $opt = get_option( $this->option_name ) ?: [];
        $report = get_transient( 'naver_rss_last_report' );
        
        $v = function($key, $default='') use ($opt) { return isset($opt[$key]) ? esc_attr($opt[$key]) : $default; };
        $c = function($key, $val) use ($opt) { return checked($val, isset($opt[$key]) ? $opt[$key] : '', false); };
        $s = function($key, $val) use ($opt) { return selected($val, isset($opt[$key]) ? $opt[$key] : '', false); };
        
        $thumb_id = $opt['default_thumb_id'] ?? '';
        $thumb_url = '';
        if ( !empty($thumb_id) ) {
            $img_attr = wp_get_attachment_image_src($thumb_id, 'medium');
            if ($img_attr) $thumb_url = $img_attr[0];
        }
        
        ?>
        <style>
            #sync-loader { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.9); z-index:99999; text-align:center; padding-top:15%; }
            .spinner-visual { width:60px; height:60px; border:6px solid #e0e0e0; border-top:6px solid #03C75A; border-radius:50%; animation: spin 1s linear infinite; margin:0 auto 20px; }
            @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
            
            .nrap-wrap { max-width: 1200px; margin: 20px 20px 20px 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif; }
            .nrap-header { background: linear-gradient(135deg, #03C75A 0%, #009944 100%); color: white; padding: 25px 30px; border-radius: 10px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
            .nrap-header h1 { color: white; margin: 0; font-size: 24px; font-weight: 700; }
            
            .nrap-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 25px; }
            @media (max-width: 900px) { .nrap-grid { grid-template-columns: 1fr; } }
            
            .nrap-card { background: #fff; border: 1px solid #e2e4e7; border-radius: 8px; padding: 25px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
            .nrap-card h2 { margin-top: 0; border-bottom: 2px solid #f0f0f1; padding-bottom: 15px; font-size: 16px; color: #1d2327; }
            
            .nrap-field { margin-bottom: 25px; }
            .nrap-field label { display: block; font-weight: 600; margin-bottom: 8px; color: #2c3338; }
            .nrap-field input[type="text"], .nrap-field input[type="url"], .nrap-field input[type="number"], .nrap-field select, .nrap-field textarea { width: 100%; max-width: 100%; box-sizing: border-box; padding: 8px 12px; border: 1px solid #8c8f94; border-radius: 4px; box-shadow: inset 0 1px 2px rgba(0,0,0,0.07); }
            .nrap-field input:focus, .nrap-field textarea:focus { border-color: #03C75A; box-shadow: 0 0 0 1px #03C75A; outline: none; }
            
            .nrap-hint { display: block; font-size: 13px; color: #50575e; margin-top: 8px; line-height: 1.6; background: #f8f9f9; padding: 10px 15px; border-left: 3px solid #03C75A; border-radius: 0 4px 4px 0; }
            
            .nrap-btn-primary { background: #03C75A !important; color: white !important; font-size: 15px !important; padding: 12px 25px !important; height: auto !important; font-weight: 600 !important; border:none; border-radius:4px; cursor:pointer; width: 100%; }
            .nrap-btn-primary:hover { background: #00b350 !important; }
            .nrap-btn-action { width: 100%; text-align: center; padding: 12px !important; font-size: 15px !important; margin-top: 10px; background: #2271b1 !important; color: white !important; border-radius: 4px !important; border:none; cursor:pointer; font-weight:600 !important; }
            .nrap-btn-action:hover { background: #135e96 !important; }
            
            .nrap-thumb-preview-box { width: 150px; height: 120px; border: 2px dashed #c3c4c7; background: #f0f0f1; border-radius: 6px; display: flex; align-items: center; justify-content: center; overflow: hidden; margin-bottom: 10px; }
            .nrap-thumb-preview-box img { max-width: 100%; max-height: 100%; object-fit: cover; }
            .report-list { background: #f6f7f7; padding: 10px 15px; border-radius: 4px; margin-top: 15px; font-size: 13px; max-height: 150px; overflow-y: auto; }
        </style>
        
        <script>
            jQuery(document).ready(function($){
                var mediaUploader;
                $('#nrap_upload_btn').on('click', function(e) {
                    e.preventDefault();
                    if (mediaUploader) { mediaUploader.open(); return; }
                    mediaUploader = wp.media({ title: '기본 썸네일로 사용할 이미지 선택', button: { text: '이 이미지 사용하기' }, multiple: false });
                    mediaUploader.on('select', function() {
                        var attachment = mediaUploader.state().get('selection').first().toJSON();
                        $('#nrap_default_thumb_id').val(attachment.id);
                        $('#nrap_thumb_preview_box').html('<img src="' + attachment.url + '">');
                        $('#nrap_remove_btn').show();
                    });
                    mediaUploader.open();
                });

                $('#nrap_remove_btn').on('click', function(e){
                    e.preventDefault();
                    $('#nrap_default_thumb_id').val('');
                    $('#nrap_thumb_preview_box').html('<span style="color:#8c8f94; font-size:12px;">이미지 없음</span>');
                    $(this).hide();
                });
            });

            function showSyncLoader() {
                if(confirm('🌐 수동 동기화를 시작합니다. 이미지를 서버로 가져오기 때문에 다소 시간이 소요될 수 있습니다.')) {
                    document.getElementById('sync-loader').style.display = 'block';
                    return true;
                }
                return false;
            }
        </script>

        <div id="sync-loader"><div class="spinner-visual"></div><h2 style="font-weight:bold; color:#333;">네이버 블로그를 워드프레스로 복사 중입니다...</h2><p>창을 닫지 말고 잠시만 기다려주세요.</p></div>

        <div class="nrap-wrap">
            <div class="nrap-header">
                <h1>🚀 네이버 블로그 자동 발행 통합 제어반</h1>
                <div>v9.3 SEO & UI Update</div>
            </div>

            <div class="nrap-grid">
                <div class="nrap-main">
                    <form id="nrap-settings-form" method="post" action="options.php">
                        <?php settings_fields( 'naver_rss_option_group' ); ?>
                        
                        <div class="nrap-card">
                            <h2>1. 📡 데이터 소스 (어디서 가져올까요?)</h2>
                            <div class="nrap-field">
                                <label>네이버 블로그 RSS 주소</label>
                                <input type="url" name="<?php echo $this->option_name; ?>[rss_url]" value="<?php echo $v('rss_url', 'https://rss.blog.naver.com/nest_amc.xml'); ?>">
                                <span class="nrap-hint">💡 일반적인 네이버 블로그 주소가 아닌, 뒤에 <b>.xml</b>이 붙는 RSS 전용 주소를 입력하셔야 시스템이 데이터를 읽을 수 있습니다.<br>(예시: https://rss.blog.naver.com/아이디.xml)</span>
                            </div>
                            
                            <div style="display:flex; gap:30px; align-items:flex-start; margin-top:30px;">
                                <div class="nrap-field" style="flex:1;">
                                    <label>한 번에 가져올 최신 글 개수</label>
                                    <input type="number" name="<?php echo $this->option_name; ?>[fetch_count]" value="<?php echo $v('fetch_count', '5'); ?>" min="1" max="50">
                                    <span class="nrap-hint">💡 시스템이 한 번 돌 때마다 검사할 네이버 최신 글의 개수입니다. 숫자가 너무 크면 서버가 느려지거나 멈출 수 있으므로 <b>5~10개를 권장</b>합니다.</span>
                                </div>
                                <div class="nrap-field" style="flex:1;">
                                    <label>🖼️ 기본 썸네일 (이미지가 없는 글 대비용)</label>
                                    <div class="nrap-thumb-preview-box" id="nrap_thumb_preview_box">
                                        <?php echo $thumb_url ? '<img src="'.esc_url($thumb_url).'">' : '<span style="color:#8c8f94; font-size:12px;">이미지 없음</span>'; ?>
                                    </div>
                                    <input type="hidden" id="nrap_default_thumb_id" name="<?php echo $this->option_name; ?>[default_thumb_id]" value="<?php echo esc_attr($thumb_id); ?>">
                                    <button type="button" class="button" id="nrap_upload_btn">라이브러리에서 선택</button>
                                    <button type="button" class="button" id="nrap_remove_btn" style="<?php echo $thumb_url ? '' : 'display:none;'; ?> color:#d63638; border-color:#d63638;">제거</button>
                                    <span class="nrap-hint">💡 네이버 블로그 원문에 사진이 한 장도 없을 경우 엑스박스가 뜨는 것을 방지합니다. 센터의 <b>로고나 예쁜 기본 안내 이미지</b>를 하나 지정해 두시면 좋습니다.</span>
                                </div>
                            </div>
                        </div>

                        <div class="nrap-card">
                            <h2>2. 🗂️ 카테고리 매핑 (어디에 분류할까요?)</h2>
                            <div class="nrap-field">
                                <label>매핑 규칙 작성</label>
                                <textarea name="<?php echo $this->option_name; ?>[category_mapping]" rows="4" placeholder="예시: 식사:3, 프로그램:5"><?php echo isset($opt['category_mapping']) ? esc_textarea($opt['category_mapping']) : ''; ?></textarea>
                                <span class="nrap-hint">💡 네이버의 카테고리를 워드프레스의 특정 카테고리에 넣고 싶을 때 사용합니다.<br>작성법: <code>네이버카테고리명:워드프레스ID번호</code> 형태로 적고 쉼표(,)로 이어주세요.<br>(예시: 식사:3, 프로그램:5, 일상:12)</span>
                            </div>
                            <div class="nrap-field" style="margin-bottom:0;">
                                <label style="font-weight:normal; display:flex; align-items:center; gap:8px;">
                                    <input type="checkbox" name="<?php echo $this->option_name; ?>[auto_create_category]" value="1" <?php echo $c('auto_create_category', 1); ?>> <b>카테고리 자동 생성 허용</b>
                                </label>
                                <span class="nrap-hint">💡 체크하시면, 위 매핑 규칙에 적혀있지 않은 새로운 카테고리의 글이 네이버에서 넘어올 때 '미분류'로 보내지 않고 똑같은 이름의 카테고리를 워드프레스에 스스로 만듭니다.</span>
                            </div>
                        </div>

                        <div class="nrap-card">
                            <h2>3. ⚙️ 시스템 작동 제어 (어떻게 운영할까요?)</h2>
                            <div class="nrap-field">
                                <label>프론트엔드 작동 모드 선택</label>
                                <label style="display:block; margin-bottom:12px; font-weight:normal; background:#f9f9f9; padding:10px; border-radius:4px;">
                                    <input type="radio" name="<?php echo $this->option_name; ?>[operation_mode]" value="A" <?php echo $c('operation_mode', 'A') ?: 'checked'; ?>> 
                                    <strong>모드 A (본문 체류형):</strong> 워드프레스에 글과 이미지를 복사해 옵니다. <b>(SEO 페널티를 막기 위해 원본 네이버 링크를 Canonical 태그로 자동 삽입합니다)</b>
                                </label>
                                <label style="display:block; font-weight:normal; background:#f9f9f9; padding:10px; border-radius:4px;">
                                    <input type="radio" name="<?php echo $this->option_name; ?>[operation_mode]" value="B" <?php echo $c('operation_mode', 'B'); ?>> 
                                    <strong>모드 B (네이버 강제 이동형):</strong> 방문자가 글을 클릭하면 즉시 <b>원본 네이버 블로그로 납치(리디렉션)</b>시킵니다. 네이버 조회수를 올리고 싶을 때 유리합니다.
                                </label>
                            </div>
                            
                            <div style="border-top:1px dashed #e2e4e7; margin: 25px 0;"></div>
                            
                            <div style="display:flex; gap:30px;">
                                <div style="flex:1;">
                                    <label>기본 발행 상태</label>
                                    <select name="<?php echo $this->option_name; ?>[post_status]">
                                        <option value="draft" <?php echo $s('post_status', 'draft'); ?>>임시저장 (권장)</option>
                                        <option value="publish" <?php echo $s('post_status', 'publish'); ?>>즉시 게시</option>
                                    </select>
                                    <span class="nrap-hint">💡 처음 세팅하실 때는 반드시 <b>임시저장</b>으로 두고 글이 깨지지 않고 잘 들어오는지 테스트하시는 것이 안전합니다.</span>
                                </div>
                                <div style="flex:1;">
                                    <label>자동 동기화 주기 (WP-Cron)</label>
                                    <select name="<?php echo $this->option_name; ?>[sync_interval]">
                                        <option value="manual" <?php echo $s('sync_interval', 'manual'); ?>>수동 (자동화 끄기)</option>
                                        <option value="hourly" <?php echo $s('sync_interval', 'hourly'); ?>>1시간마다</option>
                                        <option value="twicedaily" <?php echo $s('sync_interval', 'twicedaily'); ?>>12시간마다</option>
                                        <option value="daily" <?php echo $s('sync_interval', 'daily'); ?>>하루 1번</option>
                                    </select>
                                    <span class="nrap-hint">💡 수동으로 설정하면 오직 우측의 '즉시 동기화' 버튼을 누를 때만 작동합니다. 완전히 자동화하시려면 시간을 선택하세요.</span>
                                </div>
                            </div>
                            
                            <div style="border-top:1px dashed #e2e4e7; margin: 25px 0;"></div>
                            
                            <label style="color:#d63638; display:flex; align-items:center; gap:8px;">
                                <input type="checkbox" name="<?php echo $this->option_name; ?>[use_classic_editor]" value="1" <?php echo $c('use_classic_editor', 1); ?>> 
                                <strong>[오류 방지용] 구버전(클래식) 편집기 강제 사용</strong>
                            </label>
                            <span class="nrap-hint" style="border-left-color:#d63638;">💡 네이버 블로그에서 긁어온 복잡한 HTML 코드가 워드프레스의 최신 블록 편집기(구텐베르크)와 충돌하여 화면이 하얗게 깨지는 현상을 100% 방지해줍니다. <b>반드시 체크해 두시길 권장합니다.</b></span>
                        </div>
                        </form>
                </div>

                <div class="nrap-sidebar">
                    <div class="nrap-card" style="border-color:#03C75A; background:#f0fdf4;">
                        <h2 style="color:#03C75A; border-bottom:1px solid #dcfce7;">💾 설정 저장</h2>
                        <p style="font-size:13px; color:#555; margin-top:0;">좌측에서 변경한 설정값들을 사이트에 적용합니다.</p>
                        <button type="submit" form="nrap-settings-form" class="nrap-btn-primary">설정 저장 및 적용하기</button>
                    </div>

                    <div class="nrap-card" style="border-color:#2271b1; background:#f6f9fc;">
                        <h2 style="color:#2271b1; border-bottom:1px solid #e0e7ff;">⚡ 수동 동기화 제어</h2>
                        <p style="font-size:13px; color:#555; margin-top:0;">자동화 시간을 기다리지 않고 지금 즉시 글을 긁어옵니다.</p>
                        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" onsubmit="return showSyncLoader();">
                            <input type="hidden" name="action" value="naver_rss_manual_sync">
                            <?php wp_nonce_field( 'naver_rss_sync_nonce', 'naver_rss_nonce' ); ?>
                            <button type="submit" class="nrap-btn-action">지금 즉시 동기화 실행</button>
                        </form>

                        <?php if ( $report ) : delete_transient('naver_rss_last_report'); ?>
                            <div class="report-list">
                                <h4 style="margin:0 0 10px 0; color:#03C75A;">✅ 최근 작업 완료!</h4>
                                발견: <b><?php echo $report['found']; ?></b>개 | 중복 스킵: <b><?php echo $report['skipped']; ?></b>개 | 신규 발행: <b style="color:#d63638;"><?php echo $report['imported']; ?></b>개
                                <?php if ( !empty($report['titles']) ) : ?>
                                    <ul style="margin-top:10px; margin-bottom:0; padding-left:15px; color:#444;">
                                        <?php foreach($report['titles'] as $title) echo "<li>".esc_html($title)."</li>"; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function page_init() {
        register_setting( 'naver_rss_option_group', $this->option_name, [ $this, 'sanitize' ] );
        if ( ! wp_next_scheduled( 'naver_rss_cron_hook' ) ) {
            $interval = $this->options['sync_interval'] ?? 'manual';
            if ( $interval !== 'manual' ) wp_schedule_event( time(), $interval, 'naver_rss_cron_hook' );
        }
    }

    public function sanitize($i) { 
        $i['fetch_count'] = absint($i['fetch_count'] ?? 5); 
        $i['auto_create_category'] = isset($i['auto_create_category']) ? 1 : 0;
        $i['use_classic_editor'] = isset($i['use_classic_editor']) ? 1 : 0;
        $i['default_thumb_id'] = absint($i['default_thumb_id'] ?? 0);
        if(isset($i['rss_url'])) $i['rss_url'] = sanitize_url($i['rss_url']);
        if(isset($i['category_mapping'])) $i['category_mapping'] = sanitize_textarea_field($i['category_mapping']);
        return $i; 
    }

    /* --------------------------------------------------------------------------
       [PART 2] 백엔드 코어 (이미지 썸네일 및 파싱) 
       -------------------------------------------------------------------------- */
    public function disable_gutenberg( $status, $type ) {
        if ( ($this->options['use_classic_editor'] ?? 0) && $type === 'post' ) return false;
        return $status;
    }

    public function update_cron_schedule($o, $n, $opt) { 
        wp_clear_scheduled_hook('naver_rss_cron_hook'); 
        if(($n['sync_interval']??'manual')!=='manual') wp_schedule_event(time(), $n['sync_interval'], 'naver_rss_cron_hook'); 
    }

    public function manual_sync_trigger() {
        if ( ! current_user_can('manage_options') ) wp_die('권한이 없습니다.');
        check_admin_referer('naver_rss_sync_nonce', 'naver_rss_nonce');
        set_transient('naver_rss_last_report', $this->run_sync(), 60);
        wp_redirect(admin_url('options-general.php?page=naver-rss-sync')); exit;
    }

    public function run_sync() {
        $this->options = get_option($this->option_name);
        $url = $this->options['rss_url'] ?? '';
        if(!$url) return ['found'=>0, 'skipped'=>0, 'imported'=>0, 'titles'=>[]];
        
        include_once(ABSPATH . WPINC . '/feed.php');
        $feed = fetch_feed($url);
        if(is_wp_error($feed)) return ['found'=>0, 'skipped'=>0, 'imported'=>0, 'titles'=>[]];
        
        $items = $feed->get_items(0, $this->options['fetch_count'] ?? 5);
        $report = ['found'=>count($items), 'skipped'=>0, 'imported'=>0, 'titles'=>[]];
        
        foreach($items as $item) {
            if(get_posts(['meta_key'=>'_naver_guid','meta_value'=>$item->get_id(),'post_type'=>'post','post_status'=>'any','fields'=>'ids'])) { $report['skipped']++; continue; }
            
            $cat_id = $this->get_mapped_category_id($item);
            $raw_desc = $item->get_description();
            $img_url = preg_match('/<img[^>]+src=\'"[\'"]/', $raw_desc, $m) ? $m[1] : '';
            $content = wp_kses_post(preg_replace('/<img[^>]*>/', '', $raw_desc));
            $content .= '<div style="text-align:center;margin-top:30px;"><a href="'.$item->get_permalink().'" target="_blank" style="padding:10px 20px;background:#03C75A;color:#fff;border-radius:5px;text-decoration:none;font-weight:bold;">자세히 보러가기</a></div>';
            
            $pid = wp_insert_post([
                'post_title' => $item->get_title(), 'post_content' => $content, 'post_status' => $this->options['post_status'] ?? 'draft',
                'post_category' => [$cat_id], 'post_date' => $item->get_date('Y-m-d H:i:s'),
                'meta_input' => ['_naver_guid'=>$item->get_id(), '_naver_original_url'=>$item->get_permalink()]
            ]);
            
            if(!is_wp_error($pid)) {
                if($img_url) {
                    $this->sideload_image($img_url, $pid, $item->get_title());
                } else {
                    $default_id = $this->options['default_thumb_id'] ?? 0;
                    if($default_id > 0) set_post_thumbnail($pid, $default_id);
                }
                $report['imported']++; $report['titles'][] = $item->get_title();
            }
        }
        return $report;
    }

    private function get_mapped_category_id($item) {
        $n = $item->get_category() ? $item->get_category()->get_term() : '미분류';
        $mapping = $this->options['category_mapping'] ?? '';
        if($mapping) {
            foreach(explode(',', $mapping) as $row) {
                $p = explode(':', $row);
                if(count($p)==2 && trim($p[0])==$n) return intval(trim($p[1]));
            }
        }
        if($this->options['auto_create_category']??0) {
            $t = term_exists($n, 'category');
            if(!$t) $t = wp_insert_term($n, 'category');
            if(!is_wp_error($t) && is_array($t)) return $t['term_id'];
        }
        return get_option('default_category');
    }

    private function sideload_image($url, $pid, $title) {
        require_once(ABSPATH.'wp-admin/includes/media.php');
        require_once(ABSPATH.'wp-admin/includes/file.php');
        require_once(ABSPATH.'wp-admin/includes/image.php');
        $id = media_sideload_image($url, $pid, $title, 'id');
        if(!is_wp_error($id)) set_post_thumbnail($pid, $id);
    }

    public function mode_b_redirect() {
        if(($this->options['operation_mode']??'A')==='B' && is_single()) {
            $url = get_post_meta(get_the_ID(), '_naver_original_url', true);
            if($url) { wp_redirect($url, 301); exit; }
        }
    }

    /* --------------------------------------------------------------------------
       [PART 3] 메타박스 및 [신규] SEO Canonical 주입 로직
       -------------------------------------------------------------------------- */
    public function add_custom_meta_box() { add_meta_box('naver_rss_meta_box', '🔗 네이버 원본 링크 (리디렉션 관리)', [ $this, 'render_meta_box' ], 'post', 'side'); }
    
    public function render_meta_box($post) { 
        wp_nonce_field('naver_nonce','n_nonce'); 
        $v = get_post_meta($post->ID, '_naver_original_url', true); 
        echo '<p style="font-size:12px; color:#666;">설정이 <b>[모드 B]</b>일 때 작동합니다. 일반 글은 비워두세요.</p><input type="url" name="naver_url" value="'.esc_attr($v).'" style="width:100%; box-sizing:border-box;">'; 
    }
    
    public function save_custom_meta_box($pid) { 
        if(!isset($_POST['n_nonce']) || !wp_verify_nonce($_POST['n_nonce'],'naver_nonce')) return; 
        if(!current_user_can('edit_post', $pid)) return; 
        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        
        if(isset($_POST['naver_url'])) { 
            $url = sanitize_url($_POST['naver_url']); 
            if(empty($url)) delete_post_meta($pid,'_naver_original_url'); 
            else update_post_meta($pid,'_naver_original_url',$url); 
        } 
    }

    /**
     * [신규] SEO Canonical 태그 자동 주입 메서드
     * 방문자가 네이버에서 가져온 글을 워드프레스에서 읽을 때(모드 A),
     * 소스코드의 <head> 영역에 원본 네이버 링크를 canonical로 명시합니다.
     */
    public function inject_canonical_url() {
        if ( is_single() ) {
            $naver_url = get_post_meta( get_the_ID(), '_naver_original_url', true );
            
            if ( ! empty( $naver_url ) ) {
                // 1. 워드프레스 기본 canonical 태그 출력 방지
                remove_action( 'wp_head', 'rel_canonical' );
                
                // 2. Yoast SEO 플러그인 호환 (사용 중일 경우)
                add_filter( 'wpseo_canonical', function( $canonical ) use ( $naver_url ) { return $naver_url; } );
                
                // 3. Rank Math 플러그인 호환 (사용 중일 경우)
                add_filter( 'rank_math/frontend/canonical', function( $canonical ) use ( $naver_url ) { return $naver_url; } );
        
                // 4. 강제 출력 (별도의 SEO 플러그인이 없거나 무력화되었을 때를 대비한 2중 안전장치)
                echo "\n\n";
                echo '<link rel="canonical" href="' . esc_url( $naver_url ) . '" />' . "\n";
            }
        }
    }
}
new Naver_RSS_Auto_Poster_V9_3();
