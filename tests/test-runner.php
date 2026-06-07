<?php
function test_image_url_cleansing() {
    $url = "https://postfiles.pstatic.net/test_img1.jpg?type=w80_r80";
    $clean_url = strtok($url, '?');
    $filename = basename($clean_url);
    
    assert($clean_url === "https://postfiles.pstatic.net/test_img1.jpg");
    assert($filename === "test_img1.jpg");
    echo "PASS: Image URL Cleansing Test\n";
}

test_image_url_cleansing();
