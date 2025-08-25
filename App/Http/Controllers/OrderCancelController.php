<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderCancelController extends Controller
{
    public function cancel(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer'
        ]);

        try {
            $deleted = DB::table('GDC_Quotation_BangBaoGia')
                ->where('IDBG', $request->order_id)
                ->where('TinhTrangXacNhan', 0)
                ->delete(); 

            if ($deleted > 0) {
                return back()->with('success', 'Huỷ đơn hàng thành công!');
            }

            return back()->with('error', 'Không thể huỷ đơn hàng. Có thể đơn đã được xác nhận.');
        } catch (\Exception $e) {
            Log::error('Huỷ đơn hàng lỗi: ' . $e->getMessage());
            return back()->with('error', 'Đã xảy ra lỗi khi huỷ đơn hàng.');
        }
    }


    /**
     * Cancel an order stored in session-based order history.
     */
    public function cancelSession(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required'
        ]);

        $targetOrderId = $validated['order_id'];

        $orders = $request->session()->get('order_history', []);

        $foundIndex = null;
        foreach ($orders as $index => $order) {
            if (($order['order_id'] ?? null) === $targetOrderId) {
                $foundIndex = $index;
                // Only allow cancel if status is 0 (pending)
                if (isset($order['status']) && (int)$order['status'] !== 0) {
                    return back()->with('error', 'Không thể huỷ đơn hàng. Đơn đã được xử lý.');
                }
                break;
            }
        }

        if ($foundIndex === null) {
            return back()->with('error', 'Không tìm thấy đơn hàng để huỷ.');
        }

        // Remove the order from session history
        array_splice($orders, $foundIndex, 1);
        $request->session()->put('order_history', array_values($orders));

        // If the current order_data matches, remove it as well
        $orderData = $request->session()->get('order_data');
        if (is_array($orderData) && (($orderData['order_id'] ?? null) === $targetOrderId)) {
            $request->session()->forget('order_data');
        }

        return redirect()->route('cart.history')->with('success', 'Huỷ đơn hàng thành công!');
    }
}
