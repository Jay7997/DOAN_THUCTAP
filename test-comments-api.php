<?php
// Test script to check what the comment API returns
echo "Testing comment API...\n";

// Test product ID (you can change this to test with different products)
$productId = 60001;

// Test getting comments
echo "Testing GET request to get comments...\n";
$apiUrl = "https://demodienmay.125.atoz.vn/ww2/binhluan.pc.asp?id={$productId}&txtloai=desc&pageid=1";
echo "API URL: $apiUrl\n";

// Use cURL to make the request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for testing
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

if ($error) {
    echo "cURL Error: $error\n";
} else {
    echo "HTTP Status Code: $httpCode\n";
    echo "Response:\n";
    echo $response . "\n";
    
    // Try to parse as JSON
    $json = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "Parsed JSON:\n";
        echo json_encode($json, JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "Response is not valid JSON. Error: " . json_last_error_msg() . "\n";
    }
}

echo "\nTesting POST request to add comment...\n";
// Note: We won't actually send a POST request in this test to avoid creating test comments
echo "POST request would be sent to: https://demodienmay.125.atoz.vn/ww2/binhluan.pc.asp?id={$productId}&action=add\n";