<?php $__env->startSection('title', $title); ?>

<?php $__env->startSection('content'); ?>
<div class="wishlist-container container mt-5">
    <h1 class="mb-4 text-center">Danh sách yêu thích</h1>

    <?php if(session('success')): ?>
    <div class="alert alert-success text-center"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <?php if(session('error')): ?>
    <div class="alert alert-danger text-center"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <?php if(empty($wishlist)): ?>
    <p class="text-center">Danh sách yêu thích của bạn đang trống.</p>
    <div class="text-center mt-3">
        <a href="<?php echo e(route('products.index')); ?>" class="btn btn-primary">Tiếp tục mua sắm</a>
    </div>
    <?php else: ?>
    <table class="table table-bordered text-center">
        <thead class="table-dark">
            <tr>
                <th>Hình ảnh</th>
                <th>Sản phẩm</th>
                <th>Giá</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $wishlist; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr id="row-wishlist-<?php echo e($id); ?>">
                <td>
                    <img src="<?php echo e($item['hinhdaidien']); ?>" width="100" alt="<?php echo e($item['tieude']); ?>">
                </td>
                <td>
                    <a href="<?php echo e(route('products.show', $id)); ?>">
                        <?php echo e($item['tieude']); ?>

                    </a>
                </td>
                <td><?php echo e($item['gia'] > 0 ? number_format($item['gia'], 0, ',', '.') . 'đ' : 'Liên hệ'); ?></td>
                <td>
                    <button class="btn btn-danger btn-sm btn-remove-wishlist" data-id="<?php echo e($id); ?>">
                        <i class="bi bi-trash"></i> Xoá
                    </button>
                    <a href="<?php echo e(route('cart.add', $id)); ?>" class="btn btn-primary btn-sm mt-2">Mua hàng</a>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<script src="<?php echo e(asset('js/wishlist.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\tuan0\Downloads\DOANTHUCTAP\resources\views/wishlist/index.blade.php ENDPATH**/ ?>