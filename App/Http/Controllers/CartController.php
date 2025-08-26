<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class CartController extends Controller
{
    protected $productService;
    protected $cartService;

    public function __construct(ProductService $productService, CartService $cartService)
    {
        $this->productService = $productService;
        $this->cartService = $cartService;
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


    public function view(Request $request)
    {
        // Ưu tiên hiển thị theo giỏ hàng ngoài (cookie DathangMabaogia)
        $externalCart = [];
        $externalTotal = 0;

        try {
            $cookie = $request->cookie('DathangMabaogia');
            if ($cookie) {
                $response = Http::withOptions(['verify' => false])
                    ->withHeaders(['Cache-Control' => 'no-cache'])
                    ->withCookies([
                        'DathangMabaogia' => $cookie,
                    ], 'demodienmay.125.atoz.vn')
                    ->get('https://demodienmay.125.atoz.vn/ww1/giohanghientai.asp?ts=' . time());

                if ($response->ok()) {
                    $json = $response->json();
                    if (!empty($json['items'])) {
                        foreach ($json['items'] as $item) {
                            $image = $item['image'] ?? '';
                            if ($image && !str_starts_with($image, 'http')) {
                                $image = 'https://demodienmay.125.atoz.vn' . $image;
                            }
                            $id = (string)($item['id'] ?? '');
                            $qty = (int)($item['quantity'] ?? ($item['sl'] ?? 1));
                            $price = (float)($item['price'] ?? 0);
                            $externalCart[$id] = [
                                'id' => $id,
                                'tieude' => $item['partName'] ?? ($item['name'] ?? 'Sản phẩm'),
                                'hinhdaidien' => $image,
                                'gia' => $price,
                                'quantity' => $qty,
                            ];
                            $externalTotal += $price * $qty;
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Load external cart failed', ['error' => $e->getMessage()]);
        }

        $cartData = !empty($externalCart) ? $externalCart : $this->cartService->getCart();
        $totalData = !empty($externalCart) ? $externalTotal : $this->cartService->getCartTotal();

        return view('cart.view', [
            'cart' => $cartData,
            'total' => $totalData,
            'quantity' => !empty($externalCart) ? array_sum(array_column($externalCart, 'quantity')) : $this->cartService->getTotalQuantity(),
            'title' => 'Giỏ hàng'
        ]);
    }


    public function update(Request $request)
    {
        $productId = (string) $request->input('product_id');
        $quantity = (int) $request->input('quantity');

        $cookie = $request->cookie('DathangMabaogia');
        if ($cookie) {
            // Update external cart
            try {
                $apiUrl = "https://demodienmay.125.atoz.vn/ww1/upgiohang.asp?IDPart={$productId}&id={$cookie}&id1={$quantity}";
                $res = Http::withOptions(['verify' => false])->get($apiUrl);
                $body = $res->body();
                $success = $res->ok();
                if (!$success && is_string($body)) {
                    $lower = mb_strtolower(strip_tags($body), 'UTF-8');
                    $success = str_contains($lower, 'thành công') || str_contains($lower, 'cập nhật');
                }
                if ($success) {
                    return redirect()->route('cart.view')->with('success', 'Cập nhật giỏ hàng thành công');
                }
            } catch (\Throwable $e) {
                Log::warning('External cart update failed', ['error' => $e->getMessage()]);
            }
        }

        // Fallback to session cart
        $success = $this->cartService->updateCart((int)$productId, $quantity);
        if ($success) {
            return redirect()->route('cart.view')->with('success', 'Cập nhật giỏ hàng thành công');
        }
        return redirect()->route('cart.view')->with('error', 'Không tìm thấy sản phẩm trong giỏ');
    }


    public function remove(Request $request, $productId)
    {
        $productId = (string) $productId;
        $cookie = $request->cookie('DathangMabaogia');
        if ($cookie) {
            // Remove from external cart
            try {
                $apiUrl = "https://demodienmay.125.atoz.vn/ww1/removegiohang.asp?IDPart={$productId}&id={$cookie}";
                $res = Http::withOptions(['verify' => false])->get($apiUrl);
                $body = $res->body();
                $success = $res->ok();
                if (!$success && is_string($body)) {
                    // API có thể trả về JS object hoặc HTML có chữ 'thành công'/'xóa'
                    $lower = mb_strtolower(strip_tags($body), 'UTF-8');
                    $success = str_contains($lower, 'thành công') || str_contains($lower, 'xóa') || str_contains($lower, 'xoá');
                }
                if ($success) {
                    return redirect()->route('cart.view')->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng');
                }
            } catch (\Throwable $e) {
                Log::warning('External cart remove failed', ['error' => $e->getMessage()]);
            }
        }

        // Fallback to session cart
        $success = $this->cartService->removeFromCart((int)$productId);
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
}
