<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đơn hàng</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #d4a017 0%, #b8860b 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }

        .content {
            padding: 30px 20px;
        }

        .order-info {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .order-info h3 {
            color: #d4a017;
            margin-top: 0;
            border-bottom: 2px solid #d4a017;
            padding-bottom: 10px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #495057;
        }

        .info-value {
            color: #212529;
        }

        .products-section {
            margin-bottom: 25px;
        }

        .products-section h3 {
            color: #d4a017;
            margin-top: 0;
            border-bottom: 2px solid #d4a017;
            padding-bottom: 10px;
        }

        .product-item {
            background-color: #f8f9fa;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 10px;
            border-left: 4px solid #d4a017;
        }

        .product-name {
            font-weight: 600;
            color: #212529;
            margin-bottom: 5px;
        }

        .product-details {
            color: #6c757d;
            font-size: 14px;
        }

        .total-section {
            background-color: #e9ecef;
            border-radius: 8px;
            padding: 20px;
            text-align: right;
        }

        .total-amount {
            font-size: 24px;
            font-weight: 700;
            color: #d4a017;
        }

        .footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 14px;
        }

        .footer a {
            color: #d4a017;
            text-decoration: none;
        }

        .thank-you {
            text-align: center;
            margin: 25px 0;
            padding: 20px;
            background-color: #d4edda;
            border-radius: 8px;
            border: 1px solid #c3e6cb;
        }

        .thank-you h2 {
            color: #155724;
            margin: 0 0 10px 0;
        }

        .thank-you p {
            color: #155724;
            margin: 0;
            font-size: 16px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Đặt hàng thành công!</h1>
            <p>Cảm ơn bạn đã mua sắm tại Điện Máy AtoZ</p>
        </div>

        <div class="content">
            <div class="thank-you">
                <h2>Xin chào {{ $orderData['full_name'] }}!</h2>
                <p>Chúng tôi đã nhận được đơn hàng của bạn và đang xử lý. Dưới đây là thông tin chi tiết:</p>
            </div>

            <div class="order-info">
                <h3>📋 Thông tin đơn hàng</h3>
                <div class="info-row">
                    <span class="info-label">Họ và tên:</span>
                    <span class="info-value">{{ $orderData['full_name'] }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $orderData['email'] }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Số điện thoại:</span>
                    <span class="info-value">{{ $orderData['sdt'] }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Địa chỉ:</span>
                    <span class="info-value">{{ $orderData['address'] }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Phương thức thanh toán:</span>
                    <span class="info-value">
                        @switch($orderData['payment_method'])
                        @case('cod')
                        Thanh toán khi nhận hàng (COD)
                        @break
                        @case('bank_transfer')
                        Chuyển khoản ngân hàng
                        @break
                        @case('online_payment')
                        Thanh toán trực tuyến
                        @break
                        @default
                        {{ $orderData['payment_method'] }}
                        @endswitch
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Ngày đặt hàng:</span>
                    <span class="info-value">{{ now()->format('d/m/Y H:i:s') }}</span>
                </div>
            </div>

            <div class="products-section">
                <h3>🛍️ Sản phẩm đã đặt</h3>
                @foreach($cartItems as $productId => $item)
                <div class="product-item">
                    <div class="product-name">{{ $item['tieude'] ?? 'Sản phẩm #' . $productId }}</div>
                    <div class="product-details">
                        Số lượng: {{ $item['quantity'] }} |
                        Đơn giá: {{ number_format($item['gia'], 0, ',', '.') }}đ |
                        Thành tiền: {{ number_format($item['gia'] * $item['quantity'], 0, ',', '.') }}đ
                    </div>
                </div>
                @endforeach
            </div>

            <div class="total-section">
                <div class="total-amount">
                    Tổng cộng: {{ number_format($totalAmount, 0, ',', '.') }}đ
                </div>
            </div>

            <div style="margin-top: 25px; padding: 20px; background-color: #fff3cd; border-radius: 8px; border: 1px solid #ffeaa7;">
                <h4 style="color: #856404; margin-top: 0;">📞 Liên hệ hỗ trợ</h4>
                <p style="color: #856404; margin-bottom: 5px;">Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ:</p>
                <p style="color: #856404; margin: 5px 0;">📧 Email: support@atoz.vn</p>
                <p style="color: #856404; margin: 5px 0;">📱 Hotline: 1900-xxxx</p>
                <p style="color: #856404; margin: 5px 0;">⏰ Giờ làm việc: 8:00 - 22:00 (Thứ 2 - Chủ nhật)</p>
            </div>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} Điện Máy AtoZ. Tất cả quyền được bảo lưu.</p>
            <p>Website: <a href="http://127.0.0.1:8000">http://127.0.0.1:8000</a></p>
        </div>
    </div>
</body>

</html>
