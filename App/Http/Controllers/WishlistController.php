<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\ApiCartService;

class WishlistController extends Controller
{
    protected $apiCartService;

    public function __construct(ApiCartService $apiCartService)
    {
        $this->apiCartService = $apiCartService;
    }
    // Lấy số WishlistMabaogia từ API ngoài, trả về cho JS
    public function getWishlistCookie()
    {
        $response = Http::withOptions(['verify' => false])->get('https://demodienmay.125.atoz.vn/ww1/cookie.mabaogia.asp');
        $json = $response->json();
        $wishlist = null;
        if (is_array($json)) {
            foreach ($json as $item) {
                if (isset($item['WishlistMabaogia'])) {
                    $wishlist = $item['WishlistMabaogia'];
                    break;
                }
            }
        }
        return response()->json(['WishlistMabaogia' => $wishlist]);
    }
    public function index(Request $request)
    {
        $cookie = $request->cookie('WishlistMabaogia');
        $response = Http::withOptions(['verify' => false])->withHeaders([
            'Cache-Control' => 'no-cache',
        ])->withCookies([
            'WishlistMabaogia' => $cookie,
        ], 'demodienmay.125.atoz.vn')->get('https://demodienmay.125.atoz.vn/ww1/wishlisthientai.asp?ts=' . time());

        $json = $response->json();
        $wishlist = [];
        if (!empty($json['items'])) {
            foreach ($json['items'] as $item) {
                $image = $item['image'] ?? '';
                if ($image && !str_starts_with($image, 'http')) {
                    $image = 'https://demodienmay.125.atoz.vn' . $image;
                }
                $wishlist[$item['id']] = [
                    'tieude' => $item['partName'],
                    'hinhdaidien' => $image,
                    'gia' => $item['price'],
                ];
            }
        }

        return view('wishlist.index', [
            'wishlist' => $wishlist,
            'title' => 'Danh sách yêu thích'
        ]);
    }

    public function add(Request $request, $id)
    {
        $userid = $request->user() ? $request->user()->id : null;
        $pass = $request->user() ? $request->user()->password : null;
        $cookie = $request->input('wishlistCookie') ?: $request->cookie('WishlistMabaogia');

        if ($userid && $pass) {
            $apiUrl = "https://demodienmay.125.atoz.vn/ww1/save.wishlist.asp?userid=$userid&pass=$pass&id=$id";
        } else {
            $apiUrl = "https://demodienmay.125.atoz.vn/ww1/addwishlist.asp?IDPart=$id&id=$cookie";
        }

        $response = Http::withOptions(['verify' => false])->get($apiUrl);
        $json = $response->json();
        $thongbao = isset($json['thongbao']) ? strip_tags($json['thongbao']) : 'Đã thêm vào yêu thích!';

        // Nếu là request AJAX thì trả về JSON, không redirect
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $thongbao,
                'productId' => $id
            ]);
        }

        return redirect()->back()->with('success', $thongbao);
    }

    public function remove(Request $request, $id)
    {
        $cookie = $request->input('wishlistCookie') ?: $request->cookie('WishlistMabaogia');
        $apiUrl = "https://demodienmay.125.atoz.vn/cart/xoawl.asp?IDPart=$id&id=$cookie";

        Log::info("Wishlist remove - API call", [
            'productId' => $id,
            'cookie' => $cookie,
            'apiUrl' => $apiUrl
        ]);

        $response = Http::withOptions(['verify' => false])->get($apiUrl);

        Log::info("Wishlist remove - API response", [
            'status' => $response->status(),
            'body' => $response->body()
        ]);

        // API trả về JavaScript object: var info = {sl: 2, ...}
        $responseBody = $response->body();
        $thongbao = 'Đã xoá khỏi yêu thích!';
        $success = false;
        $successByMessage = false;

        // Parse JavaScript object để lấy thongbao
        $pattern = '/var info = \{([^}]*)\};/';
        if (preg_match($pattern, $responseBody, $matches)) {
            $jsContent = $matches[1];
            $thongbaoPattern = '/thongbao:\s*[\'"]([^\'"]*)[\'"]*/';
            if (preg_match($thongbaoPattern, $jsContent, $thongbaoMatch)) {
                $thongbao = strip_tags($thongbaoMatch[1]);
            }
        }

        // Xác định thành công theo thông điệp
        $tbLower = mb_strtolower($thongbao, 'UTF-8');
        if (strpos($tbLower, 'xóa') !== false || strpos($tbLower, 'xoá') !== false || strpos($tbLower, 'thành công') !== false) {
            $successByMessage = true;
        }

        // Kiểm tra lại bằng cách gọi danh sách wishlist và xác nhận item không còn
        try {
            $listRes = Http::withOptions(['verify' => false])
                ->withHeaders(['Cache-Control' => 'no-cache'])
                ->withCookies([
                    'WishlistMabaogia' => $cookie,
                ], 'demodienmay.125.atoz.vn')
                ->get('https://demodienmay.125.atoz.vn/ww1/wishlisthientai.asp?ts=' . time());

            $listJson = $listRes->json();
            $stillExists = false;
            if (!empty($listJson['items'])) {
                foreach ($listJson['items'] as $item) {
                    if ((string)($item['id'] ?? '') === (string)$id) {
                        $stillExists = true;
                        break;
                    }
                }
            }
            $success = $successByMessage || !$stillExists;
        } catch (\Throwable $e) {
            Log::warning('Wishlist remove - verify failed', ['error' => $e->getMessage()]);
            // fallback: dùng kết quả theo thông điệp
            $success = $successByMessage;
        }

        Log::info("Wishlist remove - Parsed result", [
            'success' => $success,
            'thongbao' => $thongbao
        ]);

        // Nếu là request AJAX thì trả về JSON, không redirect
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => $success,
                'message' => $thongbao,
                'productId' => $id
            ]);
        }

        return redirect()->route('wishlist.index')->with('success', $thongbao);
    }
    // Lấy số lượng wishlist (API nội bộ cho JS)
    public function getCount(Request $request)
    {
        $cookie = $request->cookie('WishlistMabaogia');
        $response = Http::withCookies([
            'WishlistMabaogia' => $cookie,
        ], 'demodienmay.125.atoz.vn')->get('https://demodienmay.125.atoz.vn/ww1/wishlisthientai.asp');
        $json = $response->json();
        $count = 0;
        if (!empty($json['items'])) {
            $count = count($json['items']);
        }
        return response()->json(['count' => $count]);
    }

    // ========== API WISHLIST METHODS THEO YÊU CẦU ==========

    /**
     * Lấy wishlist hiện tại qua API
     */
    public function getCurrentWishlist(Request $request)
    {
        $wishlistCookie = $request->cookie('WishlistMabaogia');
        $wishlist = $this->apiCartService->getCurrentWishlist($wishlistCookie);
        
        return response()->json($wishlist);
    }

    /**
     * Thêm sản phẩm vào wishlist qua API (tương tự như cart)
     */
    public function apiAddToWishlist(Request $request)
    {
        $productId = $request->input('productId') ?: $request->input('IDPart');
        $wishlistCookie = $request->input('wishlistCookie') ?: $request->input('id') ?: $request->cookie('WishlistMabaogia');
        
        $userInfo = $this->apiCartService->getCurrentUserInfo();
        
        if ($userInfo['authenticated']) {
            $apiUrl = "https://demodienmay.125.atoz.vn/ww1/save.wishlist.asp?userid={$userInfo['userid']}&pass={$userInfo['pass']}&id={$productId}";
        } else {
            $apiUrl = "https://demodienmay.125.atoz.vn/ww1/addwishlist.asp?IDPart={$productId}&id={$wishlistCookie}";
        }

        $response = Http::withOptions(['verify' => false])->get($apiUrl);
        $json = $response->json();
        
        $result = [
            'success' => true,
            'message' => isset($json['thongbao']) ? strip_tags($json['thongbao']) : 'Đã thêm vào yêu thích!'
        ];

        return response()->json($result);
    }

    /**
     * Xóa sản phẩm khỏi wishlist qua API
     */
    public function apiRemoveFromWishlist(Request $request)
    {
        $productId = $request->input('productId') ?: $request->input('IDPart');
        $wishlistCookie = $request->input('wishlistCookie') ?: $request->input('id') ?: $request->cookie('WishlistMabaogia');
        
        $apiUrl = "https://demodienmay.125.atoz.vn/cart/xoawl.asp?IDPart={$productId}&id={$wishlistCookie}";
        
        $response = Http::withOptions(['verify' => false])->get($apiUrl);
        $responseBody = $response->body();
        
        $result = ['success' => true, 'message' => 'Đã xóa khỏi yêu thích'];

        // Parse JavaScript object để lấy thongbao
        $pattern = '/var info = \{([^}]*)\};/';
        if (preg_match($pattern, $responseBody, $matches)) {
            $jsContent = $matches[1];
            $thongbaoPattern = '/thongbao:\s*[\'"]([^\'"]*)[\'"]*/';
            if (preg_match($thongbaoPattern, $jsContent, $thongbaoMatch)) {
                $result['message'] = strip_tags($thongbaoMatch[1]);
            }
        }

        return response()->json($result);
    }
}
