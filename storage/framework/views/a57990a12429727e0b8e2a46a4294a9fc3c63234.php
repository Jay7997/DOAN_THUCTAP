<?php $__env->startSection('title', 'Đặt hàng thành công'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mt-5">
    <div class="text-center">
        <div style="font-size: 80px; color: #28a745; margin-bottom: 30px;">🎉</div>
        <h1 style="color: #28a745; margin-bottom: 20px;">Đặt hàng thành công!</h1>
        <p style="color: #6c757d; font-size: 18px; margin-bottom: 40px;">
            Cảm ơn bạn đã mua sắm tại Điện Máy AtoZ. Chúng tôi đã nhận được đơn hàng của bạn và đang xử lý.
        </p>

        <?php if(session('order_data')): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h4>📋 Thông tin đơn hàng</h4>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6"><strong>Họ và tên:</strong></div>
                    <div class="col-6"><?php echo e(session('order_data.full_name')); ?></div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><strong>Email:</strong></div>
                    <div class="col-6"><?php echo e(session('order_data.email')); ?></div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><strong>Số điện thoại:</strong></div>
                    <div class="col-6"><?php echo e(session('order_data.sdt')); ?></div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><strong>Địa chỉ:</strong></div>
                    <div class="col-6"><?php echo e(session('order_data.address')); ?></div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><strong>Phương thức thanh toán:</strong></div>
                    <div class="col-6">
                        <?php switch(session('order_data.payment_method')):
                        case ('cod'): ?>
                        💵 Thanh toán khi nhận hàng (COD)
                        <?php break; ?>
                        <?php case ('bank_transfer'): ?>
                        🏦 Chuyển khoản ngân hàng
                        <?php break; ?>
                        <?php case ('online_payment'): ?>
                        🌐 Thanh toán trực tuyến
                        <?php break; ?>
                        <?php default: ?>
                        <?php echo e(session('order_data.payment_method')); ?>

                        <?php endswitch; ?>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><strong>Tổng tiền:</strong></div>
                    <div class="col-6 text-success fw-bold">
                        <?php echo e(number_format(session('order_data.total_amount'), 0, ',', '.')); ?>đ
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="alert alert-info mb-4">
            <h5>📧 Email xác nhận</h5>
            <p class="mb-0">
                Chúng tôi đã gửi email xác nhận đơn hàng đến <strong><?php echo e(session('order_data.email') ?? 'email của bạn'); ?></strong>.
                Vui lòng kiểm tra hộp thư (bao gồm thư mục spam) để xem chi tiết đơn hàng.
            </p>
        </div>

        <div class="mb-4">
            <a href="<?php echo e(route('products.index')); ?>" class="btn btn-primary btn-lg me-3">
                🛍️ Tiếp tục mua sắm
            </a>
            <a href="<?php echo e(route('cart.history')); ?>" class="btn btn-outline-secondary btn-lg">
                📋 Xem lịch sử đơn hàng
            </a>
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\tuan0\Downloads\DOANTHUCTAP\resources\views/payment/success.blade.php ENDPATH**/ ?>