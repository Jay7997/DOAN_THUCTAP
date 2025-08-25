<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Services\CartService;
use App\Services\ApiCartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    protected $productService;
    protected $cartService;
    protected $apiCartService;

    public function __construct(ProductService $productService, CartService $cartService, ApiCartService $apiCartService)
    {
        $this->productService = $productService;
        $this->cartService = $cartService;
        $this->apiCartService = $apiCartService;
    }

    public function add($productId, Request $request)
    {
        Log::info("Attempting to add product to cart", ['productId' => $productId]);

        if (!is_numeric($productId) || $productId <= 0) {
            Log::error("Invalid product ID", ['productId' => $productId]);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'ID sản phẩm không hợp lệ'], 400);
            }
            return redirect()->back()->with('error', 'ID sản phẩm không hợp lệ');
        }

        $product = $this->productService->fetchProductDetail($productId);
        Log::info("Product fetch result", ['productId' => $productId, 'product' => $product]);

        if (isset($product['error'])) {
            Log::error("Failed to fetch product detail", ['productId' => $productId, 'error' => $product['error']]);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Sản phẩm không tồn tại'], 404);
            }
            return redirect()->back()->with('error', 'Sản phẩm không tồn tại');
        }

        if (empty($product['hinhdaidien']) || strpos($product['hinhdaidien'], 'placeholder') !== false) {
            Log::error("Invalid product image", ['productId' => $productId, 'hinhdaidien' => $product['hinhdaidien']]);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Sản phẩm không có hình ảnh hợp lệ'], 400);
            }
            return redirect()->back()->with('error', 'Sản phẩm không có hình ảnh hợp lệ');
        }

        $quantity = (int) $request->input('quantity', 1);
        $success = $this->cartService->addToCart($product, $quantity);

        Log::info("Cart after adding", ['cart' => $productId, 'success' => $success]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => $success,
                'message' => $success ? 'Đã thêm sản phẩm vào giỏ hàng' : 'Không thể thêm sản phẩm vào giỏ hàng',
                'cartCount' => $this->cartService->getTotalQuantity()
            ]);
        }

        return redirect()->route('products.index')->with('success', 'Đã thêm sản phẩm vào giỏ hàng');
    }

    /**
     * Thêm sản phẩm vào giỏ hàng qua POST request
     */
    public function addPost(Request $request)
    {
        Log::info("Attempting to add product to cart via POST", ['request' => $request->all()]);

        $productId = $request->input('product_id');

        if (!$productId || !is_numeric($productId) || $productId <= 0) {
            Log::error("Invalid product ID in POST request", ['productId' => $productId]);
            return response()->json([
                'success' => false,
                'error' => 'ID sản phẩm không hợp lệ'
            ], 400);
        }

        $product = $this->productService->fetchProductDetail($productId);
        Log::info("Product fetch result for POST", ['productId' => $productId, 'product' => $product]);

        if (isset($product['error'])) {
            Log::error("Failed to fetch product detail for POST", ['productId' => $productId, 'error' => $product['error']]);
            return response()->json([
                'success' => false,
                'error' => 'Sản phẩm không tồn tại'
            ], 404);
        }

        $quantity = (int) $request->input('quantity', 1);
        $success = $this->cartService->addToCart($product, $quantity);

        Log::info("Cart after adding via POST", ['productId' => $productId, 'success' => $success]);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Đã thêm sản phẩm vào giỏ hàng' : 'Không thể thêm sản phẩm vào giỏ hàng',
            'cartCount' => $this->cartService->getTotalQuantity()
        ]);
    }


    public function view()
    {
        return view('cart.view', [
            'cart' => $this->cartService->getCart(),
            'total' => $this->cartService->getCartTotal(),
            'quantity' => $this->cartService->getTotalQuantity(),
        ]);
    }


    public function update(Request $request)
    {
        $productId = (int) $request->input('product_id');
        $quantity = (int) $request->input('quantity');

        $success = $this->cartService->updateCart($productId, $quantity);

        if ($success) {
            return redirect()->route('cart.view')->with('success', 'Cập nhật giỏ hàng thành công');
        }

        return redirect()->route('cart.view')->with('error', 'Không tìm thấy sản phẩm trong giỏ');
    }


    public function remove($productId)
    {
        $success = $this->cartService->removeFromCart($productId);

        return redirect()->route('cart.view')->with(
            $success ? 'success' : 'error',
            $success ? 'Đã xóa sản phẩm khỏi giỏ hàng' : 'Không tìm thấy sản phẩm trong giỏ'
        );
    }

    public function clear()
    {
        $this->cartService->clearCart();
        return redirect()->route('cart.view')->with('success', 'Đã xóa toàn bộ giỏ hàng');
    }

    // ========== API CART METHODS THEO YÊU CẦU ==========

    /**
     * Lấy cookie cho DathangMabaogia và WishlistMabaogia
     */
    public function getCookies()
    {
        $cookies = $this->apiCartService->getCookies();
        
        $response = response()->json($cookies);
        
        // Thiết lập cookie với thời gian 365 ngày
        if (isset($cookies['DathangMabaogia'])) {
            $response->cookie('DathangMabaogia', $cookies['DathangMabaogia'], 365 * 24 * 60);
        }
        if (isset($cookies['WishlistMabaogia'])) {
            $response->cookie('WishlistMabaogia', $cookies['WishlistMabaogia'], 365 * 24 * 60);
        }
        
        return $response;
    }

    /**
     * Lấy giỏ hàng hiện tại
     */
    public function getCurrentCart(Request $request)
    {
        $cartCookie = $request->cookie('DathangMabaogia');
        $cart = $this->apiCartService->getCurrentCart($cartCookie);
        
        return response()->json($cart);
    }

    /**
     * Thêm sản phẩm vào giỏ hàng qua API
     */
    public function apiAddToCart(Request $request)
    {
        $productId = $request->input('productId') ?: $request->input('IDPart');
        $cartCookie = $request->input('cartCookie') ?: $request->input('id') ?: $request->cookie('DathangMabaogia');
        
        $userInfo = $this->apiCartService->getCurrentUserInfo();
        
        if ($userInfo['authenticated']) {
            $result = $this->apiCartService->addToCart(
                $productId,
                $userInfo['userid'],
                $userInfo['pass']
            );
        } else {
            $result = $this->apiCartService->addToCart(
                $productId,
                null,
                null,
                $cartCookie
            );
        }
        
        return response()->json($result);
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng qua API
     */
    public function apiRemoveFromCart(Request $request)
    {
        $productId = $request->input('productId') ?: $request->input('IDPart');
        $cartCookie = $request->input('cartCookie') ?: $request->input('id') ?: $request->cookie('DathangMabaogia');
        
        $userInfo = $this->apiCartService->getCurrentUserInfo();
        
        if ($userInfo['authenticated']) {
            $result = $this->apiCartService->removeFromCart(
                $productId,
                $userInfo['userid'],
                $userInfo['pass']
            );
        } else {
            $result = $this->apiCartService->removeFromCart(
                $productId,
                null,
                null,
                $cartCookie
            );
        }
        
        return response()->json($result);
    }

    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng qua API
     */
    public function apiUpdateQuantity(Request $request)
    {
        $productId = $request->input('productId') ?: $request->input('IDPart');
        $quantity = $request->input('quantity') ?: $request->input('id1') ?: $request->input('id2');
        $cartCookie = $request->input('cartCookie') ?: $request->input('id') ?: $request->cookie('DathangMabaogia');
        
        $userInfo = $this->apiCartService->getCurrentUserInfo();
        
        if ($userInfo['authenticated']) {
            $result = $this->apiCartService->updateCartQuantity(
                $productId,
                (int)$quantity,
                $userInfo['userid'],
                $userInfo['pass']
            );
        } else {
            $result = $this->apiCartService->updateCartQuantity(
                $productId,
                (int)$quantity,
                null,
                null,
                $cartCookie
            );
        }
        
        return response()->json($result);
    }
}
