<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ApiCartService
{
    protected $baseUrl = 'https://demodienmay.125.atoz.vn/ww1';

    /**
     * Lấy cookie cho DathangMabaogia và WishlistMabaogia
     */
    public function getCookies(): array
    {
        try {
            // Thử cả 2 endpoint để tìm endpoint đúng
            $endpoints = [
                'https://demodienmay.125.atoz.vn/ww1/cookie.mabaogia.asp',
                'https://demodienmay.125.atoz.vn/ww1/cookie.mabaogia'
            ];
            
            foreach ($endpoints as $endpoint) {
                Log::info('Trying cookie endpoint', ['url' => $endpoint]);
                
                $response = Http::withOptions(['verify' => false])
                    ->timeout(10)
                    ->get($endpoint);
                
                Log::info('Cookie API response', [
                    'url' => $endpoint,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                $data = $response->json();
                
                if ($data && is_array($data) && !isset($data['maloi'])) {
                    $cookies = [];
                    
                    foreach ($data as $item) {
                        if (isset($item['DathangMabaogia'])) {
                            $cookies['DathangMabaogia'] = $item['DathangMabaogia'];
                        }
                        if (isset($item['WishlistMabaogia'])) {
                            $cookies['WishlistMabaogia'] = $item['WishlistMabaogia'];
                        }
                    }
                    
                    if (!empty($cookies)) {
                        return $cookies;
                    }
                }
            }
            
            return ['error' => 'Không thể lấy cookie từ server'];
        } catch (\Exception $e) {
            Log::error('Error getting cookies', ['error' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Lấy giỏ hàng hiện tại
     */
    public function getCurrentCart(?string $cartCookie = null): array
    {
        try {
            if (!$cartCookie) {
                $cartCookie = request()->cookie('DathangMabaogia');
            }

            if (!$cartCookie) {
                return ['error' => 'Không có cookie DathangMabaogia. Vui lòng lấy cookie trước.'];
            }

            $url = 'https://demodienmay.125.atoz.vn/ww1/giohanghientai.asp';
            
            Log::info('Getting current cart', [
                'url' => $url,
                'cookie' => $cartCookie
            ]);

            $response = Http::withOptions(['verify' => false])
                ->withCookies(['DathangMabaogia' => $cartCookie], 'demodienmay.125.atoz.vn')
                ->timeout(10)
                ->get($url);
            
            Log::info('Current cart response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            $data = $response->json();
            
            if (isset($data['maloi'])) {
                return [
                    'error' => $data['thongbao'] ?? 'Lỗi từ API',
                    'maloi' => $data['maloi'],
                    'details' => $data
                ];
            }
            
            return $data ?: [];
        } catch (\Exception $e) {
            Log::error('Error getting current cart', ['error' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Lấy wishlist hiện tại
     */
    public function getCurrentWishlist(?string $wishlistCookie = null): array
    {
        try {
            if (!$wishlistCookie) {
                $wishlistCookie = request()->cookie('WishlistMabaogia');
            }

            if (!$wishlistCookie) {
                return ['error' => 'Không có cookie WishlistMabaogia. Vui lòng lấy cookie trước.'];
            }

            $url = 'https://demodienmay.125.atoz.vn/ww1/wishlisthientai.asp';
            
            Log::info('Getting current wishlist', [
                'url' => $url,
                'cookie' => $wishlistCookie
            ]);

            $response = Http::withOptions(['verify' => false])
                ->withCookies(['WishlistMabaogia' => $wishlistCookie], 'demodienmay.125.atoz.vn')
                ->timeout(10)
                ->get($url);
            
            Log::info('Current wishlist response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            $data = $response->json();
            
            if (isset($data['maloi'])) {
                return [
                    'error' => $data['thongbao'] ?? 'Lỗi từ API',
                    'maloi' => $data['maloi'],
                    'details' => $data
                ];
            }
            
            return $data ?: [];
        } catch (\Exception $e) {
            Log::error('Error getting current wishlist', ['error' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function addToCart(string $productId, ?string $userid = null, ?string $pass = null, ?string $cartCookie = null): array
    {
        try {
            if ($userid && $pass) {
                // Đã đăng nhập
                $url = "https://demodienmay.125.atoz.vn/ww1/save.addtocart.asp?userid={$userid}&pass={$pass}&id={$productId}";
            } else {
                // Chưa đăng nhập
                if (!$cartCookie) {
                    $cartCookie = request()->cookie('DathangMabaogia');
                }
                
                if (!$cartCookie) {
                    return ['success' => false, 'message' => 'Không có cookie DathangMabaogia. Vui lòng lấy cookie trước.'];
                }
                
                $url = "https://demodienmay.125.atoz.vn/ww1/addgiohang.asp?IDPart={$productId}&id={$cartCookie}";
            }

            Log::info('Adding to cart', [
                'url' => $url,
                'productId' => $productId,
                'hasAuth' => !empty($userid),
                'hasCookie' => !empty($cartCookie)
            ]);

            $response = Http::withOptions(['verify' => false])
                ->timeout(10)
                ->get($url);
            
            Log::info('Add to cart API response', [
                'url' => $url,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return $this->parseResponse($response->body());
        } catch (\Exception $e) {
            Log::error('Error adding to cart', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Lỗi khi thêm vào giỏ hàng: ' . $e->getMessage()];
        }
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng
     */
    public function removeFromCart(string $productId, ?string $userid = null, ?string $pass = null, ?string $cartCookie = null): array
    {
        try {
            if ($userid && $pass) {
                // Đã đăng nhập
                $url = $this->baseUrl . "/remove.listcart?userid={$userid}&pass={$pass}&id={$productId}";
            } else {
                // Chưa đăng nhập
                if (!$cartCookie) {
                    $cartCookie = request()->cookie('DathangMabaogia');
                }
                $url = $this->baseUrl . "/removegiohang?IDPart={$productId}&id={$cartCookie}";
            }

            $response = Http::withOptions(['verify' => false])->get($url);
            
            Log::info('Remove from cart API response', [
                'url' => $url,
                'response' => $response->body()
            ]);

            return $this->parseResponse($response->body());
        } catch (\Exception $e) {
            Log::error('Error removing from cart', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Lỗi khi xóa khỏi giỏ hàng'];
        }
    }

    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng
     */
    public function updateCartQuantity(string $productId, int $quantity, ?string $userid = null, ?string $pass = null, ?string $cartCookie = null): array
    {
        try {
            if ($userid && $pass) {
                // Đã đăng nhập
                $url = $this->baseUrl . "/upcart?userid={$userid}&pass={$pass}&id={$productId}&id2={$quantity}";
            } else {
                // Chưa đăng nhập
                if (!$cartCookie) {
                    $cartCookie = request()->cookie('DathangMabaogia');
                }
                $url = $this->baseUrl . "/upgiohang?IDPart={$productId}&id={$cartCookie}&id1={$quantity}";
            }

            $response = Http::withOptions(['verify' => false])->get($url);
            
            Log::info('Update cart quantity API response', [
                'url' => $url,
                'response' => $response->body()
            ]);

            return $this->parseResponse($response->body());
        } catch (\Exception $e) {
            Log::error('Error updating cart quantity', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Lỗi khi cập nhật số lượng'];
        }
    }

    /**
     * Parse phản hồi từ API (có thể là JSON hoặc JavaScript object)
     */
    protected function parseResponse(string $responseBody): array
    {
        // Thử parse JSON trước
        $json = json_decode($responseBody, true);
        if ($json !== null) {
            return $json;
        }

        // Nếu không phải JSON, thử parse JavaScript object
        $data = ['success' => true, 'message' => 'Thành công'];

        $pattern = '/var info = \{([^}]*)\};/';
        if (preg_match($pattern, $responseBody, $matches)) {
            $jsContent = $matches[1];
            
            if (preg_match('/thongbao:\s*[\'\"]([^\'\"]*)[\'\"]/', $jsContent, $msgMatch)) {
                $data['message'] = strip_tags($msgMatch[1]);
            }
            if (preg_match('/sl:\s*(\d+)/', $jsContent, $slMatch)) {
                $data['sl'] = (int)$slMatch[1];
            }
            if (preg_match('/tongtien:\s*[\'\"]([^\'\"]*)[\'\"]/', $jsContent, $totalMatch)) {
                $data['tongtien'] = $totalMatch[1];
            }
            
            // Xác định success dựa trên message
            $message = strtolower($data['message']);
            $data['success'] = !str_contains($message, 'lỗi') && !str_contains($message, 'error');
        }

        return $data;
    }

    /**
     * Lấy thông tin user hiện tại nếu đã đăng nhập
     */
    public function getCurrentUserInfo(): array
    {
        if (Auth::check()) {
            $user = Auth::user();
            return [
                'userid' => $user->name ?? $user->email,
                'pass' => session('plain_pass', ''),
                'authenticated' => true
            ];
        }

        return ['authenticated' => false];
    }
}