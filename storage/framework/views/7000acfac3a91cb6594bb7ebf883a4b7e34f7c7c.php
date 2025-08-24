


<?php $__env->startSection('title', 'Kết quả lọc sản phẩm'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mt-5">
    <h2 class="text-center mb-4">Kết quả lọc sản phẩm</h2>

    <div class="mb-3">
        <label>Danh mục: <?php echo e($categories[$categoryId] ?? 'Không xác định'); ?></label>
        <a href="<?php echo e(route('filters.index', ['category' => $categoryId])); ?>" class="btn btn-secondary">Chỉnh sửa bộ lọc</a>
    </div>

    <?php if(!empty($selectedFilters)): ?>
        <div class="mb-3">
            <h4>Bộ lọc đã chọn:</h4>
            <?php $__currentLoopData = $selectedFilters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupId => $filterIds): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $__currentLoopData = $filterIds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filterId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="badge bg-primary me-1"><?php echo e($filterId); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <?php if(!empty($product['hinhdaidien'])): ?>
                        <img src="<?php echo e($product['hinhdaidien']); ?>" class="card-img-top" alt="<?php echo e($product['tieude'] ?? 'N/A'); ?>">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo e($product['tieude'] ?? 'N/A'); ?></h5>
                        <p class="card-text">
                            <?php
                                $fields = [
                                    'thuonghieu' => 'Thương hiệu',
                                    'kichcomanhinh' => 'Kích cỡ màn hình',
                                    'tinhnangdacbiet' => 'Tính năng đặc biệt',
                                    'hieunangvapin' => 'Hiệu năng và pin',
                                    'camera' => 'Camera',
                                    'bonhotrong' => 'Bộ nhớ trong',
                                    'dungluongram' => 'Dung lượng RAM',
                                    'tansoquet' => 'Tần số quét',
                                    'chipxuli' => 'Chip xử lý',
                                    'cpu' => 'CPU',
                                    'mainboard' => 'Mainboard',
                                    'ram' => 'RAM',
                                    'ocung' => 'Ổ cứng',
                                    'carddohoa' => 'Card đồ họa',
                                    'nhucau' => 'Nhu cầu'
                                ];
                            ?>
                            <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(!empty($product[$key]) && is_array($product[$key])): ?>
                                    <?php echo e($label); ?>: <?php echo e($product[$key][0]['tengoi'] ?? 'N/A'); ?><br>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            Giá: <?php echo e(number_format($product['giakhuyenmai'] ?: $product['gia'] ?? 0, 0, ',', '.')); ?> VNĐ
                        </p>
                        <a href="<?php echo e(route('products.show', $product['id'])); ?>" class="fw-bold text-gold text-decoration-none d-block">Xem chi tiết</a>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-center">Không tìm thấy sản phẩm nào phù hợp với bộ lọc.</p>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\tuan0\Downloads\DOANTHUCTAP\resources\views/filters/results.blade.php ENDPATH**/ ?>