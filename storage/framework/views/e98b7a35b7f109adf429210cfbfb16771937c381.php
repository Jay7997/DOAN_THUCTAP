<?php $__env->startSection('title', $title); ?>
<?php $__env->startSection('content'); ?>
<div class="news-page">
    <div class="container">
        <h1 class="text-center mb-4">Tin tức công nghệ</h1>

        <?php if(empty($news) || $news->count() === 0): ?>
        <div class="text-center">
            <p class="text-muted">Không có tin tức nào.</p>
        </div>
        <?php else: ?>
        <div class="row">
            <?php $__currentLoopData = $news; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $newsItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-img-top-container" style="height: 200px; overflow: hidden;">
                        <img src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw=="
                            data-src="<?php echo e($newsItem['hinhdaidien'] ?? 'https://via.placeholder.com/300x200?text=No+Image'); ?>"
                            class="lazy card-img-top" alt="Ảnh tin tức"
                            style="width: 100%; height: 100%; object-fit: cover;">
                        <div class="image-placeholder text-center py-5">Đang tải...</div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">
                            <a href="<?php echo e(route('news.show', (string)$newsItem['id'])); ?>" class="text-decoration-none">
                                <?php echo e(\Illuminate\Support\Str::limit($newsItem['tieude'] ?? 'Không có tiêu đề', 80, '...')); ?>

                            </a>
                        </h5>
                        <p class="card-text flex-grow-1">
                            <?php echo e(\Illuminate\Support\Str::limit($newsItem['mota'] ?? $newsItem['noidungtomtat'] ?? 'Không có mô tả', 120, '...')); ?>

                        </p>
                        <div class="mt-auto">
                            <small class="text-muted">
                                Ngày đăng: <?php echo e($newsItem['ngay'] ?? $newsItem['ngaydang'] ?? 'N/A'); ?>

                            </small>
                            <div class="mt-2">
                                <a href="<?php echo e(route('news.show', (string)$newsItem['id'])); ?>" class="btn btn-primary btn-sm">
                                    Đọc thêm
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <?php if($news->hasPages()): ?>
        <div class="d-flex justify-content-center mt-4">
            <?php echo e($news->links()); ?>

        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const images = document.querySelectorAll('img.lazy');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.add('lazy-loaded');
                    img.parentElement.querySelector('.image-placeholder').style.display = 'none';
                    observer.unobserve(img);
                }
            });
        }, {
            rootMargin: '100px'
        });

        images.forEach(img => observer.observe(img));

        images.forEach(img => {
            img.addEventListener('error', () => {
                img.src = 'https://via.placeholder.com/300x200?text=No+Image';
                img.classList.add('lazy-loaded');
                img.parentElement.querySelector('.image-placeholder').style.display = 'none';
            });
        });
    });
</script>

<style>
    .news-page {
        padding: 2rem 0;
    }

    .card {
        transition: transform 0.2s ease-in-out;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .card-title a {
        color: #333;
    }

    .card-title a:hover {
        color: #007bff;
    }

    .image-placeholder {
        background-color: #f8f9fa;
        color: #6c757d;
        font-size: 0.9rem;
    }

    .lazy-loaded+.image-placeholder {
        display: none !important;
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\DOANTHUCTAP\resources\views/news/index.blade.php ENDPATH**/ ?>