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

    <div class="card" style="border-color: #ff6b6b; background-color: #fff5f5;">
        <div class="card-header" style="background-color: #ff6b6b; color: white;">
            ⚠️ Lưu ý quan trọng về lỗi "Dữ liệu không tồn tại"
        </div>
        <div class="card-body">
            <p><strong>Nếu bạn gặp lỗi:</strong> <code>{"thongbao": "Dữ liệu không tồn tại", "maloi": "1", "loi": "1234"}</code></p>
            <p><strong>Nguyên nhân phổ biến:</strong></p>
            <ul>
                <li><strong>ID sản phẩm không đúng:</strong> 60009 có thể không tồn tại, cần dùng slug như "singpc-aio-m24ei5128m5-w-..."</li>
                <li><strong>Cookie chưa được set:</strong> Phải lấy cookie trước khi thao tác</li>
                <li><strong>Sản phẩm chưa được thêm:</strong> Chỉ có thể xóa/update sản phẩm đã có trong giỏ</li>
                <li><strong>Format ID không đúng:</strong> API có thể cần slug thay vì numeric ID</li>
            </ul>
            <p><strong>Hướng dẫn thao tác đúng:</strong></p>
            <ol>
                <li><strong>Lấy cookie trước:</strong> Click "Lấy Cookie" → chờ response thành công</li>
                <li><strong>Chọn sản phẩm có sẵn:</strong> Dùng dropdown để chọn ID thực từ hệ thống</li>
                <li><strong>Thêm vào giỏ trước:</strong> Thêm sản phẩm trước khi xóa/update</li>
                <li><strong>Kiểm tra giỏ hàng:</strong> Xem "Giỏ hàng hiện tại" để confirm</li>
                <li><strong>Debug khi cần:</strong> Dùng "Test API trực tiếp" để troubleshoot</li>
            </ol>
        </div>
    </div>

    <div class="info-section">
        <h5>Thông tin cookie hiện tại:</h5>
        <p><strong>DathangMabaogia:</strong> <span id="cart-cookie">Chưa có</span></p>
        <p><strong>WishlistMabaogia:</strong> <span id="wishlist-cookie">Chưa có</span></p>
        <p><strong>Trạng thái đăng nhập:</strong> <span id="auth-status">{{ Auth::check() ? 'Đã đăng nhập' : 'Chưa đăng nhập' }}</span></p>
        <button class="btn btn-primary" onclick="testDirectAPI()">Test API trực tiếp</button>
        <button class="btn btn-primary" onclick="clearCookies()">Xóa Cookies</button>
        <button class="btn btn-warning" onclick="debugCookies()">🔍 Debug Cookies</button>
    </div>

    <!-- Lấy Cookie -->
    <div class="card">
        <div class="card-header">
            1. Lấy Cookie (365 ngày) - /ww1/cookie.mabaogia
        </div>
        <div class="card-body">
            <p>Lần đầu truy cập để lấy cookie cho DathangMabaogia đặt hàng hoặc WishlistMabaogia yêu thích</p>
            <button class="btn btn-primary" onclick="getCookies()">Lấy Cookie (Server Set)</button>
            <button class="btn btn-success" onclick="getCookiesManual()">🔧 Lấy Cookie (Manual Set)</button>
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
            
            <div class="row">
                <div class="col-md-6">
                    <label>Mã sản phẩm:</label>
                    <input type="text" class="form-control" id="add-product-id" placeholder="Ví dụ: singpc-aio-m24ei5128m5-w-i5-124008gb512gb238-inch-full-hdban-phimchuotwin11pro" value="">
                </div>
                <div class="col-md-6">
                    <label>Hoặc chọn từ danh sách:</label>
                    <select class="form-control" id="product-select" onchange="updateProductId()">
                        <option value="">-- Chọn sản phẩm --</option>
                        <option value="singpc-aio-m24ei5128m5-w-i5-124008gb512gb238-inch-full-hdban-phimchuotwin11pro">SingPC AIO M24Ei5128M5-W</option>
                        <option value="60001">ID: 60001</option>
                        <option value="60002">ID: 60002</option>
                        <option value="60003">ID: 60003</option>
                    </select>
                </div>
            </div>
            <button class="btn btn-success" onclick="addToCart()">Thêm vào giỏ hàng</button>
            <button class="btn btn-info" onclick="copyAddedProductId()" id="copy-product-btn" style="display:none;">
                📋 Copy ID để xóa/update
            </button>
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
            
            <input type="text" class="form-control" id="remove-product-id" placeholder="Mã sản phẩm" value="">
            <small class="text-muted">Sử dụng cùng ID như đã thêm vào giỏ hàng</small>
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
            
            <input type="text" class="form-control" id="update-product-id" placeholder="Mã sản phẩm" value="">
            <input type="number" class="form-control" id="update-quantity" placeholder="Số lượng mới" value="2" min="1">
            <small class="text-muted">Sử dụng cùng ID như đã thêm vào giỏ hàng</small>
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
            <input type="text" class="form-control" id="wishlist-product-id" placeholder="Mã sản phẩm" value="">
            <button class="btn btn-success" onclick="addToWishlist()">Thêm vào yêu thích</button>
            <button class="btn btn-danger" onclick="removeFromWishlist()">Xóa khỏi yêu thích</button>
            <div class="result-area" id="wishlist-result"></div>
        </div>
    </div>
</div>

<script>
// Lấy cookies từ browser với debug
function getCookieValue(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    
    // Debug: log all cookies
    console.log('All cookies:', document.cookie);
    console.log(`Looking for cookie: ${name}`);
    
    if (parts.length === 2) {
        const cookieValue = parts.pop().split(';').shift();
        console.log(`Found cookie ${name}:`, cookieValue);
        return cookieValue;
    }
    
    console.log(`Cookie ${name} not found`);
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
        console.log('Calling cookie API...');
        
        const response = await fetch('/ww1/cookie.mabaogia', {
            method: 'GET',
            credentials: 'include', // Important: include cookies in request
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        
        console.log('Cookie API response status:', response.status);
        console.log('Cookie API response headers:', response.headers);
        
        const data = await response.json();
        console.log('Cookie API response data:', data);
        
        document.getElementById('cookie-result').textContent = JSON.stringify(data, null, 2);
        
        // Force manual cookie setting if server doesn't set them properly
        if (Array.isArray(data)) {
            data.forEach(item => {
                if (item.DathangMabaogia) {
                    document.cookie = `DathangMabaogia=${item.DathangMabaogia}; max-age=${365*24*60*60}; path=/`;
                    console.log('Manually set DathangMabaogia cookie:', item.DathangMabaogia);
                }
                if (item.WishlistMabaogia) {
                    document.cookie = `WishlistMabaogia=${item.WishlistMabaogia}; max-age=${365*24*60*60}; path=/`;
                    console.log('Manually set WishlistMabaogia cookie:', item.WishlistMabaogia);
                }
            });
        }
        
        // Cập nhật hiển thị cookie sau khi lấy
        setTimeout(() => {
            updateCookieDisplay();
            console.log('Updated cookie display');
        }, 500);
        
    } catch (error) {
        console.error('Cookie API error:', error);
        document.getElementById('cookie-result').textContent = 'Lỗi: ' + error.message;
    }
}

// 1b. Lấy cookie và set manual (fallback method)
async function getCookiesManual() {
    try {
        console.log('Calling cookie API for manual setting...');
        
        const response = await fetch('/ww1/cookie.mabaogia', {
            method: 'GET',
            credentials: 'include'
        });
        
        const data = await response.json();
        console.log('Cookie API response data:', data);
        
        document.getElementById('cookie-result').textContent = JSON.stringify(data, null, 2);
        
        // Always set cookies manually from response data
        if (Array.isArray(data)) {
            data.forEach(item => {
                if (item.DathangMabaogia) {
                    const cookieValue = `DathangMabaogia=${item.DathangMabaogia}; max-age=${365*24*60*60}; path=/; SameSite=Lax`;
                    document.cookie = cookieValue;
                    console.log('Set DathangMabaogia cookie:', cookieValue);
                }
                if (item.WishlistMabaogia) {
                    const cookieValue = `WishlistMabaogia=${item.WishlistMabaogia}; max-age=${365*24*60*60}; path=/; SameSite=Lax`;
                    document.cookie = cookieValue;
                    console.log('Set WishlistMabaogia cookie:', cookieValue);
                }
            });
            
            // Force update display
            setTimeout(() => {
                updateCookieDisplay();
                console.log('Force updated cookie display');
                
                // Show success message
                document.getElementById('cookie-result').textContent = 
                    JSON.stringify(data, null, 2) + '\n\n✅ Cookies đã được set thành công!';
            }, 200);
        }
        
    } catch (error) {
        console.error('Manual cookie API error:', error);
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

// Global variable để store added product ID
let lastAddedProductId = null;

// Copy added product ID to other fields
function copyAddedProductId() {
    if (lastAddedProductId) {
        document.getElementById('remove-product-id').value = lastAddedProductId;
        document.getElementById('update-product-id').value = lastAddedProductId;
        document.getElementById('wishlist-product-id').value = lastAddedProductId;
        alert(`Đã copy ID "${lastAddedProductId}" vào các field khác`);
    }
}

// 4. Thêm vào giỏ hàng
async function addToCart() {
    try {
        const productId = document.getElementById('add-product-id').value;
        
        if (!productId) {
            document.getElementById('add-cart-result').textContent = 'Vui lòng nhập mã sản phẩm';
            return;
        }
        
        const cartCookie = getCookieValue('DathangMabaogia');
        
        if (!cartCookie) {
            document.getElementById('add-cart-result').textContent = 'Vui lòng lấy cookie trước khi thao tác';
            return;
        }
        
        @if(Auth::check())
            // Đã đăng nhập
            const response = await fetch(`/ww1/save.addtocart?userid={{ Auth::user()->name ?? Auth::user()->email }}&pass={{ session('plain_pass', '') }}&id=${productId}`);
        @else
            // Chưa đăng nhập
            const response = await fetch(`/ww1/addgiohang?IDPart=${productId}&id=${cartCookie}`);
        @endif
        
        const data = await response.json();
        document.getElementById('add-cart-result').textContent = JSON.stringify(data, null, 2);
        
        // If successful, store product ID and show copy button
        if (data && (!data.maloi || data.maloi != "1")) {
            lastAddedProductId = productId;
            document.getElementById('copy-product-btn').style.display = 'inline-block';
        }
    } catch (error) {
        document.getElementById('add-cart-result').textContent = 'Lỗi: ' + error.message;
    }
}

// 5. Xóa khỏi giỏ hàng
async function removeFromCart() {
    try {
        const productId = document.getElementById('remove-product-id').value;
        
        if (!productId) {
            document.getElementById('remove-cart-result').textContent = 'Vui lòng nhập mã sản phẩm';
            return;
        }
        
        const cartCookie = getCookieValue('DathangMabaogia');
        
        if (!cartCookie) {
            document.getElementById('remove-cart-result').textContent = 'Vui lòng lấy cookie trước khi thao tác';
            return;
        }
        
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
        
        if (!productId) {
            document.getElementById('update-cart-result').textContent = 'Vui lòng nhập mã sản phẩm';
            return;
        }
        
        if (!quantity || quantity < 1) {
            document.getElementById('update-cart-result').textContent = 'Vui lòng nhập số lượng hợp lệ (>= 1)';
            return;
        }
        
        const cartCookie = getCookieValue('DathangMabaogia');
        
        if (!cartCookie) {
            document.getElementById('update-cart-result').textContent = 'Vui lòng lấy cookie trước khi thao tác';
            return;
        }
        
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

// Test API trực tiếp
async function testDirectAPI() {
    try {
        const response = await fetch('/debug/test-api');
        const data = await response.json();
        console.log('Direct API test results:', data);
        
        // Hiển thị kết quả trong popup hoặc console
        alert('Kết quả test API (xem console): ' + JSON.stringify(data, null, 2));
    } catch (error) {
        console.error('Error testing direct API:', error);
        alert('Lỗi khi test API: ' + error.message);
    }
}

// Xóa cookies
function clearCookies() {
    document.cookie = 'DathangMabaogia=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
    document.cookie = 'WishlistMabaogia=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
    updateCookieDisplay();
    alert('Đã xóa cookies');
}

// Debug cookies
function debugCookies() {
    console.log('=== COOKIE DEBUG ===');
    console.log('All document.cookie:', document.cookie);
    console.log('DathangMabaogia:', getCookieValue('DathangMabaogia'));
    console.log('WishlistMabaogia:', getCookieValue('WishlistMabaogia'));
    console.log('Current domain:', window.location.hostname);
    console.log('Current path:', window.location.pathname);
    console.log('===================');
    
    alert(`Cookie debug info logged to console.\n\nAll cookies: ${document.cookie}\n\nDathangMabaogia: ${getCookieValue('DathangMabaogia')}\nWishlistMabaogia: ${getCookieValue('WishlistMabaogia')}`);
}

// Update product ID từ dropdown
function updateProductId() {
    const select = document.getElementById('product-select');
    const input = document.getElementById('add-product-id');
    if (select.value) {
        input.value = select.value;
    }
}

// Load real products từ API
async function loadRealProducts() {
    try {
        const response = await fetch('/debug/products');
        const data = await response.json();
        
        if (data.products_id_check && data.products_id_check.length > 0) {
            const select = document.getElementById('product-select');
            
            // Clear existing options except first one
            while (select.children.length > 1) {
                select.removeChild(select.lastChild);
            }
            
            // Add real products
            data.products_id_check.forEach(product => {
                if (product.id && product.id !== 'NULL') {
                    const option = document.createElement('option');
                    option.value = product.id;
                    option.textContent = `${product.title.substring(0, 50)}... (ID: ${product.id})`;
                    select.appendChild(option);
                }
            });
        }
    } catch (error) {
        console.error('Error loading real products:', error);
    }
}

// Cập nhật hiển thị cookie khi trang load
document.addEventListener('DOMContentLoaded', function() {
    updateCookieDisplay();
    loadRealProducts(); // Load real products
});
</script>
@endsection