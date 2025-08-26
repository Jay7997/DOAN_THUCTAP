@extends('layouts.app')

@section('title', 'Công ty Chồi Xanh Media - Chuyên cung cấp máy tinh và thiết bị công nghệ ')

@push('styles')
<style>
    .product-link-disabled {
        color: #999 !important;
        font-weight: 500;
        cursor: not-allowed;
    }
    
    .btn-quick-view:disabled,
    .btn-wishlist:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>
@endpush

@section('content')
<div class="container">


    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-content">
            <h1>Công ty TNHH Chồi Xanh Media </h1>
            <p>Chuyên Cung Cấp máy tính và Thiết Bị Công Nghệ -Những sản phẩm điện tử chất lượng cao với giá cả hợp lý</p>
            <div class="hero-buttons">
                <!-- <a href="#products" class="btn btn-primary">Xem sản phẩm</a>
                <a href="{{ route('filters.index') }}" class="btn btn-outline-primary">Bộ lọc nâng cao</a> -->
            </div>
        </div>
    </div>

    <!-- Category Pills -->
    <div class="category-pills mb-4">
        <div class="d-flex flex-wrap gap-2 justify-content-center">
            <a href="{{ route('products.index') }}" class="category-pill {{ !request('category') ? 'active' : '' }}">
                <i class="bi bi-grid-3x3-gap"></i> Tất cả
            </a>
            <a href="{{ route('products.index', ['category' => 'computer']) }}"
                class="category-pill {{ request('category') == 'computer' ? 'active' : '' }}">
                <i class="bi bi-pc-display"></i> Máy tính
            </a>
            <a href="{{ route('products.index', ['category' => 'phone']) }}"
                class="category-pill {{ request('category') == 'phone' ? 'active' : '' }}">
                <i class="bi bi-phone-fill"></i> Điện thoại
            </a>
            <a href="{{ route('products.index', ['category' => 'tv']) }}"
                class="category-pill {{ request('category') == 'tv' ? 'active' : '' }}">
                <i class="bi bi-display"></i> Tivi
            </a>
            <a href="{{ route('products.index', ['category' => 'air_conditioner']) }}"
                class="category-pill {{ request('category') == 'air_conditioner' ? 'active' : '' }}">
                <i class="bi bi-fan"></i> Máy lạnh
            </a>
        </div>
    </div>

    <!-- Search Results Info -->
    @if(request('query'))
    <div class="search-results-info mb-4">
        <div class="alert alert-info">
            <i class="bi bi-search"></i>
            Kết quả tìm kiếm cho: "<strong>{{ request('query') }}</strong>"
            <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-primary ms-3">
                <i class="bi bi-x-circle"></i> Xóa tìm kiếm
            </a>
        </div>
    </div>
    @endif

    <!-- Products Section -->
    <section id="products" class="products-section">
        @if(isset($data['products']) && is_array($data['products']) && count($data['products']) > 0)
        <div class="products-header mb-4">
            <h2 class="section-title">
                @if(request('category'))
                @switch(request('category'))
                @case('computer')
                <i class="bi bi-pc-display"></i> Máy tính
                @break
                @case('phone')
                <i class="bi bi-phone-fill"></i> Điện thoại di động
                @break
                @case('tv')
                <i class="bi bi-display"></i> Tivi
                @break
                @case('air_conditioner')
                <i class="bi bi-fan"></i> Máy lạnh
                @break
                @default
                Sản phẩm
                @endswitch
                @else
                <i class="bi bi-grid-3x3-gap"></i> Tất cả sản phẩm
                @endif
            </h2>
            <p class="section-subtitle">
                Tìm thấy {{ count($data['products']) }} sản phẩm
            </p>
        </div>

        <div class="product-grid">
            @foreach($data['products'] as $product)
            @php
                $productUrl = !empty($product['id']) ? $product['id'] : null;
            @endphp
            <div class="product-card fade-in-up" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <div class="product-image-container">
                    <img src="{{ $product['hinhdaidien'] ?? asset('images/default-product.jpg') }}"
                        alt="{{ $product['tieude'] ?? 'Sản phẩm' }}"
                        class="product-image lazyload"
                        loading="lazy">
                    <div class="product-overlay">
                        @if($productUrl)
                            <button class="btn-quick-view"
                                data-product-id="{{ $productUrl }}"
                                data-product-title="{{ $product['tieude'] ?? 'Sản phẩm' }}">
                                <i class="bi bi-eye"></i>
                            </button>
                        @else
                            <button class="btn-quick-view" disabled title="Sản phẩm không có ID">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <div class="product-info">
                    <h3 class="product-title">
                        @if($productUrl)
                            <a href="{{ route('products.show', ['id' => $productUrl]) }}"
                                class="product-link">
                                {{ $product['tieude'] ?? 'Tên sản phẩm' }}
                            </a>
                        @else
                            <span class="product-link-disabled">
                                {{ $product['tieude'] ?? 'Tên sản phẩm' }}
                            </span>
                        @endif
                    </h3>

                    <div class="product-meta">
                        @if(isset($product['category']))
                        <span class="product-category">
                            <i class="bi bi-tag"></i> {{ $product['category'] }}
                        </span>
                        @endif
                        @if(isset($product['rating']))
                        <div class="product-rating">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star{{ $i <= $product['rating'] ? '-fill' : '' }}"></i>
                                @endfor
                                <span class="rating-text">({{ $product['rating'] }})</span>
                        </div>
                        @endif
                    </div>

                    <div class="product-price">
                        @if(isset($product['gia']) && $product['gia'] > 0)
                        <span class="current-price">{{ number_format($product['gia']) }} ₫</span>
                        @if(isset($product['giacu']) && $product['giacu'] > $product['gia'])
                        <span class="old-price">{{ number_format($product['giacu']) }} ₫</span>
                        @endif
                        @else
                        <span class="price-coming">Giá liên hệ</span>
                        @endif
                    </div>

                    <p class="product-description">
                        {{ $product['mota'] ?? 'Mô tả sản phẩm sẽ được hiển thị ở đây...' }}
                    </p>

                    <div class="product-actions">
                        @if($productUrl)
                            <button class="btn btn-primary btn-lg" onclick="addToCart('{{ $productUrl }}', '{{ addslashes($product['tieude'] ?? '') }}', {{ $product['gia'] ?? 0 }}, '{{ $product['hinhdaidien'] ?? '' }}')">
                                        🛒 Thêm vào giỏ hàng
                                    </button>
                            <button class="btn-wishlist"
                                data-product-id="{{ $productUrl }}"
                                title="Thêm vào yêu thích">
                                <i class="bi bi-heart"></i>
                            </button>
                        @else
                            <button class="btn btn-secondary btn-lg" disabled title="Sản phẩm không có ID">
                                ❌ Không thể đặt hàng
                            </button>
                            <button class="btn-wishlist" disabled title="Sản phẩm không có ID">
                                <i class="bi bi-heart-slash"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if(isset($data['products']) && count($data['products']) > 12)
        <div class="pagination-wrapper mt-5">
            <nav aria-label="Phân trang sản phẩm">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <span class="page-link">Trước</span>
                    </li>
                    <li class="page-item active">
                        <span class="page-link">1</span>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">2</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">3</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">Tiếp</a>
                    </li>
                </ul>
            </nav>
        </div>
        @endif

        @else
        <!-- No Products Found -->
        <div class="no-products">
            <div class="no-products-content text-center">
                <div class="no-products-icon">
                    <i class="bi bi-search"></i>
                </div>
                <h3>Không tìm thấy sản phẩm</h3>
                <p>
                    @if(isset($data['error']))
                    {{ $data['error'] }}
                    @else
                    Hãy thử tìm kiếm với từ khóa khác hoặc xem tất cả sản phẩm
                    @endif
                </p>
                <div class="no-products-actions">
                    <a href="{{ route('products.index') }}" class="btn btn-primary">
                        <i class="bi bi-house"></i> Về trang chủ
                    </a>
                    <a href="{{ route('filters.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-funnel"></i> Sử dụng bộ lọc
                    </a>
                </div>
            </div>
        </div>
        @endif
    </section>

    <!-- Features Section -->
    <section class="features-section mt-5">
        <div class="row">
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="feature-card text-center">
                    <!-- <div class="feature-icon">
                        <i class="bi bi-shield-check"></i>
                    </div> -->
                    <h4>Chất lượng đảm bảo</h4>
                    <p>Sản phẩm chính hãng, bảo hành chính thức</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="feature-card text-center">
                    <!-- <div class="feature-icon">
                        <i class="bi bi-truck"></i>
                    </div> -->
                    <h4>Giao hàng nhanh chóng</h4>
                    <p>Giao hàng toàn quốc trong 24-48 giờ</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="feature-card text-center">
                    <!-- <div class="feature-icon">
                        <i class="bi bi-headset"></i>
                    </div> -->
                    <h4>Hỗ trợ 24/7</h4>
                    <p>Đội ngũ tư vấn chuyên nghiệp</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="feature-card text-center">
                    <!-- <div class="feature-icon">
                        <i class="bi bi-arrow-repeat"></i>
                    </div> -->
                    <h4>Đổi trả dễ dàng</h4>
                    <p>Chính sách đổi trả trong 30 ngày</p>
                </div>
            </div>
        </div>
        <div class="container my-5">
            <h2 class="text-center mb-4">Bản đồ</h2>
            <div class="full-height full-width googlemap">
                <iframe frameborder="0" height="400" src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d3919.4586764522687!2d106.64606400000001!3d10.776139!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752ebbf753cdd5%3A0xcc86f2c52d97c5af!2zVGhp4bq_dCBr4bq_IHdlYiBDaOG7k2kgWGFuaA!5e0!3m2!1svi!2sus!4v1491785812225" style="border:0;width:100%" allowfullscreen></iframe>
            </div>
        </div>
    </section>
</div>

<!-- Quick View Modal -->
<div class="modal fade" id="quickViewModal" tabindex="-1" aria-labelledby="quickViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickViewModalLabel">Xem nhanh sản phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="product-image-gallery">
                            <img src="" alt="Product" class="img-fluid quick-view-image mb-3" id="quickViewMainImage">
                            <div class="thumbnail-images d-flex gap-2" id="quickViewThumbnails">
                                <!-- Thumbnail images will be populated here -->
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="product-details">
                            <h4 class="quick-view-title mb-3"></h4>

                            <div class="product-meta mb-3">
                                <div class="category-badge mb-2">
                                    <span class="badge bg-primary" id="quickViewCategory"></span>
                                </div>
                                <div class="rating-display" id="quickViewRating">
                                    <!-- Rating stars will be populated here -->
                                </div>
                            </div>

                            <div class="quick-view-price mb-3">
                                <div class="current-price-display" id="quickViewCurrentPrice"></div>
                                <div class="old-price-display" id="quickViewOldPrice"></div>
                            </div>

                            <div class="product-specs mb-3" id="quickViewSpecs">
                                <!-- Product specifications will be populated here -->
                            </div>

                            <p class="quick-view-description mb-4"></p>

                            <div class="quick-view-actions">
                                <button class="btn btn-primary btn-lg me-2" id="quickViewAddToCart">
                                    <i class="bi bi-cart-plus"></i> Thêm vào giỏ hàng
                                </button>
                                <button class="btn btn-outline-danger btn-lg me-2" id="quickViewWishlist">
                                    <i class="bi bi-heart"></i> Yêu thích
                                </button>
                                <a href="" class="btn btn-outline-primary btn-lg" id="quickViewDetailLink">
                                    <i class="bi bi-eye"></i> Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add to Cart Success Toast -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="addToCartToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <i class="bi bi-check-circle-fill text-success me-2"></i>
            <strong class="me-auto">Thành công!</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Sản phẩm đã được thêm vào giỏ hàng!
        </div>
    </div>
</div>

<!-- News Section -->
@if(isset($news) && $news->count() > 0)
<section class="news-section mt-5">
    <div class="container">
        <div class="section-header text-center mb-4">
            <h2 class="section-title">
                <i class="bi bi-newspaper"></i> Tin tức công nghệ
            </h2>
            <p class="section-subtitle">Cập nhật những tin tức mới nhất về công nghệ</p>
        </div>

        <div class="row">
            @foreach($news->take(6) as $newsItem)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="news-card card h-100 shadow-sm">
                    <div class="news-image-container" style="height: 200px; overflow: hidden;">
                        <img src="{{ $newsItem['hinhdaidien'] ?? 'https://via.placeholder.com/300x200?text=No+Image' }}"
                            alt="Ảnh tin tức"
                            class="card-img-top"
                            style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">
                            <a href="{{ route('news.show', (string)$newsItem['id']) }}" class="text-decoration-none">
                                {{ \Illuminate\Support\Str::limit($newsItem['tieude'] ?? 'Không có tiêu đề', 80, '...') }}
                            </a>
                        </h5>
                        <p class="card-text flex-grow-1">
                            {{ \Illuminate\Support\Str::limit($newsItem['mota'] ?? $newsItem['noidungtomtat'] ?? 'Không có mô tả', 120, '...') }}
                        </p>
                        <div class="mt-auto">
                            <small class="text-muted">
                                Ngày đăng: {{ $newsItem['ngay'] ?? $newsItem['ngaydang'] ?? 'N/A' }}
                            </small>
                            <div class="mt-2">
                                <a href="{{ route('news.show', (string)$newsItem['id']) }}" class="btn btn-outline-primary btn-sm">
                                    Đọc thêm
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('news.index') }}" class="btn btn-primary">
                <i class="bi bi-arrow-right"></i> Xem tất cả tin tức
            </a>
        </div>
</section>
@endif
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/product.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing JavaScript...');

        // Quick View functionality
        const quickViewButtons = document.querySelectorAll('.btn-quick-view');
        const quickViewModalElement = document.getElementById('quickViewModal');

        if (!quickViewModalElement) {
            console.warn('Quick view modal not found');
            return;
        }

        const quickViewModal = new bootstrap.Modal(quickViewModalElement);

        quickViewButtons.forEach(button => {
            button.addEventListener('click', function() {
                const productCard = this.closest('.product-card');
                const productData = extractProductData(productCard);

                // Populate modal with product data
                populateQuickViewModal(productData);

                // Show modal
                quickViewModal.show();
            });
        });

        // Function to extract product data from product card
        function extractProductData(productCard) {
            const productData = {};

            // Extract basic info
            productData.id = productCard.querySelector('.btn-add-cart').dataset.productId;
            productData.name = productCard.querySelector('.product-title a').textContent.trim();
            productData.image = productCard.querySelector('.product-image').src;
            productData.description = productCard.querySelector('.product-description').textContent.trim();

            // Extract category
            const categoryElement = productCard.querySelector('.product-category');
            if (categoryElement) {
                productData.category = categoryElement.textContent.replace('🏷️', '').trim();
            }

            // Extract price
            const currentPriceElement = productCard.querySelector('.current-price');
            if (currentPriceElement) {
                productData.price = currentPriceElement.textContent.trim();
            }

            const oldPriceElement = productCard.querySelector('.old-price');
            if (oldPriceElement) {
                productData.oldPrice = oldPriceElement.textContent.trim();
            }

            // Extract rating if exists
            const ratingElement = productCard.querySelector('.product-rating');
            if (ratingElement) {
                const stars = ratingElement.querySelectorAll('.bi-star-fill');
                productData.rating = stars.length;
            }

            // Extract additional specs from data attributes if available
            const addToCartBtn = productCard.querySelector('.btn-add-cart');
            if (addToCartBtn.dataset.productSpecs) {
                try {
                    productData.specs = JSON.parse(addToCartBtn.dataset.productSpecs);
                } catch (e) {
                    productData.specs = {};
                }
            }

            return productData;
        }

        // Function to populate Quick View modal
        function populateQuickViewModal(productData) {
            // Set modal title
            document.getElementById('quickViewModalLabel').textContent = 'Xem nhanh: ' + productData.name;

            // Set product title
            document.getElementById('quickViewTitle').textContent = productData.name;

            // Set main image
            const mainImage = document.getElementById('quickViewMainImage');
            mainImage.src = productData.image;
            mainImage.alt = productData.name;

            // Set category
            const categoryElement = document.getElementById('quickViewCategory');
            if (productData.category) {
                categoryElement.textContent = productData.category;
                categoryElement.style.display = 'inline-block';
            } else {
                categoryElement.style.display = 'none';
            }

            // Set rating
            const ratingElement = document.getElementById('quickViewRating');
            if (productData.rating) {
                let ratingHTML = '';
                for (let i = 1; i <= 5; i++) {
                    ratingHTML += `<i class="bi bi-star${i <= productData.rating ? '-fill' : ''} text-warning"></i>`;
                }
                ratingHTML += `<span class="ms-2 text-muted">(${productData.rating}/5)</span>`;
                ratingElement.innerHTML = ratingHTML;
                ratingElement.style.display = 'block';
            } else {
                ratingElement.style.display = 'none';
            }

            // Set price
            const currentPriceElement = document.getElementById('quickViewCurrentPrice');
            const oldPriceElement = document.getElementById('quickViewOldPrice');

            if (productData.price) {
                currentPriceElement.innerHTML = `<span class="h4 text-primary fw-bold">${productData.price}</span>`;
                currentPriceElement.style.display = 'block';
            } else {
                currentPriceElement.style.display = 'none';
            }

            if (productData.oldPrice) {
                oldPriceElement.innerHTML = `<span class="text-muted text-decoration-line-through">${productData.oldPrice}</span>`;
                oldPriceElement.style.display = 'block';
            } else {
                oldPriceElement.style.display = 'none';
            }

            // Set description
            document.getElementById('quickViewDescription').textContent = productData.description;

            // Set specifications if available
            const specsElement = document.getElementById('quickViewSpecs');
            if (productData.specs && Object.keys(productData.specs).length > 0) {
                let specsHTML = '<h6 class="fw-bold mb-2">Thông số kỹ thuật:</h6><div class="row">';
                Object.entries(productData.specs).forEach(([key, value]) => {
                    if (value && value !== 'N/A' && value !== '') {
                        specsHTML += `
                            <div class="col-6 mb-2">
                                <strong>${key}:</strong> ${value}
                            </div>
                        `;
                    }
                });
                specsHTML += '</div>';
                specsElement.innerHTML = specsHTML;
                specsElement.style.display = 'block';
            } else {
                specsElement.style.display = 'none';
            }

            // Set detail link
            const detailLink = document.getElementById('quickViewDetailLink');
            detailLink.href = `/products/${productData.id}`;

            // Set data attributes for add to cart and wishlist
            const addToCartBtn = document.getElementById('quickViewAddToCart');
            const wishlistBtn = document.getElementById('quickViewWishlist');

            addToCartBtn.dataset.productId = productData.id;
            addToCartBtn.dataset.productName = productData.name;

            wishlistBtn.dataset.productId = productData.id;

            // Check if product is already in wishlist
            const originalWishlistBtn = document.querySelector(`[data-product-id="${productData.id}"].btn-wishlist`);
            if (originalWishlistBtn && originalWishlistBtn.classList.contains('active')) {
                wishlistBtn.classList.add('active');
                wishlistBtn.innerHTML = '<i class="bi bi-heart-fill"></i> Đã yêu thích';
            } else {
                wishlistBtn.classList.remove('active');
                wishlistBtn.innerHTML = '<i class="bi bi-heart"></i> Yêu thích';
            }
        }

        // Quick View Add to Cart functionality
        const quickViewAddToCartBtn = document.getElementById('quickViewAddToCart');
        if (quickViewAddToCartBtn) {
            quickViewAddToCartBtn.addEventListener('click', function() {
                const productId = this.dataset.productId;
                const productName = this.dataset.productName;

                // Add loading state
                this.innerHTML = '<i class="bi bi-hourglass-split"></i> Đang thêm...';
                this.disabled = true;

                // Call actual API to add to cart
                fetch(`/cart/add/${productId}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    })
                    .then(response => {
                        if (response.ok) {
                            // Show success toast
                            const addToCartToast = new bootstrap.Toast(document.getElementById('addToCartToast'));
                            addToCartToast.show();

                            // Update cart count
                            const cartBadge = document.querySelector('.cart-btn .badge');
                            if (cartBadge) {
                                const currentCount = parseInt(cartBadge.textContent) || 0;
                                cartBadge.textContent = currentCount + 1;
                            }

                            // Show success message
                            console.log('Product added to cart successfully');

                            // Close modal
                            quickViewModal.hide();
                        } else {
                            throw new Error('Failed to add product to cart');
                        }
                    })
                    .catch(error => {
                        console.error('Error adding product to cart:', error);
                        // Show error message
                        alert('Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng');
                    })
                    .finally(() => {
                        // Reset button
                        this.innerHTML = '<i class="bi bi-cart-plus"></i> Thêm vào giỏ';
                        this.disabled = false;
                    });
            });
        } else {
            console.warn('Quick view add to cart button not found');
        }

        // Quick View Wishlist functionality
        const quickViewWishlistBtn = document.getElementById('quickViewWishlist');
        if (quickViewWishlistBtn) {
            quickViewWishlistBtn.addEventListener('click', function() {
                const productId = this.dataset.productId;
                const isAdding = !this.classList.contains('active');

                // Call actual API to add/remove from wishlist
                fetch(`/wishlist/${isAdding ? 'add' : 'remove'}/${productId}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success !== false) {
                            // Toggle active state
                            this.classList.toggle('active');

                            // Update button text
                            if (this.classList.contains('active')) {
                                this.innerHTML = '<i class="bi bi-heart-fill"></i> Đã yêu thích';
                            } else {
                                this.innerHTML = '<i class="bi bi-heart"></i> Yêu thích';
                            }

                            // Update original wishlist button
                            const originalWishlistBtn = document.querySelector(`[data-product-id="${productId}"].btn-wishlist`);
                            if (originalWishlistBtn) {
                                originalWishlistBtn.classList.toggle('active');
                            }

                            // Update wishlist count with actual count from server
                            const wishlistBadge = document.getElementById('wishlist-count');
                            if (wishlistBadge) {
                                // Fetch actual wishlist count from server
                                fetch('/wishlist/count', {
                                        method: 'GET',
                                        headers: {
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'Accept': 'application/json',
                                        },
                                    })
                                    .then(response => response.json())
                                    .then(countData => {
                                        if (countData.count !== undefined) {
                                            wishlistBadge.textContent = countData.count;
                                        } else {
                                            // Fallback: manual calculation
                                            const currentCount = parseInt(wishlistBadge.textContent) || 0;
                                            if (this.classList.contains('active')) {
                                                wishlistBadge.textContent = currentCount + 1;
                                            } else {
                                                wishlistBadge.textContent = Math.max(0, currentCount - 1);
                                            }
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error fetching wishlist count:', error);
                                        // Fallback: manual calculation
                                        const currentCount = parseInt(wishlistBadge.textContent) || 0;
                                        if (this.classList.contains('active')) {
                                            wishlistBadge.textContent = currentCount + 1;
                                        } else {
                                            wishlistBadge.textContent = Math.max(0, currentCount - 1);
                                        }
                                    });
                            }

                            // Show success message
                            console.log(`Product ${isAdding ? 'added to' : 'removed from'} wishlist successfully:`, data.message || data.thongbao);
                        } else {
                            throw new Error(data.error || `Failed to ${isAdding ? 'add' : 'remove'} product from wishlist`);
                        }
                    })
                    .catch(error => {
                        console.error('Error updating wishlist:', error);
                        // Show error message
                        alert(`Có lỗi xảy ra khi ${isAdding ? 'thêm' : 'xóa'} sản phẩm khỏi yêu thích`);
                    });
            });
        } else {
            console.warn('Quick view wishlist button not found');
        }

        // Add to Cart functionality for main product cards
        const addToCartButtons = document.querySelectorAll('.btn-add-cart');
        const addToCartToastElement = document.getElementById('addToCartToast');

        if (addToCartToastElement) {
            const addToCartToast = new bootstrap.Toast(addToCartToastElement);

            addToCartButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.dataset.productId;
                    const productName = this.dataset.productName;

                    // Add loading state
                    this.innerHTML = '<i class="bi bi-hourglass-split"></i> Đang thêm...';
                    this.disabled = true;

                    // Call actual API to add to cart
                    fetch(`/cart/add/${productId}`, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Show success toast
                                addToCartToast.show();

                                // Update cart count with actual count from server
                                const cartBadge = document.querySelector('.cart-btn .badge');
                                if (cartBadge && data.cartCount !== undefined) {
                                    cartBadge.textContent = data.cartCount;
                                } else if (cartBadge) {
                                    // Fallback: increment by 1
                                    const currentCount = parseInt(cartBadge.textContent) || 0;
                                    cartBadge.textContent = currentCount + 1;
                                }

                                // Show success message
                                console.log('Product added to cart successfully:', data.message);
                            } else {
                                throw new Error(data.error || 'Failed to add product to cart');
                            }
                        })
                        .catch(error => {
                            console.error('Error adding product to cart:', error);
                            // Show error message
                            alert('Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng');
                        })
                        .finally(() => {
                            // Reset button
                            this.innerHTML = '<i class="bi bi-cart-plus"></i> Thêm vào giỏ';
                            this.disabled = false;
                        });
                });
            });
        }

        // Wishlist functionality for main product cards
        const wishlistButtons = document.querySelectorAll('.btn-wishlist');

        wishlistButtons.forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.productId;
                const isAdding = !this.classList.contains('active');

                // Call actual API to add/remove from wishlist
                fetch(`/wishlist/${isAdding ? 'add' : 'remove'}/${productId}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success !== false) {
                            // Toggle active state
                            this.classList.toggle('active');

                            // Update wishlist count with actual count from server
                            const wishlistBadge = document.getElementById('wishlist-count');
                            if (wishlistBadge) {
                                // Fetch actual wishlist count from server
                                fetch('/wishlist/count', {
                                        method: 'GET',
                                        headers: {
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'Accept': 'application/json',
                                        },
                                    })
                                    .then(response => response.json())
                                    .then(countData => {
                                        if (countData.count !== undefined) {
                                            wishlistBadge.textContent = countData.count;
                                        } else {
                                            // Fallback: manual calculation
                                            const currentCount = parseInt(wishlistBadge.textContent) || 0;
                                            if (this.classList.contains('active')) {
                                                wishlistBadge.textContent = currentCount + 1;
                                            } else {
                                                wishlistBadge.textContent = Math.max(0, currentCount - 1);
                                            }
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error fetching wishlist count:', error);
                                        // Fallback: manual calculation
                                        const currentCount = parseInt(wishlistBadge.textContent) || 0;
                                        if (this.classList.contains('active')) {
                                            wishlistBadge.textContent = currentCount + 1;
                                        } else {
                                            wishlistBadge.textContent = Math.max(0, currentCount - 1);
                                        }
                                    });
                            }

                            // Show success message
                            console.log(`Product ${isAdding ? 'added to' : 'removed from'} wishlist successfully:`, data.message || data.thongbao);
                        } else {
                            throw new Error(data.error || `Failed to ${isAdding ? 'add' : 'remove'} product from wishlist`);
                        }
                    })
                    .catch(error => {
                        console.error('Error updating wishlist:', error);
                        // Show error message
                        alert(`Có lỗi xảy ra khi ${isAdding ? 'thêm' : 'xóa'} sản phẩm khỏi yêu thích`);
                    });
            });
        });

        // Smooth scrolling for category pills
        const categoryPills = document.querySelectorAll('.category-pill');
        categoryPills.forEach(pill => {
            pill.addEventListener('click', function(e) {
                // Remove active class from all pills
                categoryPills.forEach(p => p.classList.remove('active'));
                // Add active class to clicked pill
                this.classList.add('active');
            });
        });

        // Load initial wishlist count
        function loadWishlistCount() {
            const wishlistBadge = document.getElementById('wishlist-count');
            if (wishlistBadge) {
                fetch('/wishlist/count', {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.count !== undefined) {
                            wishlistBadge.textContent = data.count;
                        }
                    })
                    .catch(error => {
                        console.error('Error loading wishlist count:', error);
                    });
            }
        }

        // Load wishlist count when page loads
        loadWishlistCount();

        // Lazy loading for images
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src || img.src;
                        img.classList.remove('lazyload');
                        observer.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    });
</script>
@endpush