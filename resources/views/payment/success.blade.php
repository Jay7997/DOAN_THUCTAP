@extends('layouts.app')

@section('title', 'Đặt hàng thành công')

@section('content')
<div class="container mt-5">
    <div class="text-center">
        <div style="font-size: 80px; color: #28a745; margin-bottom: 30px;">🎉</div>
        <h1 style="color: #28a745; margin-bottom: 20px;">Đặt hàng thành công!</h1>
        <p style="color: #6c757d; font-size: 18px; margin-bottom: 40px;">
            Cảm ơn bạn đã mua sắm tại Điện Máy AtoZ. Chúng tôi đã nhận được đơn hàng của bạn và đang xử lý.
        </p>

        @if(session('order_data'))
        <div class="card mb-4">
            <div class="card-header">
                <h4>📋 Thông tin đơn hàng</h4>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6"><strong>Họ và tên:</strong></div>
                    <div class="col-6">{{ session('order_data.full_name') }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><strong>Email:</strong></div>
                    <div class="col-6">{{ session('order_data.email') }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><strong>Số điện thoại:</strong></div>
                    <div class="col-6">{{ session('order_data.sdt') }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><strong>Địa chỉ:</strong></div>
                    <div class="col-6">{{ session('order_data.address') }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><strong>Phương thức thanh toán:</strong></div>
                    <div class="col-6">
                        @switch(session('order_data.payment_method'))
                        @case('cod')
                        💵 Thanh toán khi nhận hàng (COD)
                        @break
                        @case('bank_transfer')
                        🏦 Chuyển khoản ngân hàng
                        @break
                        @case('online_payment')
                        🌐 Thanh toán trực tuyến
                        @break
                        @default
                        {{ session('order_data.payment_method') }}
                        @endswitch
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><strong>Tổng tiền:</strong></div>
                    <div class="col-6 text-success fw-bold">
                        {{ number_format(session('order_data.total_amount'), 0, ',', '.') }}đ
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="alert alert-info mb-4">
            <h5>📧 Email xác nhận</h5>
            <p class="mb-0">
                Chúng tôi đã gửi email xác nhận đơn hàng đến <strong>{{ session('order_data.email') ?? 'email của bạn' }}</strong>.
                Vui lòng kiểm tra hộp thư (bao gồm thư mục spam) để xem chi tiết đơn hàng.
            </p>
        </div>

        <div class="mb-4">
            <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg me-3">
                🛍️ Tiếp tục mua sắm
            </a>
            <a href="{{ route('cart.history') }}" class="btn btn-outline-secondary btn-lg">
                📋 Xem lịch sử đơn hàng
            </a>
            @if(session('order_data.order_id'))
            <form action="{{ route('orders.cancel.session') }}" method="POST" class="d-inline-block ms-3"
                onsubmit="return confirm('Bạn có chắc muốn huỷ đơn hàng này?');">
                @csrf
                <input type="hidden" name="order_id" value="{{ session('order_data.order_id') }}">
                <button type="submit" class="btn btn-danger btn-lg">
                    ❌ Huỷ đơn hàng
                </button>
            </form>
            @endif
        </div>

        <div class="mt-5">
            <h5 style="color: #495057;">📞 Cần hỗ trợ?</h5>
            <p style="color: #6c757d;">
                Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ chúng tôi:<br>
                📧 Email: support@atoz.vn | 📱 Hotline: 1900-xxxx
            </p>
        </div>
    </div>
</div>
@endsection