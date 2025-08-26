// ====== Cookie Helpers ======
function getCookie(name) {
	let dc = document.cookie;
	let prefix = name + "=";
	let begin = dc.indexOf("; " + prefix);
	if (begin === -1) {
		begin = dc.indexOf(prefix);
		if (begin !== 0) return null;
	} else {
		begin += 2;
	}
	let end = document.cookie.indexOf(";", begin);
	if (end === -1) {
		end = dc.length;
	}
	return decodeURIComponent(dc.substring(begin + prefix.length, end));
}

function setCookie(name, value, days = 365) {
	let expires = "";
	if (days) {
		let date = new Date();
		date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
		expires = "; expires=" + date.toUTCString();
	}
	document.cookie = name + "=" + (encodeURIComponent(value) || "") + expires + "; path=/";
}

// ====== Ensure DathangMabaogia cookie exists via backend proxy to external API ======
(function ensureCartCookie() {
	let cartCookie = getCookie("DathangMabaogia");
	if (!cartCookie) {
		// Use backend proxy to avoid CORS and normalize JSON
		$.ajax({
			url: "/cart/get-cookie",
			method: "GET",
			dataType: "json",
			success: function (res) {
				var cartValue = null;
				if (res && res.DathangMabaogia) {
					cartValue = res.DathangMabaogia;
				}
				if (cartValue) {
					setCookie("DathangMabaogia", cartValue, 365);
				} else {
					// fallback random if API fails to supply
					var randomId = Date.now().toString(36) + Math.random().toString(36).substring(2, 12);
					setCookie("DathangMabaogia", randomId, 365);
				}
			},
			error: function () {
				var randomId = Date.now().toString(36) + Math.random().toString(36).substring(2, 12);
				setCookie("DathangMabaogia", randomId, 365);
			}
		});
	}
})();

// ====== Cart API via Laravel proxy endpoints ======
function cartFetchCurrent() {
	return $.ajax({
		url: "/api/proxy-cart-current",
		method: "GET",
		dataType: "json",
		data: {
			cartCookie: getCookie("DathangMabaogia") || undefined
		}
	});
}

function cartAdd(productId, opts) {
	opts = opts || {};
	return $.ajax({
		url: "/api/proxy-cart-add",
		method: "POST",
		dataType: "json",
		data: {
			productId: productId,
			cartCookie: getCookie("DathangMabaogia") || undefined,
			userid: opts.userid || undefined,
			pass: opts.pass || undefined,
			_ts: Date.now()
		}
	});
}

function cartRemove(productId, opts) {
	opts = opts || {};
	return $.ajax({
		url: "/api/proxy-cart-remove",
		method: "POST",
		dataType: "json",
		data: {
			productId: productId,
			cartCookie: getCookie("DathangMabaogia") || undefined,
			userid: opts.userid || undefined,
			pass: opts.pass || undefined,
			_ts: Date.now()
		}
	});
}

function cartUpdate(productId, quantity, opts) {
	opts = opts || {};
	return $.ajax({
		url: "/api/proxy-cart-update",
		method: "POST",
		dataType: "json",
		data: {
			productId: productId,
			quantity: quantity,
			cartCookie: getCookie("DathangMabaogia") || undefined,
			userid: opts.userid || undefined,
			pass: opts.pass || undefined,
			_ts: Date.now()
		}
	});
}

// ====== UI Helpers ======
function showCartToast(msg) {
	let toast = $("#cart-toast");
	if (toast.length === 0) {
		toast = $('<div id="cart-toast"></div>').appendTo("body");
		toast.css({
			position: "fixed",
			bottom: "20px",
			left: "50%",
			transform: "translateX(-50%)",
			background: "#222",
			color: "#fff",
			padding: "10px 18px",
			borderRadius: "22px",
			zIndex: 99999,
			fontSize: "14px",
			display: "none",
		});
	}
	toast.html(msg).fadeIn(250);
	setTimeout(function () {
		toast.fadeOut(250);
	}, 1800);
}

function updateCartBadgeCount(count) {
	const $badge = $(".cart-btn .badge");
	if ($badge.length) {
		$badge.text(parseInt(count || 0, 10));
	}
}

// ====== Bind common interactions if needed ======
$(function () {
	// Example: wire click handler for buttons with data-action
	$(document).on("click", "[data-add-to-cart]", function (e) {
		e.preventDefault();
		var productId = $(this).data("add-to-cart");
		cartAdd(productId)
			.done(function (res) {
				showCartToast("Đã thêm vào giỏ hàng!");
				// try to refresh count from current cart
				cartFetchCurrent()
					.done(function (r) {
						if (r && typeof r.sl !== "undefined") {
							updateCartBadgeCount(r.sl);
						}
					})
					.always(function () {});
			})
			.fail(function () {
				showCartToast("Thêm vào giỏ thất bại!");
			});
	});
});