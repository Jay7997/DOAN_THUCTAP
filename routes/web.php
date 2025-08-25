<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\OrderHistoryController;
use App\Http\Controllers\OrderCancelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

// Trang chủ và sản phẩm
Route::get('/', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
Route::get('/search', [ProductController::class, 'search'])->name('products.search');
Route::get('/products/suggestions', [ProductController::class, 'suggestions'])->name('products.suggestions');
Route::get('/refresh', [ProductController::class, 'refreshCache'])->name('products.refresh');
Route::get('/api/products/{id}', [ProductController::class, 'getProduct'])->name('products.api.get');

// Tin tức
Route::get('/news', [ProductController::class, 'news'])->name('news.index');
Route::get('/news/{id}', [NewsController::class, 'show'])->name('news.show');


// Bộ lọc sản phẩm
Route::get('/loc-san-pham', [FilterController::class, 'showFilter'])->name('products.filter');
Route::get('/filters/results', [FilterController::class, 'results'])->name('filters.results');
Route::get('/filters', [FilterController::class, 'index'])->name('filters.index');
Route::post('/filters/apply', [FilterController::class, 'apply'])->name('filters.apply');

// Giỏ hàng
Route::get('/cart', [CartController::class, 'view'])->name('cart.view');
Route::get('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/add', [CartController::class, 'addPost'])->name('cart.add.post');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

// Thanh toán
Route::get('/payment', [PaymentController::class, 'showPaymentForm'])->name('payment.form');
Route::post('/payment/process', [PaymentController::class, 'processPayment'])->name('payment.process');
Route::get('/payment/success', [PaymentController::class, 'showSuccess'])->name('payment.success');

// Lịch sử đơn hàng
Route::get('/purchase-history', [OrderHistoryController::class, 'index'])->name('cart.history');
Route::post('/orders/cancel', [OrderCancelController::class, 'cancel'])->name('orders.cancel')->middleware('auth');
Route::post('/orders/cancel-session', [OrderCancelController::class, 'cancelSession'])->name('orders.cancel.session');

// Liên hệ
Route::get('/lien-he', [ContactController::class, 'show'])->name('lien-he');
Route::post('/submit-contact', [ContactController::class, 'submitContact'])->name('submit.contact');

// Wishlist - ✅ CHỈ SỬ DỤNG CONTROLLER ROUTES
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::get('/wishlist/add/{id}', [WishlistController::class, 'add'])->name('wishlist.add');
Route::get('/wishlist/remove/{id}', [WishlistController::class, 'remove'])->name('wishlist.remove');
Route::get('/wishlist/get-cookie', [\App\Http\Controllers\WishlistController::class, 'getWishlistCookie']);
// ✅ THÊM ROUTE CHO COUNT
Route::get('/wishlist/count', [WishlistController::class, 'getCount'])->name('wishlist.count');

// Xác thực
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Proxy route để lấy cookie WishlistMabaogia từ backend Laravel, tránh lỗi CORS
Route::get('/api/proxy-cookie', function () {
    $res = \Illuminate\Support\Facades\Http::withOptions(['verify' => false])->get('https://demodienmay.125.atoz.vn/ww1/cookie.mabaogia.asp');
    return response($res->body(), $res->status())
        ->header('Content-Type', 'application/json')
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
});

// Proxy routes for wishlist operations to avoid CORS
Route::post('/api/proxy-wishlist-add', function (\Illuminate\Http\Request $request) {
    $productId = $request->input('productId');
    $wishlistCookie = $request->input('wishlistCookie');
    $userid = $request->input('userid');
    $pass = $request->input('pass');

    if ($userid && $pass) {
        $apiUrl = "https://demodienmay.125.atoz.vn/ww1/save.wishlist.asp?userid={$userid}&pass={$pass}&id={$productId}";
    } else {
        $apiUrl = "https://demodienmay.125.atoz.vn/ww1/addwishlist.asp?IDPart={$productId}&id={$wishlistCookie}";
    }

    $res = \Illuminate\Support\Facades\Http::withOptions(['verify' => false])->get($apiUrl);
    return response($res->body(), $res->status())
        ->header('Content-Type', 'application/json')
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
});

Route::post('/api/proxy-wishlist-remove', function (\Illuminate\Http\Request $request) {
    $productId = $request->input('productId');
    $wishlistCookie = $request->input('wishlistCookie'); // Lấy cookie từ request

    // Log để debug
    Log::info('proxy-wishlist-remove', [
        'productId' => $productId,
        'wishlistCookie' => $wishlistCookie
    ]);

    $apiUrl = "https://demodienmay.125.atoz.vn/cart/xoawl.asp?IDPart={$productId}&id={$wishlistCookie}";

    $res = \Illuminate\Support\Facades\Http::withOptions(['verify' => false])->get($apiUrl);
    $responseBody = $res->body();

    $data = ['thongbao' => 'Đã xóa khỏi wishlist'];

    $pattern = '/var info = \{([^}]*)\};/';
    if (preg_match($pattern, $responseBody, $matches)) {
        $jsContent = $matches[1];

        if (preg_match('/thongbao:\s*[\'"]([^\'"]*)[\'"]/', $jsContent, $thongbaoMatch)) {
            $data['thongbao'] = strip_tags($thongbaoMatch[1]);
        }
        if (preg_match('/sl:\s*(\d+)/', $jsContent, $slMatch)) {
            $data['sl'] = (int)$slMatch[1];
        }
        if (preg_match('/tongtien:\s*[\'"]([^\'"]*)[\'"]/', $jsContent, $tongtienMatch)) {
            $data['tongtien'] = $tongtienMatch[1];
        }
    }

    return response()->json($data)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
});

// Proxy routes for comment system to avoid CORS
Route::get('/api/proxy-product-info/{id}', function ($id) {
    $res = \Illuminate\Support\Facades\Http::withOptions(['verify' => false])
        ->get("https://demochung.125.atoz.vn/ww2/module.sanpham.chitiet.asp?id={$id}");
    return response($res->body(), $res->status())
        ->header('Content-Type', 'application/json')
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
});

Route::get('/api/proxy-comments/{id}', function ($id) {
    $res = \Illuminate\Support\Facades\Http::withOptions(['verify' => false])
        ->get("https://demodienmay.125.atoz.vn/ww2/module.sanpham.chitiet.asp?id={$id}");
    return response($res->body(), $res->status())
        ->header('Content-Type', 'application/json')
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
});

// Route OPTIONS cho tất cả proxy endpoint (nếu cần)
Route::options('/api/{any}', function () {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
})->where('any', '.*');

// Test email (chỉ trong development)
Route::get('/test-email', function () {
    try {
        $orderData = [
            'full_name' => 'Nguyễn Văn A',
            'email' => 'test@example.com',
            'sdt' => '0123456789',
            'address' => '123 Đường ABC, Quận 1, TP.HCM',
            'payment_method' => 'cod',
            'total_amount' => 15000000,
            'order_date' => now(),
        ];

        $cartItems = [
            '60001' => [
                'tieude' => 'Laptop Gaming MSI',
                'gia' => 15000000,
                'quantity' => 1
            ]
        ];

        $totalAmount = 15000000;

        Mail::to('test@example.com')->send(new \App\Mail\OrderConfirmation($orderData, $cartItems, $totalAmount));

        return response()->json([
            'status' => 'success',
            'message' => 'Test email sent successfully! Check storage/logs/laravel.log'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to send test email: ' . $e->getMessage()
        ]);
    }
})->name('test.email');

// Test API đặt hàng (Đã vô hiệu hóa - không còn sử dụng API bên ngoài)
Route::get('/test-order-api', function () {
    return response()->json([
        'status' => 'info',
        'message' => 'Chức năng này đã được vô hiệu hóa. Hệ thống hiện tại xử lý đơn hàng cục bộ.',
        'api_response' => null
    ]);
})->name('test.order.api');

// Trang debug đặt hàng
Route::get('/payment/debug', function () {
    return view('payment.debug');
})->name('payment.debug');




Route::get('/api/proxy-binhluan/{id}/{page?}', function ($id, $page = 1) {
    $apiUrl = "http://demodienmay.125.atoz.vn/ww2/binhluan.pc.asp?id={$id}&txtloai=desc&pageid={$page}";
    $res = \Illuminate\Support\Facades\Http::withOptions(['verify' => false])->get($apiUrl);
    return response($res->body(), $res->status())
        ->header('Content-Type', 'application/json')
        ->header('Access-Control-Allow-Origin', '*');
});
// Route::post('/api/proxy-binhluan/{id}/add', function ($id, \Illuminate\Http\Request $request) {
//     // Lấy dữ liệu từ request (từ form JS của bạn)
//     $nguoidang = $request->input('nguoidang');
//     $rating = $request->input('rating');
//     $noidungbinhluan = $request->input('noidungbinhluan');

//     // Mặc định userid và pass empty nếu không có (như ví dụ của bạn)
//     $userid = $request->input('userid', '');
//     $pass = $request->input('pass', '');

//     // Xây dựng URL giống ví dụ, thêm tham số nội dung (giả định tên param; test và adjust nếu cần)
//     $apiUrl = "https://demodienmay.125.atoz.vn/ww1/save.binhluan.asp?userid={$userid}&pass={$pass}&id={$id}&nguoidang={$nguoidang}&rating={$rating}&noidung={$noidungbinhluan}";

//     // Gọi GET đến API gốc (vì ví dụ của bạn là GET)
//     $res = \Illuminate\Support\Facades\Http::withOptions(['verify' => false])->get($apiUrl);

//     // Trả về response từ API, với headers CORS
//     return response($res->body(), $res->status())
//         ->header('Content-Type', 'application/json')
//         ->header('Access-Control-Allow-Origin', '*')
//         ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
//         ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
// });



Route::post('/api/proxy-binhluan/{id}/add', function ($id, \Illuminate\Http\Request $request) {
    $apiUrl = "https://demodienmay.125.atoz.vn/ww1/save.binhluan.asp"; // đổi sang endpoint gốc ww1

    // Dữ liệu người dùng gửi lên
    $nameInput = $request->input('nguoidang', '');
    $contentInput = $request->input('noidungbinhluan', '');
    $ratingInput = (int)$request->input('rating', 5); // có thể 1–5 hoặc 20–100

    // Chuẩn hóa rating về thang 1–5 cho id2
    if ($ratingInput > 5) {
        $ratingInput = max(1, min(5, intval($ratingInput / 20)));
    }

    // Thông tin gửi lên API gốc
    $remoteData = [
        'userid'       => '', // để trống khi chưa đăng nhập
        'pass'         => '',
        'id'           => $id, // id sản phẩm
        'tenkh'        => $nameInput,
        'txtemail'     => $request->input('txtemail', ''),
        'txtdienthoai' => $request->input('txtdienthoai', ''),
        'noidungtxt'   => $contentInput,
        'id2'          => $ratingInput, // số sao 1–5
        'id3'          => '/dist/images/user.jpg', // avatar mặc định
    ];

    // Nếu người dùng đã đăng nhập, thêm userid & pass
    if (\Illuminate\Support\Facades\Auth::check()) {
        $user = \Illuminate\Support\Facades\Auth::user();
        $remoteData['userid'] = $user->name ?? $user->email;
        $remoteData['pass'] = session('plain_pass', '');
    }

    $response = \Illuminate\Support\Facades\Http::withOptions(['verify' => false])
        ->asForm()
        ->post($apiUrl, $remoteData);

    return response($response->body(), $response->status())
        ->header('Content-Type', 'application/json')
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
});
