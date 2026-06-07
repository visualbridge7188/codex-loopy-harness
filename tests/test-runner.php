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

// Execute all tests
test_image_url_cleansing();
test_rss_xml_parsing();
test_seo_and_canonical_logic();
test_shortcode_rendering_engine();
