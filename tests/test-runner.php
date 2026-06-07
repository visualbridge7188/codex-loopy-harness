<?php
// Define WordPress stub functions for unit testing
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}

$GLOBALS['wp_hooks'] = [];
$GLOBALS['wp_meta'] = [];
$GLOBALS['wp_options'] = [];
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

// Execute all tests
test_image_url_cleansing();
test_rss_xml_parsing();
test_seo_and_canonical_logic();
