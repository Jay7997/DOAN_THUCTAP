@extends('layouts.app')

@section('title', 'Demo API Giỏ hàng')

@section('content')
<style>
    .demo-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        font-family: Arial, sans-serif;
        margin-bottom: 100px;
    }

    .demo-container h1 {
        color: #d4a017;
        font-size: 2rem;
        text-align: center;
        margin-bottom: 30px;
    }

    .card {
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
    }

    .card-header {
        background-color: #1a252f;
        color: #d4a017;
        padding: 15px;
        font-weight: bold;
    }

    .card-body {
        padding: 20px;
        background-color: #fff;
    }

    .btn-primary {
        background-color: #d4a017;
        border: none;
        color: #1a252f;
        font-weight: bold;
        margin: 5px;
        padding: 10px 20px;
    }

    .btn-primary:hover {
        background-color: #b58900;
    }

    .btn-success {
        background-color: #3e7c3e;
        border: none;
        color: #fff;
        font-weight: bold;
        margin: 5px;
        padding: 10px 20px;
    }

    .btn-danger {
        background-color: #dc3545;
        border: none;
        color: #fff;
        font-weight: bold;
        margin: 5px;
        padding: 10px 20px;
    }

    .form-control {
        margin: 5px 0;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    .result-area {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 15px;
        margin-top: 15px;
        min-height: 100px;
        white-space: pre-wrap;
        font-family: monospace;
    }

    .info-section {
        background-color: #e7f3ff;
        border: 1px solid #b3d9ff;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 20px;
    }
</style>

<div class="demo-container">
    <h1>Demo API Giỏ hàng theo yêu cầu</h1>

    <div class="info-section">
        <h5>Thông tin cookie hiện tại:</h5>
        <p><strong>DathangMabaogia:</strong> <span id="cart-cookie">Chưa có</span></p>
        <p><strong>WishlistMabaogia:</strong> <span id="wishlist-cookie">Chưa có</span></p>
        <p><strong>Trạng thái đăng nhập:</strong> <span id="auth-status">{{ Auth::check() ? 'Đã đăng nhập' : 'Chưa đăng nhập' }}</span></p>
    </div>

    <!-- Lấy Cookie -->
    <div class="card">
        <div class="card-header">
            1. Lấy Cookie (365 ngày) - /ww1/cookie.mabaogia
        </div>
        <div class="card-body">
            <p>Lần đầu truy cập để lấy cookie cho DathangMabaogia đặt hàng hoặc WishlistMabaogia yêu thích</p>
            <button class="btn btn-primary" onclick="getCookies()">Lấy Cookie</button>
            <div class="result-area" id="cookie-result"></div>
        </div>
    </div>

    <!-- Giỏ hàng hiện tại -->
    <div class="card">
        <div class="card-header">
            2. Giỏ hàng hiện tại - /ww1/giohanghientai
        </div>
        <div class="card-body">
            <button class="btn btn-primary" onclick="getCurrentCart()">Xem giỏ hàng hiện tại</button>
            <div class="result-area" id="current-cart-result"></div>
        </div>
    </div>

    <!-- Wishlist hiện tại -->
    <div class="card">
        <div class="card-header">
            3. Yêu thích hiện tại - /ww1/wishlisthientai
        </div>
        <div class="card-body">
            <button class="btn btn-primary" onclick="getCurrentWishlist()">Xem danh sách yêu thích</button>
            <div class="result-area" id="current-wishlist-result"></div>
        </div>
    </div>

    <!-- Thêm vào giỏ hàng -->
    <div class="card">
        <div class="card-header">
            4. Thêm mặt hàng vào giỏ hàng
        </div>
        <div class="card-body">
            <p><strong>Đã đăng nhập:</strong> /ww1/save.addtocart?userid&pass&id=mã_sản_phẩm</p>
            <p><strong>Chưa đăng nhập:</strong> /ww1/addgiohang?IDPart=mã_sản_phẩm&id=cookie_DathangMabaogia</p>
            
            <input type="text" class="form-control" id="add-product-id" placeholder="Mã sản phẩm (ví dụ: 60001)" value="60001">
            <button class="btn btn-success" onclick="addToCart()">Thêm vào giỏ hàng</button>
            <div class="result-area" id="add-cart-result"></div>
        </div>
    </div>

    <!-- Xóa khỏi giỏ hàng -->
    <div class="card">
        <div class="card-header">
            5. Xóa mặt hàng khỏi giỏ hàng
        </div>
        <div class="card-body">
            <p><strong>Đã đăng nhập:</strong> /ww1/remove.listcart?userid&pass&id=mã_sản_phẩm</p>
            <p><strong>Chưa đăng nhập:</strong> /ww1/removegiohang?IDPart=mã_sản_phẩm&id=cookie_DathangMabaogia</p>
            
            <input type="text" class="form-control" id="remove-product-id" placeholder="Mã sản phẩm" value="60001">
            <button class="btn btn-danger" onclick="removeFromCart()">Xóa khỏi giỏ hàng</button>
            <div class="result-area" id="remove-cart-result"></div>
        </div>
    </div>

    <!-- Cập nhật số lượng -->
    <div class="card">
        <div class="card-header">
            6. Tăng giảm số lượng mặt hàng
        </div>
        <div class="card-body">
            <p><strong>Đã đăng nhập:</strong> /ww1/upcart?userid&pass&id=mã_sản_phẩm&id2=số_lượng_mới</p>
            <p><strong>Chưa đăng nhập:</strong> /ww1/upgiohang?IDPart=mã_sản_phẩm&id=cookie_DathangMabaogia&id1=số_lượng_mới</p>
            
            <input type="text" class="form-control" id="update-product-id" placeholder="Mã sản phẩm" value="60001">
            <input type="number" class="form-control" id="update-quantity" placeholder="Số lượng mới" value="2" min="1">
            <button class="btn btn-primary" onclick="updateQuantity()">Cập nhật số lượng</button>
            <div class="result-area" id="update-cart-result"></div>
        </div>
    </div>

    <!-- Wishlist operations -->
    <div class="card">
        <div class="card-header">
            7. Thao tác với Wishlist
        </div>
        <div class="card-body">
            <input type="text" class="form-control" id="wishlist-product-id" placeholder="Mã sản phẩm" value="60001">
            <button class="btn btn-success" onclick="addToWishlist()">Thêm vào yêu thích</button>
            <button class="btn btn-danger" onclick="removeFromWishlist()">Xóa khỏi yêu thích</button>
            <div class="result-area" id="wishlist-result"></div>
        </div>
    </div>
</div>

<script>
// Lấy cookies từ browser
function getCookieValue(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}

// Cập nhật hiển thị cookie
function updateCookieDisplay() {
    document.getElementById('cart-cookie').textContent = getCookieValue('DathangMabaogia') || 'Chưa có';
    document.getElementById('wishlist-cookie').textContent = getCookieValue('WishlistMabaogia') || 'Chưa có';
}

// 1. Lấy cookie
async function getCookies() {
    try {
        const response = await fetch('/ww1/cookie.mabaogia');
        const data = await response.json();
        document.getElementById('cookie-result').textContent = JSON.stringify(data, null, 2);
        
        // Cập nhật hiển thị cookie sau khi lấy
        setTimeout(updateCookieDisplay, 1000);
    } catch (error) {
        document.getElementById('cookie-result').textContent = 'Lỗi: ' + error.message;
    }
}

// 2. Lấy giỏ hàng hiện tại
async function getCurrentCart() {
    try {
        const response = await fetch('/ww1/giohanghientai');
        const data = await response.json();
        document.getElementById('current-cart-result').textContent = JSON.stringify(data, null, 2);
    } catch (error) {
        document.getElementById('current-cart-result').textContent = 'Lỗi: ' + error.message;
    }
}

// 3. Lấy wishlist hiện tại
async function getCurrentWishlist() {
    try {
        const response = await fetch('/ww1/wishlisthientai');
        const data = await response.json();
        document.getElementById('current-wishlist-result').textContent = JSON.stringify(data, null, 2);
    } catch (error) {
        document.getElementById('current-wishlist-result').textContent = 'Lỗi: ' + error.message;
    }
}

// 4. Thêm vào giỏ hàng
async function addToCart() {
    try {
        const productId = document.getElementById('add-product-id').value;
        const cartCookie = getCookieValue('DathangMabaogia');
        
        @if(Auth::check())
            // Đã đăng nhập
            const response = await fetch(`/ww1/save.addtocart?userid={{ Auth::user()->name ?? Auth::user()->email }}&pass={{ session('plain_pass', '') }}&id=${productId}`);
        @else
            // Chưa đăng nhập
            const response = await fetch(`/ww1/addgiohang?IDPart=${productId}&id=${cartCookie}`);
        @endif
        
        const data = await response.json();
        document.getElementById('add-cart-result').textContent = JSON.stringify(data, null, 2);
    } catch (error) {
        document.getElementById('add-cart-result').textContent = 'Lỗi: ' + error.message;
    }
}

// 5. Xóa khỏi giỏ hàng
async function removeFromCart() {
    try {
        const productId = document.getElementById('remove-product-id').value;
        const cartCookie = getCookieValue('DathangMabaogia');
        
        @if(Auth::check())
            // Đã đăng nhập
            const response = await fetch(`/ww1/remove.listcart?userid={{ Auth::user()->name ?? Auth::user()->email }}&pass={{ session('plain_pass', '') }}&id=${productId}`);
        @else
            // Chưa đăng nhập
            const response = await fetch(`/ww1/removegiohang?IDPart=${productId}&id=${cartCookie}`);
        @endif
        
        const data = await response.json();
        document.getElementById('remove-cart-result').textContent = JSON.stringify(data, null, 2);
    } catch (error) {
        document.getElementById('remove-cart-result').textContent = 'Lỗi: ' + error.message;
    }
}

// 6. Cập nhật số lượng
async function updateQuantity() {
    try {
        const productId = document.getElementById('update-product-id').value;
        const quantity = document.getElementById('update-quantity').value;
        const cartCookie = getCookieValue('DathangMabaogia');
        
        @if(Auth::check())
            // Đã đăng nhập
            const response = await fetch(`/ww1/upcart?userid={{ Auth::user()->name ?? Auth::user()->email }}&pass={{ session('plain_pass', '') }}&id=${productId}&id2=${quantity}`);
        @else
            // Chưa đăng nhập
            const response = await fetch(`/ww1/upgiohang?IDPart=${productId}&id=${cartCookie}&id1=${quantity}`);
        @endif
        
        const data = await response.json();
        document.getElementById('update-cart-result').textContent = JSON.stringify(data, null, 2);
    } catch (error) {
        document.getElementById('update-cart-result').textContent = 'Lỗi: ' + error.message;
    }
}

// 7. Thêm vào wishlist
async function addToWishlist() {
    try {
        const productId = document.getElementById('wishlist-product-id').value;
        const response = await fetch('/api/wishlist/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                productId: productId
            })
        });
        
        const data = await response.json();
        document.getElementById('wishlist-result').textContent = JSON.stringify(data, null, 2);
    } catch (error) {
        document.getElementById('wishlist-result').textContent = 'Lỗi: ' + error.message;
    }
}

// 8. Xóa khỏi wishlist
async function removeFromWishlist() {
    try {
        const productId = document.getElementById('wishlist-product-id').value;
        const response = await fetch('/api/wishlist/remove', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                productId: productId
            })
        });
        
        const data = await response.json();
        document.getElementById('wishlist-result').textContent = JSON.stringify(data, null, 2);
    } catch (error) {
        document.getElementById('wishlist-result').textContent = 'Lỗi: ' + error.message;
    }
}

// Cập nhật hiển thị cookie khi trang load
document.addEventListener('DOMContentLoaded', function() {
    updateCookieDisplay();
});
</script>
@endsection