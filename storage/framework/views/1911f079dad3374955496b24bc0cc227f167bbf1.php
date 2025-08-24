

<?php $__env->startSection('title', 'Liên Hệ'); ?>

<?php $__env->startSection('content'); ?>
<style>
    /* General Contact Styles */
    .contact-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        font-family: Arial, sans-serif;
    }

    .contact-card {
        max-width: 600px; /* Tăng max-width để phù hợp với form dài hơn */
        margin: 40px auto;
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        padding: 20px;
    }

    .contact-card h2 {
        background-color: #1a252f; /* Đồng bộ với navbar */
        color: #d4a017; /* Màu vàng đồng bộ */
        font-size: 1.8rem;
        text-align: center;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    /* Form Styles */
    .form-label {
        color: #1a252f;
        font-weight: bold;
    }

    .form-control {
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 10px;
        transition: border-color 0.3s ease;
    }

    .form-control:focus {
        border-color: #d4a017;
        box-shadow: 0 0 5px rgba(212, 160, 23, 0.3);
    }

    .form-control.is-invalid {
        border-color: #dc3545;
    }

    .invalid-feedback {
        color: #dc3545;
        font-size: 0.9rem;
    }

    /* Textarea */
    textarea.form-control {
        resize: vertical; /* Chỉ cho phép thay đổi chiều cao */
        min-height: 100px;
    }

    /* Button Styles */
    .btn-primary {
        background-color: #d4a017; /* Màu vàng đồng bộ */
        border: none;
        color: #1a252f;
        font-weight: bold;
        transition: background-color 0.3s ease;
        padding: 10px 20px;
    }

    .btn-primary:hover {
        background-color: #b58900;
    }

    /* Alert Styles */
    .alert-success {
        color: #3e7c3e;
        background-color: #e6f3e6;
        border-color: #3e7c3e;
        border-radius: 5px;
        text-align: center;
        font-weight: bold;
    }

    .alert-danger {
        color: #dc3545;
        background-color: #f8d7da;
        border-color: #dc3545;
        border-radius: 5px;
        text-align: center;
        font-weight: bold;
    }

    /* Responsive */
    @media (max-width: 576px) {
        .contact-card {
            margin: 20px;
            padding: 15px;
        }

        .contact-card h2 {
            font-size: 1.5rem;
        }

        .btn-primary {
            padding: 8px 15px;
            font-size: 0.9rem;
        }

        .form-control {
            font-size: 0.9rem;
        }
    }
</style>

<div class="contact-container">
    <div class="contact-card">
        <h2>Liên Hệ Với Chúng Tôi</h2>

        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="<?php echo e(route('submit.contact')); ?>" method="POST" class="needs-validation" novalidate>
            <?php echo csrf_field(); ?>
            <div class="mb-3">
                <label for="name" class="form-label">Họ tên</label>
                <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="name" name="name" placeholder="Nhập họ tên" value="<?php echo e(old('name')); ?>" required>
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php else: ?>
                    <div class="invalid-feedback">Vui lòng nhập họ tên.</div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Địa chỉ email</label>
                <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="email" name="email" placeholder="Nhập địa chỉ email" value="<?php echo e(old('email')); ?>" required>
                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php else: ?>
                    <div class="invalid-feedback">Vui lòng nhập email hợp lệ.</div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Điện thoại</label>
                <input type="tel" class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="phone" name="phone" placeholder="Nhập số điện thoại" value="<?php echo e(old('phone')); ?>" required pattern="^(03|05|07|08|09)[0-9]{8}$" maxlength="10">
                <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php else: ?>
                    <div class="invalid-feedback">Vui lòng nhập số điện thoại hợp lệ.</div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Nội dung</label>
                <textarea class="form-control <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="message" name="message" rows="4" placeholder="Nhập nội dung" required><?php echo e(old('message')); ?></textarea>
                <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php else: ?>
                    <div class="invalid-feedback">Vui lòng nhập nội dung.</div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="captcha" class="form-label">Mã xác nhận: 9999</label>
                <input type="text" class="form-control <?php $__errorArgs = ['captcha'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="captcha" name="captcha" placeholder="Nhập mã xác nhận" value="<?php echo e(old('captcha')); ?>" required>
                <?php $__errorArgs = ['captcha'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php else: ?>
                    <div class="invalid-feedback">Vui lòng nhập mã xác nhận chính xác.</div>
                <?php endif; ?>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Gửi đi</button>
            </div>
        </form>
    </div>
</div>

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
                console.log(new FormData(form));
            }, false);
        });
    })();

    document.getElementById('phone').addEventListener('input', function(e) {
        const value = e.target.value;
        const regex = /^(03|05|07|08|09)[0-9]{8}$/;
        if (!regex.test(value) && value.length > 0) {
            e.target.classList.add('is-invalid');
        } else {
            e.target.classList.remove('is-invalid');
        }
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\tuan0\Downloads\DOANTHUCTAP\resources\views/pages/contact.blade.php ENDPATH**/ ?>