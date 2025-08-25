// ====== Cookie Helper ======
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
    document.cookie =
        name + "=" + (encodeURIComponent(value) || "") + expires + "; path=/";
}

// ====== Đảm bảo có cookie WishlistMabaogia từ API ngoài ======
(function () {
    let wishlistCookie = getCookie("WishlistMabaogia");
    if (!wishlistCookie) {
        $.ajax({
            url: "/api/proxy-cookie",
            method: "GET",
            dataType: "json",
            success: function (res) {
                console.log("[DEBUG] API proxy-cookie trả về:", res);
                // Luôn lấy đúng giá trị WishlistMabaogia (kiểu chuỗi số)
                let wishlistValue = null;
                if (Array.isArray(res)) {
                    for (let i = 0; i < res.length; i++) {
                        if (
                            typeof res[i].WishlistMabaogia !== "undefined" &&
                            res[i].WishlistMabaogia
                        ) {
                            wishlistValue = res[i].WishlistMabaogia;
                            break;
                        }
                    }
                }
                console.log(
                    "[DEBUG] WishlistMabaogia lấy được:",
                    wishlistValue
                );
                if (wishlistValue) {
                    setCookie("WishlistMabaogia", wishlistValue, 365);
                    console.log("[DEBUG] Đã set cookie:", wishlistValue);
                } else {
                    // fallback: random nếu không lấy được
                    let randomId =
                        Date.now().toString(36) +
                        Math.random().toString(36).substring(2, 12);
                    setCookie("WishlistMabaogia", randomId, 365);
                    console.log(
                        "[DEBUG] Không lấy được, dùng random:",
                        randomId
                    );
                }
            },
            error: function (xhr, status, error) {
                console.log(
                    "[DEBUG] Lỗi API proxy-cookie:",
                    xhr,
                    status,
                    error
                );
                // fallback: random nếu API lỗi
                let randomId =
                    Date.now().toString(36) +
                    Math.random().toString(36).substring(2, 12);
                setCookie("WishlistMabaogia", randomId, 365);
                console.log("[DEBUG] API lỗi, dùng random:", randomId);
            },
        });
    }
})();

// ====== Cập nhật số lượng wishlist trên header ======
function reloadWishlistCount() {
    $.ajax({
        url: "/wishlist/count",
        method: "GET",
        success: function (res) {
            let total = 0;
            if (res && typeof res.count !== "undefined") {
                total = res.count;
            }
            $("#wishlist-count").text(total);
        },
        error: function () {
            $("#wishlist-count").text("0");
        },
    });
}

// ====== Refresh danh sách wishlist ======
function refreshWishlistTable() {
    console.log("[Wishlist] Refreshing wishlist table...");
    location.reload();
}

// ====== Thêm sản phẩm vào wishlist (AJAX) ======
function addToWishlist(productId, callback) {
    console.log("[Wishlist] Adding wishlist item with ID:", productId);

    $.ajax({
        url: `/wishlist/add/${productId}`,
        method: "GET",
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
        },
        data: {
            wishlistCookie: getCookie("WishlistMabaogia") || undefined,
        },
        success: function (res) {
            console.log("[Wishlist] Kết quả thêm từ Laravel API:", res);

            let message = "Đã thêm vào yêu thích!";
            if (res && res.message) {
                message = res.message;
            } else if (res && res.thongbao) {
                message = res.thongbao;
            }

            console.log("[Wishlist] Final message:", message);
            reloadWishlistCount();
            showWishlistToast(message);
            if (typeof callback === "function") callback(true);
        },
        error: function (xhr) {
            console.log("[Wishlist] Lỗi thêm qua Laravel API:", xhr);
            showWishlistToast("Thêm vào yêu thích thất bại!");
            if (typeof callback === "function") callback(false);
        },
    });
}

// ====== Xoá sản phẩm khỏi wishlist (AJAX) ======
function removeFromWishlist(productId, callback) {
    console.log("[DEBUG] Removing wishlist item with ID:", productId);

    $.ajax({
        url: `/wishlist/remove/${productId}`,
        method: "GET",
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
        },
        data: {
            wishlistCookie: getCookie("WishlistMabaogia") || undefined,
        },
        success: function (res) {
            console.log("[Wishlist] Kết quả xóa từ Laravel API:", res);

            let message = "Đã xóa khỏi yêu thích!";
            let success = false;

            if (res && res.success !== undefined) {
                success = res.success;
            } else if (res && res.message) {
                message = res.message;
                success = true;
            } else if (res && res.thongbao) {
                message = res.thongbao;
                success = true;
            }

            console.log("[Wishlist] Final result:", { success, message });

            if (success) {
                reloadWishlistCount();
                showWishlistToast(message);
                if (typeof callback === "function") callback(true);
            } else {
                showWishlistToast("Xóa khỏi yêu thích thất bại: " + message);
                if (typeof callback === "function") callback(false);
            }
        },
        error: function (xhr) {
            console.log("[Wishlist] Lỗi xóa qua Laravel API:", xhr);
            showWishlistToast("Xóa khỏi yêu thích thất bại!");
            if (typeof callback === "function") callback(false);
        },
    });
}
// ====== Hiển thị toast/thông báo ======
function showWishlistToast(msg) {
    let toast = $("#wishlist-toast");
    if (toast.length === 0) {
        toast = $('<div id="wishlist-toast"></div>').appendTo("body");
        toast.css({
            position: "fixed",
            bottom: "60px",
            left: "50%",
            transform: "translateX(-50%)",
            background: "#222",
            color: "#fff",
            padding: "12px 24px",
            borderRadius: "24px",
            zIndex: 99999,
            fontSize: "15px",
            display: "none",
        });
    }
    toast.html(msg).fadeIn(300);
    setTimeout(function () {
        toast.fadeOut(300);
    }, 2000);
}

// ====== Gắn sự kiện cho nút yêu thích ======
$(function () {
    reloadWishlistCount();

    $(document).on("click", ".btn-add-wishlist", function (e) {
        e.preventDefault();
        let productId = $(this).data("id");
        addToWishlist(productId, function (success) {
            if (success) {
                $(`.btn-add-wishlist[data-id="${productId}"]`).addClass(
                    "active"
                );
            }
        });
    });

    $(document).on("click", ".btn-remove-wishlist", function (e) {
        e.preventDefault();
        let productId = $(this).data("id");
        console.log("[DEBUG] Removing wishlist item with ID:", productId);

        // Disable button to prevent multiple clicks
        let $button = $(this);
        $button
            .prop("disabled", true)
            .html('<i class="bi bi-hourglass-split"></i> Đang xóa...');

        removeFromWishlist(productId, function (success) {
            console.log("[DEBUG] Remove callback success:", success);
            if (success) {
                // Show success message first
                showWishlistToast("Đã xóa sản phẩm khỏi yêu thích!");

                // Remove row from table with animation
                $(`#row-wishlist-${productId}`).fadeOut(300, function () {
                    $(this).remove();

                    // Check if table is empty
                    if ($("tbody tr").length === 0) {
                        // Refresh page to show empty state
                        setTimeout(() => {
                            refreshWishlistTable();
                        }, 500);
                    } else {
                        // Update any wishlist buttons on other pages
                        $(
                            `.btn-add-wishlist[data-id="${productId}"]`
                        ).removeClass("active");
                        // Ensure page reflects latest server state
                        setTimeout(() => {
                            refreshWishlistTable();
                        }, 500);
                    }
                });
            } else {
                // Re-enable button if failed
                $button
                    .prop("disabled", false)
                    .html('<i class="bi bi-trash"></i> Xoá');
            }
        });
    });
});
