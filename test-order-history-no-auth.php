<?php
// Simple test script to simulate accessing order history without authentication
session_start();

echo "Testing order history functionality without authentication...\n";

// Simulate having order history in session (like PaymentController would have stored)
$orderHistory = $_SESSION['order_history'] ?? [];

// Add a sample order if there isn't any
if (empty($orderHistory)) {
    echo "No existing order history found. Adding a sample order...\n";
    $orderHistory[] = [
        'order_id' => 'ORD-' . time(),
        'created_at' => date('Y-m-d H:i:s'),
        'total' => 1000000,
        'status' => 1,
        'payment_data' => [
            'full_name' => 'Test User',
            'email' => 'demo@example.com',
            'sdt' => '0123456789',
            'address' => '123 Test Street',
            'payment_method' => 'cod',
            'total_amount' => 1000000,
            'order_date' => date('Y-m-d H:i:s')
        ],
        'items' => [
            '60001' => [
                'tieude' => 'Test Product',
                'gia' => 1000000,
                'quantity' => 1
            ]
        ]
    ];
    $_SESSION['order_history'] = $orderHistory;
}

// Simulate OrderHistoryController logic
echo "Retrieving order history...\n";
$orders = $_SESSION['order_history'] ?? [];

if (empty($orders)) {
    echo "No orders found in history.\n";
} else {
    echo "Found " . count($orders) . " order(s) in history:\n";
    foreach ($orders as $index => $order) {
        echo "Order #" . ($index + 1) . ":\n";
        echo "  Order ID: " . $order['order_id'] . "\n";
        echo "  Date: " . $order['created_at'] . "\n";
        echo "  Total: " . number_format($order['total'], 0, ',', '.') . "đ\n";
        echo "  Status: " . ($order['status'] == 1 ? 'Đã giao hàng' : 'Đang xử lý') . "\n";
        echo "\n";
    }
}

echo "Test completed. Order history should now be accessible without authentication.\n";