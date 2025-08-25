<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ChoiXanh - Cửa hàng điện tử uy tín với đa dạng sản phẩm chất lượng cao">
    <meta name="keywords" content="máy tính, điện thoại, tivi, máy lạnh, mua sắm, điện tử">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ChoiXanh - Cửa hàng điện tử')</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/filters.css') }}">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js" async></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        .user-menu {
            position: relative;
        }

        .user-btn {
            background: none;
            border: none;
            color: #333;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .user-btn:hover {
            background-color: #f8f9fa;
        }

        .dropdown-menu {
            margin-top: 5px;
            border: 1px solid #dee2e6;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .dropdown-item {
            padding: 8px 16px;
            color: #333;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
            color: #333;
        }

        .dropdown-item.text-danger:hover {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>

<body class="modern-body">
    <!-- Header -->
    <header class="modern-header">
        <div class="header-top">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="contact-info">
                            <span><i class="bi bi-telephone-fill"></i> 1900-1234</span>
                            <span><i class="bi bi-envelope-fill"></i> info@choixanh.com</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="social-links">
                            <a href="#"><i class="bi bi-facebook"></i></a>
                            <a href="#"><i class="bi bi-instagram"></i></a>
                            <a href="#"><i class="bi bi-youtube"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Navigation -->
        <nav class="main-nav">
            <div class="container">
                <div class="nav-wrapper">
                    <!-- Logo -->
                    <div class="logo">
                        <a href="{{ route('products.index') }}">
                            <span class="logo-text animate__animated animate__bounce animate__delay-0.5s animate__slower">ChoiXanh</span>
                            <span class="logo-subtitle animate__animated animate__bounce animate__delay-0.5s animate__slower">Cửa hàng điện tử</span>
                        </a>
                    </div>

                    <!-- Search Bar -->
                    <div class="search-container">
                        <form action="{{ route('products.search') }}" method="GET" class="search-form">
                            <div class="search-input-group">
                                <input type="text" name="query" placeholder="Tìm kiếm sản phẩm..."
                                    autocomplete="off" id="search-input" class="search-input">
                                <button type="submit" class="search-btn">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                            <div id="suggestions" class="search-suggestions"></div>
                        </form>
                    </div>

                    <!-- User Actions -->
                    <div class="user-actions">
                        <a href="/wishlist" class="action-btn wishlist-btn" title="Yêu thích">
                            <i class="bi bi-heart-fill"></i>
                            <span class="badge" id="wishlist-count">0</span>
                        </a>
                        <a href="/cart" class="action-btn cart-btn" title="Giỏ hàng">
                            <i class="bi bi-cart-check"></i>
                            <span class="badge">{{ session('cart') ? count(session('cart')) : 0 }}</span>
                        </a>
                        <a href="/cart/api-demo" class="action-btn" title="Demo API Cart" style="background-color: #d4a017; color: #1a252f; padding: 8px 12px; border-radius: 5px; text-decoration: none; font-size: 12px; font-weight: bold;">
                            API Demo
                        </a>
                        @if (session('user_email'))
                        <div class="user-menu dropdown">
                            <button class="user-btn dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle"></i>
                                <span>{{ session('user_name') ?? session('user_email') }}</span>
                                <i class="bi bi-chevron-down"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="{{ route('cart.history') }}">
                                        <i class="bi bi-clock"></i> Lịch sử mua hàng
                                    </a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right"></i> Đăng xuất
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>

                        <!-- Nút đăng xuất trực tiếp (fallback)
                        <form action="{{ route('logout') }}" method="POST" style="margin-left: 10px;">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-box-arrow-right"></i> Đăng xuất
                            </button>
                        </form> -->
                        @else
                        <div class="auth-buttons">
                            <a href="{{ route('login.form') }}" class="btn btn-outline-primary">Đăng nhập</a>
                            <a href="{{ route('register.form') }}" class="btn btn-primary">Đăng ký</a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <!-- Category Navigation -->
        <div class="category-nav">
            <div class="container">
                <ul class="category-list">
                    <li><a href="{{ route('products.index') }}" class="category-link">
                            <i class="bi bi-house-fill"></i> Trang chủ
                        </a></li>
                    <li class="dropdown">
                        <a href="#" class="category-link dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-box"></i> Sản phẩm
                        </a>
                        <ul class="dropdown-menu category-dropdown">
                            <li><a class="dropdown-item" href="{{ route('products.index', ['category' => 'computer']) }}">
                                    <i class="bi bi-pc-display"></i> Máy tính
                                </a></li>
                            <li><a class="dropdown-item" href="{{ route('products.index', ['category' => 'phone']) }}">
                                    <i class="bi bi-phone-fill"></i> Điện thoại
                                </a></li>
                            <li><a class="dropdown-item" href="{{ route('products.index', ['category' => 'tv']) }}">
                                    <i class="bi bi-display"></i> Tivi
                                </a></li>
                            <li><a class="dropdown-item" href="{{ route('products.index', ['category' => 'air_conditioner']) }}">
                                    <i class="bi bi-fan"></i> Máy lạnh
                                </a></li>
                        </ul>
                    </li>
                    <li><a href="{{ route('news.index') }}" class="category-link">
                            <i class="bi bi-file-text"></i> Tin tức
                        </a></li>
                    <li><a href="{{ route('lien-he') }}" class="category-link">
                            <i class="bi bi-envelope-fill"></i> Liên hệ
                        </a></li>
                    <li><a href="{{ route('filters.index') }}" class="category-link">
                            <i class="bi bi-funnel"></i> Bộ lọc
                        </a></li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Footer -->
    @include('footer')

    <!-- Scripts -->
    <script src="{{ asset('js/wishlist.js') }}"></script>
    <script src="{{ asset('js/product.js') }}"></script>

    <script>
        $(document).ready(function() {
            const searchInput = $('#search-input');
            const suggestionsBox = $('#suggestions');

            searchInput.on('input', function() {
                let query = $(this).val().trim();
                if (query.length >= 2) {
                    $.ajax({
                        url: '{{ route("products.suggestions") }}',
                        method: 'GET',
                        data: {
                            query: query
                        },
                        success: function(response) {
                            suggestionsBox.empty().show();
                            if (response.length === 0) {
                                suggestionsBox.append('<div class="suggestion-item">Không có gợi ý</div>');
                                return;
                            }
                            response.forEach(item => {
                                let label = item.label;
                                suggestionsBox.append(
                                    `<div class="suggestion-item" data-value="${item.value}">${label}</div>`
                                );
                            });

                            $('.suggestion-item').on('click', function() {
                                searchInput.val($(this).data('value'));
                                suggestionsBox.empty().hide();
                                searchInput.closest('form').submit();
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error('Lỗi AJAX:', status, error);
                            suggestionsBox.empty().append('<div class="suggestion-item">Lỗi khi lấy gợi ý</div>').show();
                        }
                    });
                } else {
                    suggestionsBox.empty().hide();
                }
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.search-container').length) {
                    suggestionsBox.empty().hide();
                }
            });

            // Smooth scrolling for anchor links
            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();
                const target = $(this.getAttribute('href'));
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 100
                    }, 800);
                }
            });

            // Add scroll effect to header
            $(window).scroll(function() {
                if ($(this).scrollTop() > 100) {
                    $('.modern-header').addClass('scrolled');
                } else {
                    $('.modern-header').removeClass('scrolled');
                }
            });

            // Khởi tạo dropdown Bootstrap cho user menu
            if (typeof bootstrap !== 'undefined') {
                const userDropdown = document.getElementById('userDropdown');
                if (userDropdown) {
                    const dropdown = new bootstrap.Dropdown(userDropdown);
                }
            }

            // Khởi tạo tất cả dropdowns Bootstrap
            if (typeof bootstrap !== 'undefined') {
                // Khởi tạo dropdowns cho category navigation
                const categoryDropdowns = document.querySelectorAll('.category-nav .dropdown-toggle');
                categoryDropdowns.forEach(dropdownToggle => {
                    const dropdown = new bootstrap.Dropdown(dropdownToggle);
                });

                // Khởi tạo dropdowns cho user menu
                const userDropdowns = document.querySelectorAll('.user-menu .dropdown-toggle');
                userDropdowns.forEach(dropdownToggle => {
                    const dropdown = new bootstrap.Dropdown(dropdownToggle);
                });
            }

            // Đảm bảo click vào nút user luôn toggle dropdown
            $(document).on('click', '#userDropdown', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (typeof bootstrap !== 'undefined') {
                    const dropdown = bootstrap.Dropdown.getInstance(this);
                    if (dropdown) {
                        dropdown.toggle();
                    }
                }
            });

            // Đảm bảo click vào category dropdown luôn toggle
            $(document).on('click', '.category-nav .dropdown-toggle', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (typeof bootstrap !== 'undefined') {
                    const dropdown = bootstrap.Dropdown.getInstance(this);
                    if (dropdown) {
                        dropdown.toggle();
                    }
                }
            });
        });
    </script>
</body>

</html>