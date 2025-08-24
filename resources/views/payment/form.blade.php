@extends('layouts.app')

@section('title', 'Thanh Toán Đơn Hàng')

@section('styles')
<style>
    .cart-summary {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 30px;
        border-left: 4px solid #d4a017;
    }

    .cart-summary h4 {
        color: #d4a017;
        margin-bottom: 15px;
        border-bottom: 2px solid #d4a017;
        padding-bottom: 10px;
    }

    .cart-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #dee2e6;
    }

    .cart-item:last-child {
        border-bottom: none;
    }

    .product-info {
        flex: 1;
    }

    .product-name {
        font-weight: 600;
        color: #212529;
    }

    .product-details {
        color: #6c757d;
        font-size: 14px;
    }

    .product-price {
        text-align: right;
        font-weight: 600;
        color: #d4a017;
    }

    .total-section {
        background-color: #e9ecef;
        border-radius: 6px;
        padding: 15px;
        margin-top: 15px;
        text-align: right;
    }

    .total-amount {
        font-size: 20px;
        font-weight: 700;
        color: #d4a017;
    }

    .payment-methods {
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .payment-method-item {
        padding: 15px;
        border: 2px solid #e9ecef;
        border-radius: 6px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .payment-method-item:hover {
        border-color: #d4a017;
        background-color: #fff8e1;
    }

    .payment-method-item.selected {
        border-color: #d4a017;
        background-color: #fff8e1;
    }

    .payment-method-item input[type="radio"] {
        margin-right: 10px;
    }

    .payment-method-item label {
        cursor: pointer;
        margin: 0;
        font-weight: 500;
    }

    .payment-method-description {
        color: #6c757d;
        font-size: 14px;
        margin-top: 5px;
        margin-left: 25px;
    }

    .form-section {
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 25px;
        margin-bottom: 20px;
    }

    .form-section h5 {
        color: #d4a017;
        margin-bottom: 20px;
        border-bottom: 2px solid #d4a017;
        padding-bottom: 10px;
    }

    .btn-submit {
        background: linear-gradient(135deg, #d4a017 0%, #b8860b 100%);
        border: none;
        padding: 12px 30px;
        font-size: 16px;
        font-weight: 600;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(212, 160, 23, 0.3);
    }

    .btn-secondary {
        background-color: #6c757d;
        border: none;
        padding: 12px 30px;
        font-size: 16px;
        font-weight: 600;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
        transform: translateY(-2px);
    }
</style>
@endsection

@section('content')
<div class="container mt-5">
    <h2 class="text-center mb-4 text-gold">🛒 Thanh Toán Đơn Hàng</h2>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Tóm tắt giỏ hàng -->
    <div class="cart-summary">
        <h4>📋 Tóm tắt giỏ hàng</h4>
        @foreach($cart as $productId => $item)
        <div class="cart-item">
            <div class="product-info">
                <div class="product-name">{{ $item['tieude'] ?? 'Sản phẩm #' . $productId }}</div>
                <div class="product-details">
                    Số lượng: {{ $item['quantity'] }} |
                    Đơn giá: {{ number_format($item['gia'], 0, ',', '.') }}đ
                </div>
            </div>
            <div class="product-price">
                {{ number_format($item['gia'] * $item['quantity'], 0, ',', '.') }}đ
            </div>
        </div>
        @endforeach

        <div class="total-section">
            <div class="total-amount">
                Tổng cộng: {{ number_format($totalAmount, 0, ',', '.') }}đ
            </div>
        </div>
    </div>

    <form action="{{ route('payment.process') }}" method="POST" class="needs-validation" novalidate>
        @csrf

        <!-- Thông tin khách hàng -->
        <div class="form-section">
            <h5>👤 Thông tin khách hàng</h5>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="full_name" class="form-label">Họ và Tên *</label>
                    <input type="text" class="form-control @error('full_name') is-invalid @enderror"
                        id="full_name" name="full_name" value="{{ old('full_name') }}" required>
                    @error('full_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @else
                    <div class="invalid-feedback">Vui lòng nhập họ và tên.</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                        id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @else
                    <div class="invalid-feedback">Vui lòng nhập email hợp lệ.</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="sdt" class="form-label">Số điện thoại *</label>
                    <input type="tel" class="form-control @error('sdt') is-invalid @enderror"
                        id="sdt" name="sdt" value="{{ old('sdt') }}" required
                        pattern="[0-9]{10}" maxlength="10">
                    @error('sdt')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @else
                    <div class="invalid-feedback">Số điện thoại phải gồm đúng 10 chữ số.</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="address" class="form-label">Địa chỉ giao hàng *</label>
                    <textarea class="form-control @error('address') is-invalid @enderror"
                        id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                    @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @else
                    <div class="invalid-feedback">Vui lòng nhập địa chỉ giao hàng.</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Phương thức thanh toán -->
        <div class="form-section">
            <h5>💳 Phương thức thanh toán</h5>

            <div class="payment-methods">
                <div class="payment-method-item" onclick="selectPaymentMethod('cod')">
                    <input type="radio" name="payment_method" id="cod" value="cod"
                        {{ old('payment_method') == 'cod' ? 'checked' : '' }} required>
                    <label for="cod">💵 Thanh toán khi nhận hàng (COD)</label>
                    <div class="payment-method-description">
                        Bạn sẽ thanh toán tiền mặt khi nhận hàng từ nhân viên giao hàng
                    </div>
                </div>

                <div class="payment-method-item" onclick="selectPaymentMethod('bank_transfer')">
                    <input type="radio" name="payment_method" id="bank_transfer" value="bank_transfer"
                        {{ old('payment_method') == 'bank_transfer' ? 'checked' : '' }} required>
                    <label for="bank_transfer">🏦 Chuyển khoản ngân hàng</label>
                    <div class="payment-method-description">
                        Chuyển khoản trước khi giao hàng. Thông tin tài khoản sẽ được gửi qua email
                    </div>
                </div>

                <div class="payment-method-item" onclick="selectPaymentMethod('online_payment')">
                    <input type="radio" name="payment_method" id="online_payment" value="online_payment"
                        {{ old('payment_method') == 'online_payment' ? 'checked' : '' }} required>
                    <label for="online_payment">🌐 Thanh toán trực tuyến</label>
                    <div class="payment-method-description">
                        Thanh toán qua thẻ tín dụng/ghi nợ hoặc ví điện tử
                    </div>
                </div>
            </div>

            @error('payment_method')
            <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
        </div>

        <!-- Nút đặt hàng -->
        <div class="text-center">
            <button type="submit" class="btn btn-submit text-white me-3 btn-primary">
                🚀 Đặt hàng ngay
            </button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">
                🛍️ Tiếp tục mua sắm
            </a>
        </div>

        {{-- <!-- Debug link (chỉ hiển thị trong development) -->
        @if(config('app.debug'))
        <div class="text-center mt-3">
            <a href="{{ route('payment.debug') }}" class="btn btn-outline-warning btn-sm">
                🐛 Debug Đặt Hàng
            </a>
        </div>
        @endif --}}
    </form>
</div>

<script>
    // Xử lý chọn phương thức thanh toán
    function selectPaymentMethod(method) {
        // Bỏ chọn tất cả
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.checked = false;
        });

        // Chọn phương thức được click
        document.getElementById(method).checked = true;

        // Cập nhật UI
        document.querySelectorAll('.payment-method-item').forEach(item => {
            item.classList.remove('selected');
        });

        event.currentTarget.classList.add('selected');
    }

    // Validation form
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();

    // Tự động chọn phương thức thanh toán đầu tiên nếu chưa có
    document.addEventListener('DOMContentLoaded', function() {
        if (!document.querySelector('input[name="payment_method"]:checked')) {
            const firstMethod = document.querySelector('.payment-method-item');
            if (firstMethod) {
                firstMethod.classList.add('selected');
                firstMethod.querySelector('input[type="radio"]').checked = true;
            }
        }
    });
</script>
@endsection