<?php
// Test script to check if the comment system is working correctly
echo "Testing comment system...\n";

// Test product ID
$productId = 60001;

// Test getting comments
echo "Testing GET request to get comments...\n";
$apiUrl = "http://127.0.0.1:8000/api/proxy-binhluan/{$productId}/1";

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
        
        // Check if the response format is correct
        if (is_array($json) && count($json) > 0 && isset($json[0]['data'])) {
            echo "Response format is correct. Comments data:\n";
            echo json_encode($json[0]['data'], JSON_PRETTY_PRINT) . "\n";
        } else {
            echo "Response format is not as expected.\n";
        }
    } else {
        echo "Response is not valid JSON. Error: " . json_last_error_msg() . "\n";
    }
}

echo "\nTesting POST request to add comment...\n";
// Note: We won't actually send a POST request in this test to avoid creating test comments
echo "POST request would be sent to: http://127.0.0.1:8000/api/proxy-binhluan/{$productId}/add\n";

// Test the external API directly
echo "\nTesting external API directly...\n";
$externalApiUrl = "https://demodienmay.125.atoz.vn/ww2/binhluan.pc.asp?id={$productId}&txtloai=desc&pageid=1";
echo "External API URL: $externalApiUrl\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $externalApiUrl);
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

echo "\nTest completed.\n";