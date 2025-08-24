<?php
// Simple test script to simulate placing an order and checking order history
session_start();

echo "Testing order history functionality...\n";

// Simulate placing an order
echo "Simulating placing an order...\n";

// Simulate cart data
$_SESSION['cart'] = [
    '60001' => [
        'tieude' => 'Test Product',
        'gia' => 1000000,
        'quantity' => 1
    ]
];

// Simulate order data
$orderData = [
    'full_name' => 'Test User',
    'email' => 'test@example.com',
    'sdt' => '0123456789',
    'address' => '123 Test Street',
    'payment_method' => 'cod',
    'total_amount' => 1000000,
    'order_date' => date('Y-m-d H:i:s')
];

// Simulate storing order in session (like PaymentController does)
$orderHistory = $_SESSION['order_history'] ?? [];
$orderHistory[] = [
    'order_id' => 'ORD-' . time(),
    'created_at' => date('Y-m-d H:i:s'),
    'total' => 1000000,
    'status' => 1,
    'payment_data' => $orderData,
    'items' => $_SESSION['cart']
];
$_SESSION['order_history'] = $orderHistory;

// Clear cart (like PaymentController does)
unset($_SESSION['cart']);

echo "Order placed successfully!\n";

// Simulate checking order history (like OrderHistoryController does)
echo "Checking order history...\n";
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

echo "Test completed.\n";