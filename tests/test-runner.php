<?php
// Define WordPress stub functions for unit testing
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}

$GLOBALS['wp_hooks'] = [];
$GLOBALS['wp_meta'] = [];
$GLOBALS['wp_options'] = [];
$GLOBALS['wp_shortcodes'] = [];
$GLOBALS['mock_posts'] = [];
$GLOBALS['current_post_id'] = 0;
$GLOBALS['is_singular_mock'] = false;
$GLOBALS['wp_transients'] = [];
$GLOBALS['inserted_posts'] = [];
$GLOBALS['wp_terms'] = [];
$GLOBALS['mock_rss_body'] = '';

if ( ! defined( 'MINUTE_IN_SECONDS' ) ) {
    define( 'MINUTE_IN_SECONDS', 60 );
}

if ( ! class_exists( 'WP_Error' ) ) {
    class WP_Error {
        public $code;
        public $message;
        public function __construct( $code = '', $message = '' ) {
            $this->code = $code;
            $this->message = $message;
        }
        public function get_error_message() {
            return $this->message;
        }
    }
}

if ( ! function_exists( 'is_wp_error' ) ) {
    function is_wp_error( $thing ) {
        return ( $thing instanceof WP_Error );
    }
}

if ( ! function_exists( 'sanitize_text_field' ) ) {
    function sanitize_text_field( $str ) {
        return trim( strip_tags( $str ) );
    }
}

if ( ! function_exists( 'esc_url_raw' ) ) {
    function esc_url_raw( $url ) {
        return $url;
    }
}

if ( ! function_exists( 'wp_kses_post' ) ) {
    function wp_kses_post( $content ) {
        return $content;
    }
}

if ( ! function_exists( 'delete_transient' ) ) {
    function delete_transient( $transient ) {
        unset( $GLOBALS['wp_transients'][$transient] );
        return true;
    }
}

if ( ! function_exists( 'set_transient' ) ) {
    function set_transient( $transient, $value, $expiration = 0 ) {
        $GLOBALS['wp_transients'][$transient] = $value;
        return true;
    }
}

if ( ! function_exists( 'get_transient' ) ) {
    function get_transient( $transient ) {
        return isset( $GLOBALS['wp_transients'][$transient] ) ? $GLOBALS['wp_transients'][$transient] : false;
    }
}

if ( ! function_exists( 'wp_insert_post' ) ) {
    function wp_insert_post( $postarr, $wp_error = false ) {
        $GLOBALS['inserted_posts'][] = $postarr;
        $id = count( $GLOBALS['inserted_posts'] );
        return $id;
    }
}

if ( ! function_exists( 'update_post_meta' ) ) {
    function update_post_meta( $post_id, $meta_key, $meta_value, $prev_value = '' ) {
        $GLOBALS['wp_meta'][$post_id][$meta_key] = $meta_value;
        return true;
    }
}

if ( ! function_exists( 'term_exists' ) ) {
    function term_exists( $term, $taxonomy = '', $parent = null ) {
        if ( isset( $GLOBALS['wp_terms'][$taxonomy] ) ) {
            foreach ( $GLOBALS['wp_terms'][$taxonomy] as $id => $name ) {
                if ( $name === $term ) {
                    return $id;
                }
            }
        }
        return null;
    }
}

if ( ! function_exists( 'wp_insert_term' ) ) {
    function wp_insert_term( $term, $taxonomy, $args = [] ) {
        if ( ! isset( $GLOBALS['wp_terms'][$taxonomy] ) ) {
            $GLOBALS['wp_terms'][$taxonomy] = [];
        }
        $exists = term_exists( $term, $taxonomy );
        if ( $exists ) {
            return new WP_Error( 'term_exists', 'Term already exists' );
        }
        $id = count( $GLOBALS['wp_terms'][$taxonomy] ) + 100;
        $GLOBALS['wp_terms'][$taxonomy][$id] = $term;
        return [ 'term_id' => $id, 'term_taxonomy_id' => $id ];
    }
}

if ( ! function_exists( 'get_categories' ) ) {
    function get_categories( $args = [] ) {
        $cats = [];
        $terms = isset( $GLOBALS['wp_terms']['category'] ) ? $GLOBALS['wp_terms']['category'] : [];
        foreach ( $terms as $id => $name ) {
            $cats[] = (object)[
                'term_id' => $id,
                'name'    => $name,
                'slug'    => sanitize_title( $name ),
            ];
        }
        return $cats;
    }
}

if ( ! function_exists( 'sanitize_title' ) ) {
    function sanitize_title( $title ) {
        return strtolower( str_replace( ' ', '-', $title ) );
    }
}

if ( ! function_exists( 'wp_remote_get' ) ) {
    function wp_remote_get( $url, $args = [] ) {
        if ( ! empty( $GLOBALS['mock_rss_body'] ) ) {
            return [
                'body'     => $GLOBALS['mock_rss_body'],
                'response' => [ 'code' => 200 ],
            ];
        }
        if ( strpos( $url, 'mock-rss' ) !== false || strpos( $url, 'naver' ) !== false ) {
            $body = file_get_contents( __DIR__ . '/mock-rss.xml' );
            return [
                'body'     => $body,
                'response' => [ 'code' => 200 ],
            ];
        }
        return new WP_Error( 'http_request_failed', 'Fetch failed' );
    }
}

if ( ! function_exists( 'wp_remote_retrieve_body' ) ) {
    function wp_remote_retrieve_body( $response ) {
        if ( is_wp_error( $response ) ) {
            return '';
        }
        return isset( $response['body'] ) ? $response['body'] : '';
    }
}

if ( ! function_exists( 'get_post_thumbnail_id' ) ) {
    function get_post_thumbnail_id( $post_id ) {
        return 0;
    }
}

if ( ! function_exists( 'download_url' ) ) {
    function download_url( $url, $timeout = 300 ) {
        return new WP_Error( 'download_failed', 'Mock download failure' );
    }
}

if ( ! isset( $GLOBALS['wpdb'] ) ) {
    class Mock_WPDB {
        public $postmeta = 'wp_postmeta';
        public $prepare_calls = [];
        public $get_var_calls = [];

        public function prepare( $query, ...$args ) {
            $this->prepare_calls[] = [ 'query' => $query, 'args' => $args ];
            foreach ( $args as $arg ) {
                $query = preg_replace( '/%[sd]/', "'" . esc_sql( $arg ) . "'", $query, 1 );
            }
            return $query;
        }

        public function get_var( $query ) {
            $this->get_var_calls[] = $query;
            if ( strpos( $query, '_naver_guid' ) !== false ) {
                if ( preg_match( "/meta_value = '([^']+)'/", $query, $matches ) ) {
                    $guid = $matches[1];
                    foreach ( $GLOBALS['wp_meta'] as $post_id => $metas ) {
                        if ( isset( $metas['_naver_guid'] ) && $metas['_naver_guid'] === $guid ) {
                            return $post_id;
                        }
                    }
                }
            }
            if ( strpos( $query, '_naver_source_image_url' ) !== false ) {
                if ( preg_match( "/meta_value = '([^']+)'/", $query, $matches ) ) {
                    $img_url = $matches[1];
                    foreach ( $GLOBALS['wp_meta'] as $post_id => $metas ) {
                        if ( isset( $metas['_naver_source_image_url'] ) && $metas['_naver_source_image_url'] === $img_url ) {
                            return $post_id;
                        }
                    }
                }
            }
            return null;
        }
    }
    $GLOBALS['wpdb'] = new Mock_WPDB();
}

if ( ! function_exists( 'esc_sql' ) ) {
    function esc_sql( $data ) {
        return addslashes( $data );
    }
}

if ( ! function_exists( 'add_action' ) ) {
    function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
        $GLOBALS['wp_hooks']['action'][$hook][] = $callback;
    }
}

if ( ! function_exists( 'add_filter' ) ) {
    function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
        $GLOBALS['wp_hooks']['filter'][$hook][] = $callback;
    }
}

if ( ! function_exists( 'remove_action' ) ) {
    function remove_action( $hook, $callback, $priority = 10 ) {
        $GLOBALS['wp_hooks']['removed_actions'][] = [
            'hook'     => $hook,
            'callback' => $callback,
        ];
        return true;
    }
}

if ( ! function_exists( 'register_deactivation_hook' ) ) {
    function register_deactivation_hook( $file, $function ) {}
}

if ( ! function_exists( 'get_option' ) ) {
    function get_option( $option, $default = false ) {
        return isset( $GLOBALS['wp_options'][$option] ) ? $GLOBALS['wp_options'][$option] : $default;
    }
}

if ( ! function_exists( 'update_option' ) ) {
    function update_option( $option, $value, $autoload = null ) {
        $GLOBALS['wp_options'][$option] = $value;
        return true;
    }
}

if ( ! function_exists( 'current_time' ) ) {
    function current_time( $type, $gmt = 0 ) {
        return '2026-06-08 12:00:00';
    }
}

if ( ! function_exists( 'wp_parse_args' ) ) {
    function wp_parse_args( $args, $defaults = [] ) {
        return array_merge( $defaults, is_array( $args ) ? $args : [] );
    }
}

if ( ! function_exists( 'is_singular' ) ) {
    function is_singular() {
        return $GLOBALS['is_singular_mock'];
    }
}

if ( ! function_exists( 'get_the_ID' ) ) {
    function get_the_ID() {
        return $GLOBALS['current_post_id'];
    }
}

if ( ! function_exists( 'get_post_meta' ) ) {
    function get_post_meta( $post_id, $key, $single = false ) {
        if ( isset( $GLOBALS['wp_meta'][$post_id][$key] ) ) {
            return $GLOBALS['wp_meta'][$post_id][$key];
        }
        return '';
    }
}

if ( ! function_exists( 'esc_url' ) ) {
    function esc_url( $url ) {
        return $url;
    }
}

if ( ! function_exists( 'is_admin' ) ) {
    function is_admin() {
        return false;
    }
}

if ( ! function_exists( 'add_shortcode' ) ) {
    function add_shortcode( $tag, $callback ) {
        $GLOBALS['wp_shortcodes'][$tag] = $callback;
    }
}

if ( ! function_exists( 'shortcode_atts' ) ) {
    function shortcode_atts( $pairs, $atts, $shortcode = '' ) {
        $atts = (array)$atts;
        $out = [];
        foreach ( $pairs as $name => $default ) {
            if ( array_key_exists( $name, $atts ) ) {
                $out[$name] = $atts[$name];
            } else {
                $out[$name] = $default;
            }
        }
        return $out;
    }
}

if ( ! function_exists( 'esc_html' ) ) {
    function esc_html( $text ) {
        return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
    }
}

if ( ! function_exists( 'esc_html_e' ) ) {
    function esc_html_e( $text, $domain = 'default' ) {
        echo esc_html( $text );
    }
}

if ( ! function_exists( 'esc_html__' ) ) {
    function esc_html__( $text, $domain = 'default' ) {
        return esc_html( $text );
    }
}

if ( ! function_exists( '__' ) ) {
    function __( $text, $domain = 'default' ) {
        return $text;
    }
}

if ( ! function_exists( '_e' ) ) {
    function _e( $text, $domain = 'default' ) {
        echo $text;
    }
}

if ( ! function_exists( 'get_permalink' ) ) {
    function get_permalink( $post = 0 ) {
        $id = $post ? ( is_object( $post ) ? $post->ID : $post ) : $GLOBALS['current_post_id'];
        return 'https://example.com/post-' . $id;
    }
}

if ( ! function_exists( 'get_the_title' ) ) {
    function get_the_title( $post = 0 ) {
        if ( isset( $GLOBALS['post'] ) && ( ! $post || $post === $GLOBALS['current_post_id'] ) ) {
            return $GLOBALS['post']->post_title;
        }
        return 'Mock Title';
    }
}

if ( ! function_exists( 'get_the_date' ) ) {
    function get_the_date( $d = '', $post = null ) {
        return '2026.06.08';
    }
}

if ( ! function_exists( 'get_the_excerpt' ) ) {
    function get_the_excerpt( $post = null ) {
        if ( isset( $GLOBALS['post'] ) ) {
            return $GLOBALS['post']->post_excerpt ?? '';
        }
        return 'Mock Excerpt';
    }
}

if ( ! function_exists( 'wp_strip_all_tags' ) ) {
    function wp_strip_all_tags( $string, $remove_breaks = false ) {
        return strip_tags( $string );
    }
}

if ( ! function_exists( 'get_the_content' ) ) {
    function get_the_content( $more_link_text = null, $strip_teaser = false, $post = null ) {
        if ( isset( $GLOBALS['post'] ) ) {
            return $GLOBALS['post']->post_content ?? '';
        }
        return 'Mock Content';
    }
}

if ( ! function_exists( 'wp_html_excerpt' ) ) {
    function wp_html_excerpt( $str, $count, $more = null ) {
        if ( null === $more ) {
            $more = '&hellip;';
        }
        $str = wp_strip_all_tags( $str );
        if ( mb_strlen( $str ) > $count ) {
            return mb_substr( $str, 0, $count ) . $more;
        }
        return $str;
    }
}

if ( ! function_exists( 'has_post_thumbnail' ) ) {
    function has_post_thumbnail( $post = null ) {
        if ( isset( $GLOBALS['post'] ) ) {
            return ! empty( $GLOBALS['post']->has_thumb );
        }
        return false;
    }
}

if ( ! function_exists( 'the_post_thumbnail' ) ) {
    function the_post_thumbnail( $size = 'post-thumbnail', $attr = '' ) {
        echo '<img src="https://example.com/mock-thumb.jpg" class="wp-post-image" />';
    }
}

if ( ! function_exists( 'wp_reset_postdata' ) ) {
    function wp_reset_postdata() {
        unset( $GLOBALS['post'] );
        $GLOBALS['current_post_id'] = 0;
    }
}

if ( ! class_exists( 'WP_Query' ) ) {
    class WP_Query {
        public $posts = [];
        public $current_post = -1;
        public $post_count = 0;
        public $post;

        public function __construct( $args = [] ) {
            $this->posts = isset( $GLOBALS['mock_posts'] ) ? $GLOBALS['mock_posts'] : [];
            $this->post_count = count( $this->posts );
        }

        public function have_posts() {
            if ( $this->current_post + 1 < $this->post_count ) {
                return true;
            }
            return false;
        }

        public function the_post() {
            $this->current_post++;
            $this->post = $this->posts[$this->current_post];
            $GLOBALS['post'] = $this->post;
            $GLOBALS['current_post_id'] = $this->post->ID;
        }
    }
}

// ----------------------------------------------------
// Existing Tests
// ----------------------------------------------------

function test_image_url_cleansing() {
    $url = "https://postfiles.pstatic.net/test_img1.jpg?type=w80_r80";
    $clean_url = strtok($url, '?');
    $filename = basename($clean_url);
    
    assert($clean_url === "https://postfiles.pstatic.net/test_img1.jpg");
    assert($filename === "test_img1.jpg");
    echo "PASS: Image URL Cleansing Test\n";
}

function test_rss_xml_parsing() {
    $xml_file = __DIR__ . '/mock-rss.xml';
    $body = file_get_contents($xml_file);
    
    $xml = simplexml_load_string($body, 'SimpleXMLElement', LIBXML_NOCDATA);
    assert($xml !== false);
    
    $items = isset($xml->channel->item) ? $xml->channel->item : [];
    assert(count($items) === 1);
    
    $item = $items[0];
    $guid = (string)$item->guid;
    $link = (string)$item->link;
    $title = (string)$item->title;
    $content = (string)$item->description;
    
    assert($guid === 'https://blog.naver.com/test/12345');
    assert($link === 'https://blog.naver.com/test/12345');
    assert($title === '샘플 글 제목 1');
    
    // Extract first image URL
    $first_image = '';
    if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $content, $matches)) {
        $first_image = $matches[1];
    }
    assert($first_image === 'https://postfiles.pstatic.net/test_img1.jpg?type=w80_r80');
    
    // Cleansing
    $clean_image = strtok($first_image, '?');
    assert($clean_image === 'https://postfiles.pstatic.net/test_img1.jpg');
    
    echo "PASS: RSS XML Parsing and Image Extraction Test\n";
}

// ----------------------------------------------------
// New Tests for Task 4
// ----------------------------------------------------

function test_seo_and_canonical_logic() {
    // Load the plugin file
    require_once __DIR__ . '/../docs/snippets/naver_rss_sync_ultimate.php';
    
    $instance = Naver_RSS_Sync_Ultimate::get_instance();
    
    // Test override_canonical
    // Case A: Not singular
    $GLOBALS['is_singular_mock'] = false;
    $GLOBALS['current_post_id'] = 456;
    $GLOBALS['wp_meta'][456] = [
        '_naver_link' => 'https://blog.naver.com/test/456'
    ];
    $canonical = $instance->override_canonical( 'https://mywordpress.site/post-456' );
    assert( $canonical === 'https://mywordpress.site/post-456', "Should not override canonical if not singular" );
    
    // Case B: Singular, has _naver_link
    $GLOBALS['is_singular_mock'] = true;
    $canonical = $instance->override_canonical( 'https://mywordpress.site/post-456' );
    assert( $canonical === 'https://blog.naver.com/test/456', "Should override canonical to _naver_link" );
    
    // Case C: Singular, fallback to _naver_original_url
    $GLOBALS['current_post_id'] = 789;
    $GLOBALS['wp_meta'][789] = [
        '_naver_original_url' => 'https://blog.naver.com/test/789'
    ];
    $canonical = $instance->override_canonical( 'https://mywordpress.site/post-789' );
    assert( $canonical === 'https://blog.naver.com/test/789', "Should fallback to _naver_original_url" );

    // Case D: Singular, no naver link
    $GLOBALS['current_post_id'] = 111;
    $GLOBALS['wp_meta'][111] = [];
    $canonical = $instance->override_canonical( 'https://mywordpress.site/post-111' );
    assert( $canonical === 'https://mywordpress.site/post-111', "Should not override if no naver url" );

    // Test inject_canonical_and_noindex output
    // Case A: Has naver link
    $GLOBALS['current_post_id'] = 456;
    $GLOBALS['is_singular_mock'] = true;
    $GLOBALS['wp_hooks']['removed_actions'] = []; // reset
    
    ob_start();
    $instance->inject_canonical_and_noindex();
    $output = ob_get_clean();
    
    assert( strpos( $output, 'rel="canonical"' ) !== false, "Should inject canonical tag" );
    assert( strpos( $output, 'href="https://blog.naver.com/test/456"' ) !== false, "Canonical href should match naver link" );
    assert( strpos( $output, 'name="robots"' ) !== false, "Should inject robots meta" );
    assert( strpos( $output, 'content="noindex, nofollow"' ) !== false, "Robots content should be noindex, nofollow" );
    
    // Check rel_canonical action removal
    $removed = false;
    foreach ( $GLOBALS['wp_hooks']['removed_actions'] as $action ) {
        if ( $action['hook'] === 'wp_head' && $action['callback'] === 'rel_canonical' ) {
            $removed = true;
            break;
        }
    }
    assert( $removed === true, "Should remove rel_canonical from wp_head" );

    // Test exclude_from_sitemaps
    $sitemaps = [
        'post' => (object)[],
        'page' => (object)[],
        'naver_blog' => (object)[],
    ];
    $filtered = $instance->exclude_from_sitemaps( $sitemaps );
    assert( ! isset( $filtered['naver_blog'] ), "Should exclude naver_blog from sitemaps" );
    assert( isset( $filtered['post'] ), "Should not exclude post from sitemaps" );
    assert( isset( $filtered['page'] ), "Should not exclude page from sitemaps" );

    echo "PASS: SEO Canonical and Sitemap Exclusion Logic Test\n";
}

function test_shortcode_rendering_engine() {
    $instance = Naver_RSS_Sync_Ultimate::get_instance();
    
    // 1. Verify shortcode is registered
    assert( isset( $GLOBALS['wp_shortcodes']['naver_rss_archive'] ), "Shortcode should be registered" );
    
    // Create mock posts
    $post1 = (object)[
        'ID'           => 101,
        'post_title'   => 'Toss Style Post Title',
        'post_excerpt' => 'This is the excerpt for post 101',
        'post_content' => 'This is the content for post 101',
        'has_thumb'    => true,
    ];
    
    $post2 = (object)[
        'ID'           => 102,
        'post_title'   => 'Magazine Style Post Title',
        'post_excerpt' => 'This is the excerpt for post 102',
        'post_content' => 'This is the content for post 102',
        'has_thumb'    => false,
    ];
    
    $GLOBALS['mock_posts'] = [ $post1, $post2 ];
    
    // Test Toss layout rendering (explicit style parameter)
    $toss_html = $instance->render_archive_shortcode( [ 'style' => 'toss' ] );
    assert( strpos( $toss_html, 'css-toss-wrap' ) !== false, "Output should contain toss-wrap" );
    assert( strpos( $toss_html, 'Toss Style Post Title' ) !== false, "Output should contain Toss Style Post Title" );
    assert( strpos( $toss_html, 'wp-post-image' ) !== false, "Post 101 should render post thumbnail" );
    
    // Test Magazine layout rendering (explicit style parameter)
    $mag_html = $instance->render_archive_shortcode( [ 'style' => 'magazine' ] );
    assert( strpos( $mag_html, 'css-magazine-wrap' ) !== false, "Output should contain magazine-wrap" );
    assert( strpos( $mag_html, 'Magazine Style Post Title' ) !== false, "Output should contain Magazine Style Post Title" );
    assert( strpos( $mag_html, 'nrsu-magazine-svg-placeholder' ) !== false, "Post 102 should render SVG placeholder" );

    // Test default fallback (configured via options)
    // Toss is the default
    $default_html = $instance->render_archive_shortcode( [] );
    assert( strpos( $default_html, 'css-toss-wrap' ) !== false, "Default style should be Toss" );

    // No posts case
    $GLOBALS['mock_posts'] = [];
    $no_posts_html = $instance->render_archive_shortcode( [] );
    assert( strpos( $no_posts_html, 'nrsu-no-posts' ) !== false, "Should render no posts message when empty" );

    echo "PASS: Shortcode Registration and Layout Rendering Engine Test\n";
}

function test_category_mapping_and_auto_create() {
    $instance = Naver_RSS_Sync_Ultimate::get_instance();

    // Helper to generate RSS XML with a specific category and guid
    $get_rss_xml = function( $category, $guid ) {
        return '<?xml version="1.0" encoding="utf-8" ?>
        <rss version="2.0">
          <channel>
            <title>Test Blog</title>
            <link>https://blog.naver.com/test</link>
            <item>
              <title>Test Post</title>
              <link>' . $guid . '</link>
              <description><![CDATA[Some content]]></description>
              <category>' . $category . '</category>
              <pubDate>Sun, 07 Jun 2026 12:00:00 +0900</pubDate>
              <guid>' . $guid . '</guid>
            </item>
          </channel>
        </rss>';
    };

    // Initialize/Reset Globals
    $GLOBALS['wp_transients'] = [];
    $GLOBALS['inserted_posts'] = [];
    $GLOBALS['wp_terms'] = [];
    $GLOBALS['wp_meta'] = [];
    $GLOBALS['wp_options'] = [];

    // Pre-populate some options
    $GLOBALS['wp_options']['naver_rss_sync_ultimate_settings'] = [
        'rss_url' => 'https://blog.naver.com/test/rss',
        'post_type_selection' => 'naver_blog',
        'post_status' => 'publish',
        'category_mapping' => [
            'IT/Tech' => 42, // Map "IT/Tech" to WP Category ID 42
        ],
        'auto_create_category' => 1,
    ];
    $GLOBALS['wp_options']['default_category'] = 1;

    // Reload options in instance using Reflection
    $ref = new ReflectionClass( 'Naver_RSS_Sync_Ultimate' );
    $prop = $ref->getProperty( 'options' );
    $prop->setValue( $instance, get_option( 'naver_rss_sync_ultimate_settings', [] ) );

    // ----------------------------------------------------
    // Case 1: Mapped Category ("IT/Tech" -> 42)
    // ----------------------------------------------------
    $GLOBALS['mock_rss_body'] = $get_rss_xml( 'IT/Tech', 'https://blog.naver.com/test/mapped_post' );
    $GLOBALS['inserted_posts'] = [];
    $instance->run_sync();
    
    assert( count( $GLOBALS['inserted_posts'] ) === 1, "Should insert 1 post" );
    $inserted = $GLOBALS['inserted_posts'][0];
    assert( isset( $inserted['post_category'] ), "Post should have post_category" );
    assert( $inserted['post_category'][0] === 42, "Post category should be 42 (mapped)" );

    // ----------------------------------------------------
    // Case 2: Auto-create category enabled, category does not exist ("음식")
    // ----------------------------------------------------
    $GLOBALS['mock_rss_body'] = $get_rss_xml( '음식', 'https://blog.naver.com/test/autocreate_post' );
    $GLOBALS['inserted_posts'] = [];
    $GLOBALS['wp_terms'] = []; // reset terms
    
    $instance->run_sync();
    
    // Check if term "음식" was inserted
    assert( isset( $GLOBALS['wp_terms']['category'] ), "Should have inserted a category" );
    $created_cats = $GLOBALS['wp_terms']['category'];
    $food_cat_id = array_search( '음식', $created_cats );
    assert( $food_cat_id !== false, "Category '음식' should have been created" );
    
    assert( count( $GLOBALS['inserted_posts'] ) === 1, "Should insert 1 post" );
    $inserted = $GLOBALS['inserted_posts'][0];
    assert( $inserted['post_category'][0] === $food_cat_id, "Post category should be the newly created category ID" );

    // ----------------------------------------------------
    // Case 3: Auto-create category enabled, category already exists ("음식")
    // ----------------------------------------------------
    // Category "음식" already exists with the same ID from Case 2
    $GLOBALS['mock_rss_body'] = $get_rss_xml( '음식', 'https://blog.naver.com/test/exists_post' );
    $GLOBALS['inserted_posts'] = [];
    $terms_before = count( $GLOBALS['wp_terms']['category'] );
    
    $instance->run_sync();
    
    $terms_after = count( $GLOBALS['wp_terms']['category'] );
    assert( $terms_before === $terms_after, "Should not create a new category if it already exists" );
    
    assert( count( $GLOBALS['inserted_posts'] ) === 1, "Should insert 1 post" );
    $inserted = $GLOBALS['inserted_posts'][0];
    assert( $inserted['post_category'][0] === $food_cat_id, "Post category should be the existing category ID" );

    // ----------------------------------------------------
    // Case 4: Auto-create category disabled, category does not exist ("스포츠")
    // ----------------------------------------------------
    $GLOBALS['wp_options']['naver_rss_sync_ultimate_settings']['auto_create_category'] = 0;
    $ref = new ReflectionClass( 'Naver_RSS_Sync_Ultimate' );
    $prop = $ref->getProperty( 'options' );
    $prop->setValue( $instance, get_option( 'naver_rss_sync_ultimate_settings', [] ) );
    
    $GLOBALS['mock_rss_body'] = $get_rss_xml( '스포츠', 'https://blog.naver.com/test/disabled_post' );
    $GLOBALS['inserted_posts'] = [];
    
    $instance->run_sync();
    
    // Check "스포츠" was NOT created
    $created_cats = isset( $GLOBALS['wp_terms']['category'] ) ? $GLOBALS['wp_terms']['category'] : [];
    assert( ! in_array( '스포츠', $created_cats, true ), "Category '스포츠' should NOT be created when auto-create is disabled" );
    
    assert( count( $GLOBALS['inserted_posts'] ) === 1, "Should insert 1 post" );
    $inserted = $GLOBALS['inserted_posts'][0];
    assert( $inserted['post_category'][0] === 1, "Post category should fallback to default category (1)" );

    echo "PASS: Category Mapping and Auto Create Test\n";
}

// Execute all tests
test_image_url_cleansing();
test_rss_xml_parsing();
test_seo_and_canonical_logic();
test_shortcode_rendering_engine();
test_category_mapping_and_auto_create();
