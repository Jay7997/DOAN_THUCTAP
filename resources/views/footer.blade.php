<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        /* Footer Styles */
        footer {
            background-color: #1a252f;
            /* Đồng bộ với màu nền navbar */
            color: #d4a017;
            /* Màu chữ vàng đồng bộ với navbar */
            padding: 40px 0;
            font-family: Arial, sans-serif;
            margin-top: 50px;
            /* Giảm margin-top để phù hợp hơn */
            z-index: 1000;
        }

        footer a {
            color: #d4a017;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: #ffffff;
            /* Hiệu ứng hover tương tự navbar */
        }

        footer .bi {
            font-size: 1.5rem;
            margin-right: 10px;
        }

        footer h5 {
            color: #d4a017;
            font-weight: bold;
            margin-bottom: 20px;
            font-size: 1.2rem;
        }

        footer p {
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        footer .social-links a {
            margin-right: 15px;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            footer .col-md-3 {
                margin-bottom: 30px;
            }
        }

        @media (max-width: 576px) {
            footer h5 {
                font-size: 1rem;
            }

            footer p {
                font-size: 0.85rem;
            }

            footer .bi {
                font-size: 1.2rem;
            }
        }
    </style>
</head>

<body>
    <footer class="modern-footer">
        <div class="footer-main">
            <div class="container">
                <div class="row">
                    <!-- Company Info -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="footer-section">
                            <div class="footer-logo mb-3">
                                <span class="logo-text">Chồi Xanh</span>
                                <span class="logo-subtitle">Cửa hàng điện tử</span>
                            </div>
                            <p class="footer-description">
                                Chồi Xanh tự hào là cửa hàng điện tử uy tín, cung cấp những sản phẩm chất lượng cao
                                với giá cả hợp lý. Chúng tôi cam kết mang đến trải nghiệm mua sắm tốt nhất cho khách hàng.
                            </p>
                            <div class="social-links">
                                <a href="#" class="social-link" title="Facebook">
                                    <i class="bi bi-facebook"></i>
                                </a>
                                <a href="#" class="social-link" title="Instagram">
                                    <i class="bi bi-instagram"></i>
                                </a>
                                <a href="#" class="social-link" title="YouTube">
                                    <i class="bi bi-youtube"></i>
                                </a>
                                <a href="#" class="social-link" title="Zalo">
                                    <i class="bi bi-chat-dots"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="col-lg-2 col-md-6 mb-4">
                        <div class="footer-section">
                            <h5 class="footer-title">Liên kết nhanh</h5>
                            <ul class="footer-links">
                                <li><a href="{{ route('products.index') }}">Trang chủ</a></li>
                                <li><a href="{{ route('products.index', ['category' => 'computer']) }}">Máy tính</a></li>
                                <li><a href="{{ route('products.index', ['category' => 'phone']) }}">Điện thoại</a></li>
                                <li><a href="{{ route('products.index', ['category' => 'tv']) }}">Tivi</a></li>
                                <li><a href="{{ route('products.index', ['category' => 'air_conditioner']) }}">Máy lạnh</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Customer Service -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="footer-section">
                            <h5 class="footer-title">Hỗ trợ khách hàng</h5>
                            <ul class="footer-links">
                                <li><a href="{{ route('lien-he') }}">Liên hệ</a></li>
                                <li><a href="#">Hướng dẫn mua hàng</a></li>
                                <li><a href="#">Chính sách bảo hành</a></li>
                                <li><a href="#">Chính sách đổi trả</a></li>
                                <li><a href="#">Vận chuyển & Giao hàng</a></li>
                                <li><a href="#">Thanh toán</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="footer-section">
                            <h5 class="footer-title">Thông tin liên hệ</h5>
                            <div class="contact-info">
                                <div class="contact-item">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    <div>
                                        <strong>Địa chỉ:</strong><br>
                                        82A Dân Tộc, Tân Sơn Nhì, Tân Phú, Hồ Chí Minh, Vietnam
                                    </div>
                                </div>
                                <div class="contact-item">
                                    <i class="bi bi-telephone-fill"></i>
                                    <div>
                                        <strong>Điện thoại:</strong><br>
                                        <a href="tel:19001234">028 3974 3179</a>
                                    </div>
                                </div>
                                <div class="contact-item">
                                    <i class="bi bi-envelope-fill"></i>
                                    <div>
                                        <strong>Email:</strong><br>
                                        <a href="mailto:info@choixanh.com">info@choixanh.com.vn</a>
                                    </div>
                                </div>
                                <div class="contact-item">
                                    <i class="bi bi-clock-fill"></i>
                                    <div>
                                        <strong>Giờ làm việc:</strong><br>
                                        8:00 - 17:30 (Thứ 2 - Thứ Bảy)
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="copyright">
                            © {{ date('Y') }} ChoiXanh. Tất cả quyền được bảo lưu.
                        </p>
                    </div>
                    <div class="col-md-6">
                        <div class="footer-bottom-links">
                            <a href="#">Chính sách bảo mật</a>
                            <a href="#">Điều khoản sử dụng</a>
                            <a href="#">Sitemap</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back to Top Button -->
        <button id="backToTop" class="back-to-top" title="Về đầu trang">
            <i class="bi bi-arrow-up"></i>
        </button>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Back to top functionality
            const backToTopButton = document.getElementById('backToTop');

            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    backToTopButton.classList.add('show');
                } else {
                    backToTopButton.classList.remove('show');
                }
            });

            backToTopButton.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            // Footer links hover effect
            const footerLinks = document.querySelectorAll('.footer-links a');
            footerLinks.forEach(link => {
                link.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateX(5px)';
                });

                link.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateX(0)';
                });
            });

            // Social links hover effect
            const socialLinks = document.querySelectorAll('.social-link');
            socialLinks.forEach(link => {
                link.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-3px) scale(1.1)';
                });

                link.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>