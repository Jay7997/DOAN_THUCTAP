

<?php $__env->startSection('title', $title ?? 'Lịch sử mua hàng'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .purchase-history-container {
        max-width: 1200px;
        margin: 40px auto 100px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        font-family: Arial, sans-serif;
    }

    .purchase-history-container h1 {
        color: #d4a017;
        font-size: 2rem;
        text-align: center;
        margin-bottom: 30px;
    }

    .order-item {
        border-bottom: 1px solid #ddd;
        padding: 20px 0;
    }

    .order-item:last-child {
        border-bottom: none;
    }

    .order-details p {
        margin: 5px 0;
    }

    .order-details strong {
        color: #1a252f;
    }

    .btn-primary {
        background-color: #d4a017;
        border: none;
        color: #1a252f;
        font-weight: bold;
    }

    .btn-primary:hover {
        background-color: #b58900;
    }

    .text-success {
        color: #3e7c3e;
        font-weight: bold;
        text-align: center;
        margin-bottom: 20px;
    }

    .text-danger {
        color: #dc3545;
        font-weight: bold;
        text-align: center;
        margin-bottom: 20px;
    }

    @media (max-width: 576px) {
        .purchase-history-container {
            padding: 10px;
        }

        .purchase-history-container h1 {
            font-size: 1.5rem;
        }

        .order-details p {
            font-size: 0.9rem;
        }
    }
</style>

<div class="purchase-history-container">
    <h1>Lịch sử mua hàng</h1>

    <?php if(session('success')): ?>
        <div class="text-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="text-danger"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <?php $__empty_1 = true; $__currentLoopData = $purchase_history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="order-item">
            <div class="order-details">
                <p><strong>Mã đơn hàng:</strong> <?php echo e($order['order_id']); ?></p>
                <p><strong>Ngày đặt:</strong> <?php echo e(\Carbon\Carbon::parse($order['created_at'])->format('d/m/Y H:i')); ?></p>
                <p><strong>Tổng tiền:</strong> <?php echo e(number_format($order['total'], 0, ',', '.')); ?>đ</p>

                <p><strong>Thông tin người nhận:</strong></p>
                <p>Họ tên: <?php echo e($order['payment_data']['full_name'] ?? 'N/A'); ?></p>
                <p>Email: <?php echo e($order['payment_data']['email'] ?? 'N/A'); ?></p>
                <p>Số điện thoại: <?php echo e($order['payment_data']['sdt'] ?? 'N/A'); ?></p>
                <p>Địa chỉ: <?php echo e($order['payment_data']['address'] ?? 'N/A'); ?></p>

                <p><strong>Sản phẩm:</strong></p>
                <ul>
                    <?php $__currentLoopData = $order['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $itemId => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>
                            <a href="<?php echo e(route('products.show', $itemId)); ?>" class="text-decoration-none text-gold">
                                <?php echo e($item['tieude']); ?>

                            </a> - 
                            Giá: <?php echo e(number_format($item['gia'], 0, ',', '.')); ?>đ - 
                            SL: <?php echo e($item['quantity']); ?> - 
                            Tổng: <?php echo e(number_format($item['gia'] * $item['quantity'], 0, ',', '.')); ?>đ
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <?php if($order['status'] == 0): ?>
    <form action="<?php echo e(route('orders.cancel')); ?>" method="POST" onsubmit="return confirm('Bạn có chắc muốn huỷ đơn hàng này?')">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="order_id" value="<?php echo e($order['order_id']); ?>">
        <button type="submit" class="btn btn-danger mt-2">Huỷ đơn hàng</button>
    </form>
<?php endif; ?>

            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="text-center">Bạn chưa có đơn hàng nào.</p>
        <div class="text-center mt-3">
            <a href="<?php echo e(route('products.index')); ?>" class="btn btn-primary">Tiếp tục mua sắm</a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\tuan0\Downloads\DOANTHUCTAP\resources\views/cart/history.blade.php ENDPATH**/ ?>