<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WishlistController extends Controller
{
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
		$response = Http::withCookies([
			'WishlistMabaogia' => $cookie,
		], 'demodienmay.125.atoz.vn')->get('https://demodienmay.125.atoz.vn/ww1/wishlisthientai.asp');

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
		$userid = session('user_email');
		$pass = session('plain_pass');
		$cookie = $request->cookie('WishlistMabaogia');

		// Nếu thiếu cookie, thử lấy từ API ngoài
		if (!$cookie) {
			try {
				$cookieRes = Http::withOptions(['verify' => false])->get('https://demodienmay.125.atoz.vn/ww1/cookie.mabaogia.asp');
				$cookieJson = $cookieRes->json();
				if (is_array($cookieJson)) {
					foreach ($cookieJson as $item) {
						if (isset($item['WishlistMabaogia']) && $item['WishlistMabaogia']) {
							$cookie = $item['WishlistMabaogia'];
							break;
						}
					}
				}
			} catch (\Exception $e) {
				Log::warning('Wishlist add: cannot fetch WishlistMabaogia cookie', ['error' => $e->getMessage()]);
			}
		}

		if ($userid && $pass) {
			$apiUrl = "https://demodienmay.125.atoz.vn/ww1/save.wishlist.asp?userid={$userid}&pass={$pass}&id={$id}";
		} else {
			$apiUrl = "https://demodienmay.125.atoz.vn/ww1/addwishlist.asp?IDPart={$id}&id={$cookie}";
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
		$userid = session('user_email');
		$pass = session('plain_pass');
		$cookie = $request->input('wishlistCookie') ?: $request->cookie('WishlistMabaogia');

		// Nếu thiếu cookie, thử lấy từ API ngoài
		if (!$cookie) {
			try {
				$cookieRes = Http::withOptions(['verify' => false])->get('https://demodienmay.125.atoz.vn/ww1/cookie.mabaogia.asp');
				$cookieJson = $cookieRes->json();
				if (is_array($cookieJson)) {
					foreach ($cookieJson as $item) {
						if (isset($item['WishlistMabaogia']) && $item['WishlistMabaogia']) {
							$cookie = $item['WishlistMabaogia'];
							break;
						}
					}
				}
			} catch (\Exception $e) {
				Log::warning('Wishlist remove: cannot fetch WishlistMabaogia cookie', ['error' => $e->getMessage()]);
			}
		}

		if ($userid && $pass) {
			$apiUrl = "https://demodienmay.125.atoz.vn/ww1/remove.listwishlist.asp?userid={$userid}&pass={$pass}&id={$id}";
		} else {
			$apiUrl = "https://demodienmay.125.atoz.vn/ww1/removewishlist.asp?IDPart={$id}&id={$cookie}";
		}

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

		$thongbao = 'Đã xoá khỏi yêu thích!';
		$success = false;

		// Cố gắng parse JSON nếu có
		$json = null;
		try {
			$json = $response->json();
		} catch (\Throwable $t) {
			$json = null;
		}
		if (is_array($json)) {
			if (isset($json['thongbao'])) {
				$thongbao = strip_tags($json['thongbao']);
			}
			$success = !isset($json['error']);
		} else {
			// Fallback: parse JavaScript object dạng var info = { ... }
			$responseBody = $response->body();
			$pattern = '/var info = \{([^}]*)\};/';
			if (preg_match($pattern, $responseBody, $matches)) {
				$jsContent = $matches[1];
				$thongbaoPattern = '/thongbao:\s*[\'\"]([^\'\"]*)[\'\"]*/';
				if (preg_match($thongbaoPattern, $jsContent, $thongbaoMatch)) {
					$thongbao = strip_tags($thongbaoMatch[1]);
					$success = true;
				}
			}
			if (stripos($responseBody, 'thành công') !== false || stripos($responseBody, 'đã xóa') !== false) {
				$success = true;
			}
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
}