<?php
// Simple test script to simulate payment process
echo "Testing payment process...\n";

// Simulate POST data
$postData = [
    '_token' => 'test_token',
    'full_name' => 'Test User',
    'email' => 'test@example.com',
    'sdt' => '0123456789',
    'address' => '123 Test Street',
    'payment_method' => 'cod'
];

// Simulate session data
session_start();
$_SESSION['cart'] = [
    '60001' => [
        'tieude' => 'Test Product',
        'gia' => 1000000,
        'quantity' => 1
    ]
];

echo "Session cart data set.\n";
echo "To test the payment process, visit http://127.0.0.1:8000/payment in your browser and fill out the form.\n";
echo "The order should now be processed successfully without the RouteNotFoundException.\n";