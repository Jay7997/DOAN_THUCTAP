<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OrderHistoryController extends Controller
{
    public function index(Request $request)
    {
        // For demo purposes, we'll use a default email if user is not logged in
        $email = session('user_email', 'demo@example.com');

        // Lấy lịch sử đơn hàng từ session
        $orders = $request->session()->get('order_history', []);

        $formatted = collect($orders)->map(function ($order) {
            return [
                'order_id'     => $order['order_id'] ?? null,
                'created_at'   => $order['created_at'] ?? null,
                'total'        => $order['total'] ?? 0,
                'status'       => $order['status'] ?? 0,
                'payment_data' => $order['payment_data'] ?? [],
                'items'        => $order['items'] ?? []
            ];
        });

        return view('cart.history', [
            'purchase_history' => $formatted
        ]);
    }
}
