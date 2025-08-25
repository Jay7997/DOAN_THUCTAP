@extends('layouts.app')

@section('title', $title ?? 'Chi tiết sản phẩm')

@section('content')
<div class="container mt-5">
    <div class="row">
        <!-- Chi tiết sản phẩm -->
        <div class="col-lg-8">
            <div class="card">
                <div class="row g-0">
                    <div class="col-md-6">
                        <img src="{{ $product['hinhdaidien'] ?? 'https://via.placeholder.com/400x400?text=No+Image' }}"
                            alt="{{ $product['tieude'] ?? 'Sản phẩm' }}"
                            class="img-fluid rounded-start">
                    </div>
                    <div class="col-md-6">
                        <div class="card-body">
                            <h1 class="card-title">{{ $product['tieude'] ?? 'Tên sản phẩm' }}</h1>

                            <div class="h3 text-danger mb-3">
                                {{ number_format($product['gia'] ?? 0, 0, ',', '.') }}đ
                            </div>

                            <p class="card-text">
                                {{ $product['mota'] ?? 'Mô tả sản phẩm sẽ được hiển thị ở đây...' }}
                            </p>

                            <div class="mb-3">
                                <strong>Mã sản phẩm:</strong> {{ $product['id'] ?? 'N/A' }}<br>
                                <strong>Danh mục:</strong> {{ $product['category'] ?? 'N/A' }}<br>
                                <strong>Ngày đăng:</strong> {{ $product['ngaydang'] ?? 'N/A' }}
                            </div>

                            <div class="d-grid gap-2 d-md-block">
                                <button class="btn btn-primary btn-lg" onclick="addToCart('{{ $product['id'] }}', '{{ addslashes($product['tieude'] ?? '') }}', {{ $product['gia'] ?? 0 }}, '{{ $product['hinhdaidien'] ?? '' }}')">
                                    🛒 Thêm vào giỏ hàng
                                </button>

                                <button class="btn btn-outline-danger btn-lg btn-wishlist" data-product-id="{{ $product['id'] }}">
                                    <i class="bi bi-heart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sản phẩm liên quan -->
        <div class="col-lg-4">
            <h3 class="mb-4">🔄 Sản phẩm liên quan</h3>
            @foreach($relatedProducts as $relatedProduct)
            <div class="card mb-3">
                <div class="row g-0">
                    <div class="col-4">
                        <img src="{{ $relatedProduct['hinhdaidien'] ?? 'https://via.placeholder.com/150x100?text=No+Image' }}"
                            alt="{{ $relatedProduct['tieude'] ?? 'Sản phẩm' }}"
                            class="img-fluid rounded-start">
                    </div>
                    <div class="col-8">
                        <div class="card-body">
                            <h6 class="card-title">
                                <a href="{{ route('products.show', $relatedProduct['id']) }}" class="text-decoration-none">
                                    {{ $relatedProduct['tieude'] ?? 'Tên sản phẩm' }}
                                </a>
                            </h6>
                            <p class="card-text text-danger fw-bold">
                                {{ number_format($relatedProduct['gia'] ?? 0, 0, ',', '.') }}đ
                            </p>
                            <button class="btn btn-sm btn-primary" onclick="addToCart('{{ $relatedProduct['id'] }}', '{{ $relatedProduct['tieude'] }}', {{ $relatedProduct['gia'] ?? 0 }}, '{{ $relatedProduct['hinhdaidien'] ?? '' }}')">
                                Thêm vào giỏ
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Phần thông tin chi tiết -->
    <div class="card mt-5" id="product-details">
        <div class="card-header">
            <h3>📋 Thông tin chi tiết sản phẩm</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Thông tin cơ bản -->
                <div class="col-md-6">
                    <h5 class="text-primary mb-3">ℹ️ Thông tin cơ bản</h5>
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td class="fw-bold" style="width: 40%;">Mã sản phẩm:</td>
                                <td><span class="badge bg-secondary">{{ $product['id'] ?? 'N/A' }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Tên sản phẩm:</td>
                                <td>{{ $product['tieude'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Danh mục:</td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $product['category'] ?? 'Chưa phân loại' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Ngày đăng:</td>
                                <td>{{ $product['ngaydang'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Giá bán:</td>
                                <td class="text-danger fw-bold fs-5">
                                    {{ number_format($product['gia'] ?? 0, 0, ',', '.') }}đ
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Thông tin bổ sung -->
                <div class="col-md-6">
                    <h5 class="text-primary mb-3">🔍 Thông tin bổ sung</h5>
                    <table class="table table-borderless">
                        <tbody>
                            @if(isset($product['thuonghieu']) && is_array($product['thuonghieu']))
                            <tr>
                                <td class="fw-bold" style="width: 40%;">Thương hiệu:</td>
                                <td>
                                    @foreach($product['thuonghieu'] as $brand)
                                    <span class="badge bg-primary me-1">{{ $brand['ten'] ?? '' }}</span>
                                    @endforeach
                                </td>
                            </tr>
                            @endif
                            @if(isset($product['hinhanh']) && is_array($product['hinhanh']) && count($product['hinhanh']) > 0)
                            <tr>
                                <td class="fw-bold">Số hình ảnh:</td>
                                <td><span class="badge bg-success">{{ count($product['hinhanh']) }} hình</span></td>
                            </tr>
                            @endif
                            @if(isset($product['hinhlienquan']) && is_array($product['hinhlienquan']) && count($product['hinhlienquan']) > 0)
                            <tr>
                                <td class="fw-bold">Hình liên quan:</td>
                                <td><span class="badge bg-warning">{{ count($product['hinhlienquan']) }} hình</span></td>
                            </tr>
                            @endif
                            @if(isset($product['rating']))
                            <tr>
                                <td class="fw-bold">Đánh giá:</td>
                                <td>
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star{{ $i <= $product['rating'] ? '-fill text-warning' : '' }}"></i>
                                        @endfor
                                        <span class="ms-2">({{ $product['rating'] }}/5)</span>
                                </td>
                            </tr>
                            @endif
                            @if(isset($product['soluongban']))
                            <tr>
                                <td class="fw-bold">Đã bán:</td>
                                <td><span class="badge bg-info">{{ $product['soluongban'] }} sản phẩm</span></td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Thông tin bổ sung -->
            <div class="row mt-4">
                <div class="col-12">
                    <h5 class="text-primary mb-3">🔍 Thông tin bổ sung</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="bi bi-truck fs-1 text-success mb-2"></i>
                                    <h6>Giao hàng miễn phí</h6>
                                    <small class="text-muted">Trong phạm vi 10km</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="bi bi-shield-check fs-1 text-primary mb-2"></i>
                                    <h6>Bảo hành chính hãng</h6>
                                    <small class="text-muted">12-24 tháng</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="bi bi-arrow-repeat fs-1 text-warning mb-2"></i>
                                    <h6>Đổi trả 30 ngày</h6>
                                    <small class="text-muted">Nếu có lỗi từ nhà sản xuất</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mô tả chi tiết -->
            @if(isset($product['mota']) && !empty($product['mota']))
            <div class="row mt-4">
                <div class="col-12">
                    <h5 class="text-primary mb-3">📝 Mô tả chi tiết</h5>
                    <div class="bg-light p-3 rounded">
                        {!! nl2br(e($product['mota'])) !!}
                    </div>
                </div>
            </div>
            @endif

            <!-- Thông tin khuyến mãi -->
            @if(isset($product['khuyenmai']) && !empty($product['khuyenmai']))
            <div class="row mt-4">
                <div class="col-12">
                    <h5 class="text-primary mb-3">🎁 Khuyến mãi</h5>
                    <div class="alert alert-success">
                        <i class="bi bi-gift-fill me-2"></i>
                        {{ $product['khuyenmai'] }}
                    </div>
                </div>
            </div>
            @endif

            <!-- Nội dung chi tiết -->
                @if(isset($product['noidungchitiet']) && !empty($product['noidungchitiet']))
<div class="row mt-4">
    <div class="col-12">
        <h5 class="text-primary mb-3">📄 Nội dung chi tiết</h5>
        <div class="bg-light p-3 rounded">
            {!! $product['noidungchitiet'] !!}
        </div>
    </div>
</div>
@endif

            <!-- Hình ảnh liên quan -->
            @if(isset($product['hinhlienquan']) && is_array($product['hinhlienquan']) && count($product['hinhlienquan']) > 0)
            <div class="row mt-4">
                <div class="col-12">
                    <h5 class="text-primary mb-3">🖼️ Hình ảnh liên quan</h5>
                    <div class="row">
                        @foreach($product['hinhlienquan'] as $image)
                        <div class="col-md-3 col-sm-4 col-6 mb-3">
                            <img src="{{ $image['hinhdaidien'] ?? 'https://via.placeholder.com/200x150?text=No+Image' }}"
                                alt="Hình ảnh sản phẩm"
                                class="img-fluid rounded shadow-sm"
                                style="width: 100%; height: 150px; object-fit: cover;">
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Phần bình luận -->
    <div class="card mt-5" id="comments-section">
        <div class="card-header">
            <h3>💬 Bình luận sản phẩm</h3>
        </div>
        <div class="card-body">
    <form id="comment-form" class="mb-4">
        <div class="mb-3">
            <label for="comment-name" class="form-label">Tên của bạn</label>
            <input type="text" class="form-control" id="comment-name" required>
        </div>
        <div class="mb-3">
            <label for="comment-rating" class="form-label">Đánh giá</label>
            <select class="form-control" id="comment-rating" required>
                <option value="" disabled selected>Chọn đánh giá</option>
                <option value="5">5 sao</option>
                <option value="4">4 sao</option>
                <option value="3">3 sao</option>
                <option value="2">2 sao</option>
                <option value="1">1 sao</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="comment-content" class="form-label">Nội dung bình luận</label>
            <textarea class="form-control" id="comment-content" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Gửi bình luận</button>
    </form>
    <div id="comments-list" class="mt-4"></div>
</div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var productId = parseInt('{{ $product["id"] ?? "0" }}') || 0;
        var commentsData = [];

        // Luôn hiển thị phần bình luận và tải bình luận
        document.getElementById('comments-section').style.display = 'block';
        loadComments();

        // Hàm tải bình luận từ API (được cải tiến để phù hợp nhiều định dạng phản hồi)
        function loadComments(page = 1) {
            fetch(`/api/proxy-binhluan/${productId}/${page}`)
                .then(response => response.json())
                .then(data => {
                    /*
                     * API có thể trả về:
                     *  1. { data: [...] }
                     *  2. [{ data: [...] }]
                     *  3. { comments: [...] }
                     *  4. [...] (mảng các comment trực tiếp)
                     * Đoạn mã dưới đây sẽ cố gắng chuẩn hoá về mảng comment.
                     */
                    if (Array.isArray(data)) {
                        // Kiểu [{ data: [...] }] hoặc trực tiếp [...]
                        if (data.length > 0 && Array.isArray(data[0].data)) {
                            commentsData = data[0].data;
                        } else {
                            commentsData = data; // Giả định mảng comment trực tiếp
                        }
                    } else if (data && Array.isArray(data.data)) {
                        commentsData = data.data; // Kiểu { data: [...] }
                    } else if (data && Array.isArray(data.comments)) {
                        commentsData = data.comments; // Kiểu { comments: [...] }
                    } else {
                        commentsData = [];
                    }
                    updateCommentsCount();
                    renderComments();
                })
                .catch(error => {
                    console.error('Lỗi khi tải bình luận:', error);
                    commentsData = [];
                    updateCommentsCount();
                    renderComments();
                });
        }

        // Hàm cập nhật số lượng bình luận (hiện có thể tuỳ biến hiển thị)
        function updateCommentsCount() {
            // Ví dụ: document.getElementById('comments-count').innerText = commentsData.length;
        }

        // Hàm hỗ trợ lấy giá trị theo key không phân biệt hoa/thường
        function getVal(obj, possibleKeys, defaultVal = '') {
            for (var i = 0; i < possibleKeys.length; i++) {
                var kLower = possibleKeys[i].toLowerCase();
                for (var key in obj) {
                    if (obj.hasOwnProperty(key) && key.toLowerCase() === kLower) {
                        var val = obj[key];
                        if (val !== undefined && val !== null && String(val).trim() !== '') {
                            return val;
                        }
                    }
                }
            }
            return defaultVal;
        }

        // Hàm hiển thị bình luận (cải tiến hiển thị đầy đủ thông tin)
        function renderComments() {
            var commentsList = document.getElementById('comments-list');
            var html = '';

            if (!commentsData || commentsData.length === 0) {
                commentsList.innerHTML = '<p class="text-muted">Chưa có bình luận nào.</p>';
                return;
            }

            commentsData.forEach(function(comment) {
                var name = getVal(comment, ['nguoidang','tennguoidang','name','nguoidung','hoten'] ,'Ẩn danh');
                var rawRatingStr = getVal(comment, ['rating','sosao','danhgia'], 0);
                var rawRating = parseInt(rawRatingStr, 10) || 0;
                var rating = rawRating > 5 ? rawRating / 20 : rawRating;
                var content = getVal(comment, ['noidungbinhluan','noidung','comment','ndbl','noiDung'], '');

                var stars = '';
                for (var i = 1; i <= 5; i++) {
                    stars += i <= rating ? '⭐' : '☆';
                }

                html += `
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong>${name}</strong>
                            <small class="text-muted">${comment.ngaydang ? new Date(comment.ngaydang).toLocaleDateString('vi-VN') : ''}</small>
                        </div>
                        <div class="mb-2">${stars}</div>
                        <div>${content}</div>
                    </div>
                `;
            });

            commentsList.innerHTML = html;
        }

        // Xử lý form gửi bình luận (nếu có API thì cần sửa lại, còn không thì giữ nguyên)
        document.getElementById('comment-form').addEventListener('submit', function(e) {
    e.preventDefault();

    var name = document.getElementById('comment-name').value;
    var ratingValue = document.getElementById('comment-rating').value;
    var rating = parseInt(ratingValue, 10);
    if (rating <= 5) {
        rating = rating * 20; // Chuẩn hoá về thang 100 (20,40,60,80,100)
    }
    var content = document.getElementById('comment-content').value;

    fetch(`/api/proxy-binhluan/${productId}/add`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            nguoidang: name,
            rating: rating,
            noidungbinhluan: content
        })
    })
    .then(res => res.json())
    .then(data => {
        alert('Bình luận đã được gửi!');
        loadComments(); // Reload comments
        document.getElementById('comment-form').reset();
    })
    .catch(err => {
        alert('Có lỗi khi gửi bình luận!');
    });
});
    });


    // Hàm thêm vào giỏ hàng qua proxy
    function addToCart(productId) {
        if (window.CartAPI && typeof window.CartAPI.add === 'function') {
            window.CartAPI.add(productId);
        } else {
            // Fallback tới route cũ nếu cần
            $.ajax({
                url: '/cart/add',
                method: 'POST',
                data: {
                    product_id: productId,
                    quantity: 1,
                    _token: $('meta[name="csrf-token"]').attr('content')
                }
            });
        }
    }

    // Wishlist toggle for product detail page
    (function() {
        const wishlistBtn = document.querySelector('.btn-wishlist');
        if (!wishlistBtn) return;

        wishlistBtn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const isAdding = !this.classList.contains('active');

            fetch(`/wishlist/${isAdding ? 'add' : 'remove'}/${productId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data && data.success !== false) {
                    this.classList.toggle('active');
                    if (this.classList.contains('active')) {
                        this.innerHTML = '<i class="bi bi-heart-fill"></i>';
                        this.classList.remove('btn-outline-danger');
                        this.classList.add('btn-danger');
                    } else {
                        this.innerHTML = '<i class="bi bi-heart"></i> Yêu thích';
                        this.classList.remove('btn-danger');
                        this.classList.add('btn-outline-danger');
                    }

                    const wishlistBadge = document.getElementById('wishlist-count');
                    if (wishlistBadge) {
                        fetch('/wishlist/count', {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                        })
                        .then(r => r.json())
                        .then(countData => {
                            if (countData && typeof countData.count !== 'undefined') {
                                wishlistBadge.textContent = countData.count;
                            }
                        })
                        .catch(() => {});
                    }
                } else {
                    alert(`Có lỗi xảy ra khi ${isAdding ? 'thêm' : 'xóa'} sản phẩm khỏi yêu thích`);
                }
            })
            .catch(() => {
                alert(`Có lỗi xảy ra khi ${isAdding ? 'thêm' : 'xóa'} sản phẩm khỏi yêu thích`);
            });
        });
    })();
</script>
@endsection