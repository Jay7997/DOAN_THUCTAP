

<?php $__env->startSection('title', 'Bộ lọc sản phẩm'); ?>

<?php $__env->startSection('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/filters.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container mt-5">
    <h2 class="text-center mb-4">Bộ lọc sản phẩm</h2>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if(session('success')): ?>
        <div class="alert alert-success">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('filters.index')); ?>" method="GET" class="mb-5">
        <div class="mb-3">
            <label for="category" class="form-label">Chọn danh mục</label>
            <select name="category" id="category" class="form-select" required onchange="this.form.submit()">
                <option value="">Chọn danh mục</option>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($id); ?>" <?php echo e($categoryId == $id ? 'selected' : ''); ?>><?php echo e($name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
    </form>

    <?php if(!empty($filters) && !empty($categoryId)): ?>
        <form id="filters-form" action="<?php echo e(route('filters.apply')); ?>" method="POST" class="needs-validation" novalidate>
            <?php echo csrf_field(); ?>
            <input type="hidden" name="category" value="<?php echo e($categoryId); ?>">
            <div class="filter-container">
                <?php $__currentLoopData = $filters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="filter-group">
                        <h3 data-bs-toggle="collapse" 
                            data-bs-target="#filters-<?php echo e($group['id']); ?>"
                            aria-expanded="true" 
                            aria-controls="filters-<?php echo e($group['id']); ?>">
                            <?php echo e($group['tieude']); ?>

                        </h3>
                        <div class="collapse show" id="filters-<?php echo e($group['id']); ?>">
                            <?php $__currentLoopData = $group['details']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="filters[<?php echo e($group['id']); ?>][]" 
                                           id="filter-<?php echo e($filter['ma']); ?>" 
                                           value="<?php echo e($filter['ma']); ?>" 
                                           data-alias="<?php echo e($filter['url']); ?>">
                                    <label class="form-check-label" for="filter-<?php echo e($filter['ma']); ?>"><?php echo e($filter['tengoi']); ?></label>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <button type="submit" class="btn btn-primary">Áp dụng bộ lọc</button>
            <a href="<?php echo e(route('products.index')); ?>" class="btn btn-secondary">Tiếp tục mua sắm</a>
        </form>
    <?php elseif($categoryId): ?>
        <p class="text-center">Không có bộ lọc nào cho danh mục này!</p>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    (function() {
        'use strict';
        var forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\tuan0\Downloads\DOANTHUCTAP\resources\views/filters/index.blade.php ENDPATH**/ ?>