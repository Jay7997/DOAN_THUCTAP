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

// ====== Ensure DathangMabaogia cookie exists via backend proxy ======
(function ensureCartCookie() {
    let cartCookie = getCookie("DathangMabaogia");
    if (!cartCookie) {
        $.ajax({
            url: "/cart/get-cookie",
            method: "GET",
            dataType: "json",
            success: function (res) {
                let value = res && res.DathangMabaogia ? res.DathangMabaogia : null;
                if (!value) {
                    value = Date.now().toString(36) + Math.random().toString(36).substring(2, 12);
                }
                setCookie("DathangMabaogia", value, 365);
            },
            error: function () {
                let value = Date.now().toString(36) + Math.random().toString(36).substring(2, 12);
                setCookie("DathangMabaogia", value, 365);
            },
        });
    }
})();

// ====== UI Helpers ======
function updateCartBadge(count) {
    const el = document.getElementById("cart-count");
    if (el) {
        el.textContent = count != null ? count : 0;
    }
}

function showCartToast(message, type = "success") {
    const alertClass = type === "success" ? "alert-success" : "alert-danger";
    const notification = $(`
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    $("body").append(notification);
    setTimeout(function () { notification.alert("close"); }, 2500);
}

// ====== API Calls (Proxy) ======
function fetchCartCurrent(callback) {
    $.ajax({
        url: "/api/proxy-cart-current",
        method: "GET",
        data: { cartCookie: getCookie("DathangMabaogia") || undefined },
        success: function (res) { if (typeof callback === "function") callback(true, res); },
        error: function (xhr) { if (typeof callback === "function") callback(false, xhr); },
    });
}

function proxyCartAdd(productId, callback) {
    $.ajax({
        url: "/api/proxy-cart-add",
        method: "POST",
        headers: { "X-Requested-With": "XMLHttpRequest", Accept: "application/json" },
        data: {
            productId: productId,
            cartCookie: getCookie("DathangMabaogia") || undefined,
        },
        success: function (res) {
            // Try to normalize count if provided via JS object parsing or fallback
            if (res && (res.sl != null || res.count != null)) {
                updateCartBadge(res.sl || res.count);
            }
            if (typeof callback === "function") callback(true, res);
        },
        error: function (xhr) {
            if (typeof callback === "function") callback(false, xhr);
        },
    });
}

function proxyCartRemove(productId, callback) {
    $.ajax({
        url: "/api/proxy-cart-remove",
        method: "POST",
        headers: { "X-Requested-With": "XMLHttpRequest", Accept: "application/json" },
        data: {
            productId: productId,
            cartCookie: getCookie("DathangMabaogia") || undefined,
        },
        success: function (res) {
            if (res && (res.sl != null || res.count != null)) {
                updateCartBadge(res.sl || res.count);
            }
            if (typeof callback === "function") callback(true, res);
        },
        error: function (xhr) {
            if (typeof callback === "function") callback(false, xhr);
        },
    });
}

function proxyCartUpdate(productId, quantity, callback) {
    $.ajax({
        url: "/api/proxy-cart-update",
        method: "POST",
        headers: { "X-Requested-With": "XMLHttpRequest", Accept: "application/json" },
        data: {
            productId: productId,
            quantity: quantity,
            cartCookie: getCookie("DathangMabaogia") || undefined,
        },
        success: function (res) {
            if (res && (res.sl != null || res.count != null)) {
                updateCartBadge(res.sl || res.count);
            }
            if (typeof callback === "function") callback(true, res);
        },
        error: function (xhr) {
            if (typeof callback === "function") callback(false, xhr);
        },
    });
}

// ====== Public API for UI ======
window.CartAPI = {
    add: function(productId, onDone) {
        proxyCartAdd(productId, function(success, res){
            showCartToast(success ? "Đã thêm vào giỏ hàng" : "Thêm vào giỏ hàng thất bại", success ? "success" : "error");
            if (typeof onDone === "function") onDone(success, res);
        });
    },
    remove: function(productId, onDone) {
        proxyCartRemove(productId, function(success, res){
            showCartToast(success ? "Đã xóa khỏi giỏ hàng" : "Xóa khỏi giỏ hàng thất bại", success ? "success" : "error");
            if (typeof onDone === "function") onDone(success, res);
        });
    },
    update: function(productId, quantity, onDone) {
        proxyCartUpdate(productId, quantity, function(success, res){
            showCartToast(success ? "Đã cập nhật giỏ hàng" : "Cập nhật giỏ hàng thất bại", success ? "success" : "error");
            if (typeof onDone === "function") onDone(success, res);
        });
    },
    reload: function(onDone) { fetchCartCurrent(onDone); }
};

// Optionally initialize cart count on load
$(function(){
    fetchCartCurrent(function(success, res){
        try {
            if (success && res && res.items) {
                updateCartBadge(res.items.length);
            }
        } catch(e) {}
    });
});

