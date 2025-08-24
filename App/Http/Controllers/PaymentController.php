<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmation;
use Illuminate\Support\Facades\Session;

class PaymentController extends Controller
{
    public function showPaymentForm()
    {
        // Kiểm tra giỏ hàng
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.view')->with('error', 'Giỏ hàng của bạn đang trống. Vui lòng thêm sản phẩm trước khi thanh toán.');
        }

        // Tính tổng tiền
        $totalAmount = 0;
        foreach ($cart as $item) {
            $totalAmount += ($item['gia'] ?? 0) * ($item['quantity'] ?? 1);
        }

        return view('payment.form', compact('cart', 'totalAmount'));
    }

    public function processPayment(Request $request)
    {
        // 1. Kiểm tra form đầu vào
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'sdt' => 'required|regex:/^[0-9]{10}$/',
            'address' => 'required|string|max:500',
            'payment_method' => 'required|in:cod,bank_transfer,online_payment',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // 2. Lấy giỏ hàng từ session
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->back()->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        // 3. Tính tổng tiền
        $totalAmount = 0;
        foreach ($cart as $item) {
            $totalAmount += ($item['gia'] ?? 0) * ($item['quantity'] ?? 1);
        }

        // 4. Chuẩn bị dữ liệu đơn hàng
        $orderData = [
            'full_name' => $request->input('full_name'),
            'email' => $request->input('email'),
            'sdt' => $request->input('sdt'),
            'address' => $request->input('address'),
            'payment_method' => $request->input('payment_method'),
            'total_amount' => $totalAmount,
            'order_date' => now(),
        ];

        // 5. Xử lý đơn hàng (giả lập thành công)
        $successCount = 0;
        $failedProducts = [];

        foreach ($cart as $productId => $item) {
            // Giả lập xử lý đơn hàng thành công cho tất cả sản phẩm
            $successCount++;
            Log::info('Order successful for product (simulated)', [
                'product_id' => $productId,
                'product_name' => $item['tieude'] ?? 'Sản phẩm #' . $productId
            ]);
        }

        // 6. Xử lý kết quả đặt hàng
        if ($successCount > 0) {
            try {
                // Gửi email xác nhận
                Mail::to($request->input('email'))->send(new OrderConfirmation($orderData, $cart, $totalAmount));

                Log::info('Order confirmation email sent successfully', [
                    'email' => $request->input('email'),
                    'order_data' => $orderData
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send order confirmation email', [
                    'email' => $request->input('email'),
                    'error' => $e->getMessage()
                ]);
                // Không dừng quá trình nếu gửi email thất bại
            }

            // Xóa giỏ hàng khi đặt thành công
            $request->session()->forget('cart');

            // Lưu thông tin đơn hàng vào session để hiển thị ở trang thành công
            $request->session()->put('order_data', $orderData);

            // Thêm đơn hàng vào lịch sử đơn hàng trong session
            $orderHistory = $request->session()->get('order_history', []);
            $orderHistory[] = [
                'order_id' => 'ORD-' . time(), // Generate a simple order ID
                'created_at' => now(),
                'total' => $totalAmount,
                'status' => 1, // 1 = Đã giao hàng (simulated)
                'payment_data' => $orderData,
                'items' => $cart
            ];
            $request->session()->put('order_history', $orderHistory);

            // Redirect đến trang thành công
            return redirect()->route('payment.success');
        } else {
            // Tất cả sản phẩm đều thất bại
            $errorMessage = 'Không thể đặt hàng với bất kỳ sản phẩm nào. Vui lòng thử lại sau hoặc liên hệ hỗ trợ.';

            if (!empty($failedProducts)) {
                $errorMessage .= ' Chi tiết lỗi: ' . implode(', ', array_map(function ($item) {
                    return $item['name'] . ': ' . $item['error'];
                }, $failedProducts));
            }

            return redirect()->back()->with('error', $errorMessage)->withInput();
        }
    }

    public function showSuccess()
    {
        // Kiểm tra xem có thông tin đơn hàng trong session không
        if (!session('order_data')) {
            return redirect()->route('products.index')->with('error', 'Không tìm thấy thông tin đơn hàng.');
        }

        return view('payment.success');
    }
}
