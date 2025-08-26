$(document).ready(function () {
    // Quick View functionality
    $(".btn-quick-view").on("click", function (e) {
        e.preventDefault();

        const productId = $(this).data("product-id");
        const productTitle = $(this).data("product-title");

        // Show loading state
        $("#quickViewModal .modal-body").html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3">Đang tải thông tin sản phẩm...</p>
            </div>
        `);

        // Show modal
        $("#quickViewModal").modal("show");

        // Fetch product details via AJAX
        $.ajax({
            url: `/api/products/${productId}`,
            method: "GET",
            dataType: "json",
            success: function (response) {
                if (response.success && response.data) {
                    populateQuickViewModal(response.data);
                } else {
                    showQuickViewError(
                        response.error || "Không thể tải thông tin sản phẩm"
                    );
                }
            },
            error: function (xhr, status, error) {
                console.error("Error fetching product details:", error);
                let errorMessage = "Có lỗi xảy ra khi tải thông tin sản phẩm";
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                showQuickViewError(errorMessage);
            },
        });
    });

    // Function to populate the quick view modal
    function populateQuickViewModal(product) {
        const modalBody = $("#quickViewModal .modal-body");

        // Format price
        const currentPrice = formatPrice(product.gia);
        const oldPrice = product.giacu ? formatPrice(product.giacu) : "";

        // Create image gallery HTML
        let imageGallery = "";
        if (product.hinhdaidien && product.hinhdaidien.trim() !== "") {
            imageGallery = `
                <div class="product-image-gallery">
                    <div class="quick-view-image">
                        <img src="${product.hinhdaidien}" alt="${product.tieude}" class="img-fluid" 
                             onerror="this.src='${window.location.origin}/images/default-product.jpg'">
                    </div>
                    <div class="thumbnail-images">
                        <img src="${product.hinhdaidien}" alt="${product.tieude}" class="thumbnail active" 
                             onclick="changeMainImage(this, '${product.hinhdaidien}')"
                             onerror="this.src='${window.location.origin}/images/default-product.jpg'">
                    </div>
                </div>
            `;
        } else {
            imageGallery = `
                <div class="product-image-gallery">
                    <div class="quick-view-image">
                        <img src="/images/default-product.jpg" alt="${product.tieude}" class="img-fluid">
                    </div>
                    <div class="thumbnail-images">
                        <img src="/images/default-product.jpg" alt="${product.tieude}" class="thumbnail active">
                    </div>
                </div>
            `;
        }

        // Create product details HTML
        const productDetails = `
            <div class="product-details">
                <div class="category-badge">
                    <span class="badge bg-primary">${
                        product.danhmuc || "Điện tử"
                    }</span>
                </div>
                
                <h3 class="product-title">${product.tieude}</h3>
                
                <div class="rating-display">
                    <div class="stars">
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star text-warning"></i>
                    </div>
                    <span class="rating-text">4.0 (15 đánh giá)</span>
                </div>
                
                <div class="price-display">
                    <span class="current-price-display">${currentPrice}</span>
                    ${
                        oldPrice
                            ? `<span class="old-price-display">${oldPrice}</span>`
                            : ""
                    }
                </div>
                
                <div class="product-specs">
                    <h6>Thông số kỹ thuật:</h6>
                    <ul>
                        ${
                            product.thongso && product.thongso.trim() !== ""
                                ? product.thongso
                                      .split(",")
                                      .map((spec) => `<li>${spec.trim()}</li>`)
                                      .join("")
                                : "<li>Thông tin đang cập nhật</li>"
                        }
                    </ul>
                </div>
                
                <div class="product-description">
                    <h6>Mô tả:</h6>
                    <p>${
                        product.mota && product.mota.trim() !== ""
                            ? product.mota
                            : "Mô tả sản phẩm đang được cập nhật..."
                    }</p>
                </div>
                
                <div class="quick-view-actions">
                    <button class="btn btn-primary btn-add-cart" onclick="addToCart(
                        product.id
                    )">
                        <i class="bi bi-cart-plus"></i> Thêm vào giỏ hàng
                    </button>
                    <a href="/products/${
                        product.id
                    }" class="btn btn-outline-primary">
                        <i class="bi bi-eye"></i> Xem chi tiết
                    </a>
                    <button class="btn btn-outline-danger btn-wishlist" onclick="toggleWishlist(${
                        product.id
                    })">
                        <i class="bi bi-heart"></i> Yêu thích
                    </button>
                </div>
            </div>
        `;

        // Update modal content
        modalBody.html(`
            <div class="row">
                <div class="col-md-6">
                    ${imageGallery}
                </div>
                <div class="col-md-6">
                    ${productDetails}
                </div>
            </div>
        `);
    }

    // Function to show error in quick view modal
    function showQuickViewError(message) {
        $("#quickViewModal .modal-body").html(`
            <div class="text-center py-5">
                <i class="bi bi-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                <h5 class="mt-3">Có lỗi xảy ra</h5>
                <p class="text-muted">${message}</p>
                <button class="btn btn-primary" onclick="$('#quickViewModal').modal('hide')">Đóng</button>
            </div>
        `);
    }

    // Function to format price
    function formatPrice(price) {
        if (!price) return "Liên hệ";
        return new Intl.NumberFormat("vi-VN", {
            style: "currency",
            currency: "VND",
        }).format(price);
    }

    // Function to change main image when thumbnail is clicked
    window.changeMainImage = function (thumbnail, imageSrc) {
        // Remove active class from all thumbnails
        $(".thumbnail").removeClass("active");
        // Add active class to clicked thumbnail
        $(thumbnail).addClass("active");
        // Change main image
        $(".quick-view-image img").attr("src", imageSrc);
    };

    // Function to add product to cart (via proxy to external API)
    window.addToCart = function (productId) {
        $.ajax({
            url: "/api/proxy-cart-add",
            method: "POST",
            dataType: "json",
            data: {
                productId: productId,
                cartCookie: (typeof getCookie === 'function' ? getCookie('DathangMabaogia') : null)
            },
            success: function () {
                showNotification(
                    "Sản phẩm đã được thêm vào giỏ hàng!",
                    "success"
                );
                // Poll external cart until the item appears, then redirect
                var tries = 0;
                function pollAndRedirect() {
                    $.ajax({
                        url: "/api/proxy-cart-current",
                        method: "GET",
                        dataType: "json"
                    }).done(function (r) {
                        var hasItem = false;
                        var count = null;
                        if (r) {
                            if (Array.isArray(r.items)) {
                                count = r.items.length;
                                hasItem = r.items.some(function(it){ return String(it.id) === String(productId); });
                            } else if (typeof r.sl !== "undefined") {
                                count = r.sl;
                            }
                        }
                        var badge = document.querySelector(".cart-btn .badge");
                        if (badge && count !== null) badge.textContent = count;
                        if (hasItem || tries >= 4) {
                            window.location.href = "/cart";
                        } else {
                            tries += 1;
                            setTimeout(pollAndRedirect, 350);
                        }
                    }).fail(function(){
                        // On failure, still attempt to redirect after a short delay
                        setTimeout(function(){ window.location.href = "/cart"; }, 350);
                    });
                }
                pollAndRedirect();
            },
            error: function () {
                showNotification(
                    "Có lỗi xảy ra khi thêm vào giỏ hàng!",
                    "error"
                );
            },
        });
    };

    // Function to toggle wishlist (uses GET add/remove endpoints)
    window.toggleWishlist = function (productId) {
        const btn = $(`.btn-wishlist[onclick*="${productId}"]`);
        const isAdding = !btn.hasClass("active");

        fetch(`/wishlist/${isAdding ? "add" : "remove"}/${productId}`, {
            method: "GET",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                Accept: "application/json",
            },
        })
            .then((response) => response.json())
            .then((data) => {
                if (data && data.success !== false) {
                    btn.toggleClass("active");
                    if (btn.hasClass("active")) {
                        btn.removeClass("btn-outline-danger").addClass("btn-danger");
                        btn.html('<i class="bi bi-heart-fill"></i> Đã yêu thích');
                    } else {
                        btn.removeClass("btn-danger").addClass("btn-outline-danger");
                        btn.html('<i class="bi bi-heart"></i> Yêu thích');
                    }

                    const wishlistBadge = document.getElementById("wishlist-count");
                    if (wishlistBadge) {
                        fetch("/wishlist/count", {
                            method: "GET",
                            headers: {
                                "X-Requested-With": "XMLHttpRequest",
                                Accept: "application/json",
                            },
                        })
                            .then((r) => r.json())
                            .then((countData) => {
                                if (
                                    countData &&
                                    typeof countData.count !== "undefined"
                                ) {
                                    wishlistBadge.textContent = countData.count;
                                }
                            })
                            .catch(() => {});
                    }

                    showNotification(
                        `Sản phẩm đã được ${
                            isAdding ? "thêm vào" : "xóa khỏi"
                        } yêu thích!`,
                        "success"
                    );
                } else {
                    showNotification(
                        (data && data.message) || "Có lỗi xảy ra!",
                        "error"
                    );
                }
            })
            .catch(() => {
                showNotification(
                    `Có lỗi xảy ra khi ${
                        isAdding ? "thêm" : "xóa"
                    } sản phẩm khỏi yêu thích!`,
                    "error"
                );
            });
    };

    // Function to show notification
    function showNotification(message, type) {
        const alertClass =
            type === "success" ? "alert-success" : "alert-danger";
        const notification = $(`
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);

        $("body").append(notification);

        // Auto hide after 3 seconds
        setTimeout(function () {
            notification.alert("close");
        }, 3000);
    }

    // Function to update cart count
    function updateCartCount(count) {
        $(".cart-count").text(count || 0);
    }

    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Initialize popovers
    $('[data-bs-toggle="popover"]').popover();
});
