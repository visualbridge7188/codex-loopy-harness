<?php
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

test_image_url_cleansing();
test_rss_xml_parsing();
