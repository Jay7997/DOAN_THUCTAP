# API Giỏ hàng theo yêu cầu

Hệ thống API giỏ hàng đã được xây dựng theo đúng yêu cầu với các endpoint và chức năng như sau:

## 1. Lấy Cookie (365 ngày)

### Endpoint: `/ww1/cookie.mabaogia`
- **Phương thức**: GET
- **Mô tả**: Lần đầu truy cập để lấy cookie cho DathangMabaogia đặt hàng hoặc WishlistMabaogia yêu thích
- **Thời gian cookie**: 365 ngày
- **Phản hồi**: JSON chứa DathangMabaogia và WishlistMabaogia

```javascript
// Ví dụ response
{
  "DathangMabaogia": "abc123456",
  "WishlistMabaogia": "def789012"
}
```

## 2. Giỏ hàng hiện tại

### Endpoint: `/ww1/giohanghientai`
- **Phương thức**: GET
- **Mô tả**: Lấy danh sách sản phẩm trong giỏ hàng hiện tại
- **Cookie cần thiết**: DathangMabaogia

## 3. Yêu thích hoặc theo dõi hiện tại

### Endpoint: `/ww1/wishlisthientai`
- **Phương thức**: GET
- **Mô tả**: Lấy danh sách sản phẩm yêu thích hiện tại
- **Cookie cần thiết**: WishlistMabaogia

## 4. Thêm mặt hàng vào giỏ hàng

### Đã đăng nhập:
- **Endpoint**: `/ww1/save.addtocart?userid={userid}&pass={pass}&id={product_id}`
- **Phương thức**: GET
- **Tham số**:
  - `userid`: ID người dùng
  - `pass`: Mật khẩu
  - `id`: Mã sản phẩm

### Chưa đăng nhập:
- **Endpoint**: `/ww1/addgiohang?IDPart={product_id}&id={cookie_DathangMabaogia}`
- **Phương thức**: GET
- **Tham số**:
  - `IDPart`: Mã sản phẩm
  - `id`: Cookie DathangMabaogia

## 5. Xóa mặt hàng khỏi giỏ hàng

### Đã đăng nhập:
- **Endpoint**: `/ww1/remove.listcart?userid={userid}&pass={pass}&id={product_id}`
- **Phương thức**: GET
- **Tham số**:
  - `userid`: ID người dùng
  - `pass`: Mật khẩu
  - `id`: Mã sản phẩm

### Chưa đăng nhập:
- **Endpoint**: `/ww1/removegiohang?IDPart={product_id}&id={cookie_DathangMabaogia}`
- **Phương thức**: GET
- **Tham số**:
  - `IDPart`: Mã sản phẩm
  - `id`: Cookie DathangMabaogia

## 6. Tăng giảm số lượng mặt hàng

### Đã đăng nhập:
- **Endpoint**: `/ww1/upcart?userid={userid}&pass={pass}&id={product_id}&id2={quantity}`
- **Phương thức**: GET
- **Tham số**:
  - `userid`: ID người dùng
  - `pass`: Mật khẩu
  - `id`: Mã sản phẩm
  - `id2`: Số lượng mới

### Chưa đăng nhập:
- **Endpoint**: `/ww1/upgiohang?IDPart={product_id}&id={cookie_DathangMabaogia}&id1={quantity}`
- **Phương thức**: GET
- **Tham số**:
  - `IDPart`: Mã sản phẩm
  - `id`: Cookie DathangMabaogia
  - `id1`: Số lượng mới

## API Mở rộng (POST methods)

### Thêm vào giỏ hàng (POST)
- **Endpoint**: `/api/cart/add`
- **Phương thức**: POST
- **Body**:
```javascript
{
  "productId": "60001",
  "cartCookie": "abc123456" // optional, sẽ lấy từ cookie nếu không có
}
```

### Xóa khỏi giỏ hàng (POST)
- **Endpoint**: `/api/cart/remove`
- **Phương thức**: POST
- **Body**:
```javascript
{
  "productId": "60001",
  "cartCookie": "abc123456" // optional
}
```

### Cập nhật số lượng (POST)
- **Endpoint**: `/api/cart/update`
- **Phương thức**: POST
- **Body**:
```javascript
{
  "productId": "60001",
  "quantity": 3,
  "cartCookie": "abc123456" // optional
}
```

## Wishlist API

### Thêm vào wishlist
- **Endpoint**: `/api/wishlist/add`
- **Phương thức**: POST

### Xóa khỏi wishlist
- **Endpoint**: `/api/wishlist/remove`
- **Phương thức**: POST

### Lấy wishlist hiện tại
- **Endpoint**: `/api/wishlist/current`
- **Phương thức**: GET

## Demo Page

Truy cập `/cart/api-demo` để xem demo tương tác với tất cả các API endpoints.

### 🎯 **Hướng dẫn sử dụng Demo:**

1. **Lấy Cookie trước tiên:**
   - Click "Lấy Cookie" và đợi response thành công
   - Cookie sẽ được lưu 365 ngày

2. **Chọn sản phẩm đúng:**
   - Dùng dropdown để chọn sản phẩm có sẵn trong hệ thống
   - Hoặc nhập slug ID (không phải numeric ID)

3. **Thao tác theo thứ tự:**
   - Thêm sản phẩm vào giỏ hàng trước
   - Kiểm tra "Giỏ hàng hiện tại" để confirm
   - Sau đó mới xóa/cập nhật

4. **Copy ID tự động:**
   - Sau khi thêm thành công, click "📋 Copy ID để xóa/update"
   - ID sẽ được copy tự động vào các field khác

5. **Debug khi cần:**
   - Dùng "Test API trực tiếp" để troubleshoot
   - Check console logs để xem chi tiết

### ⚠️ **Lỗi "Dữ liệu không tồn tại":**

**Nguyên nhân phổ biến:**
- ID sản phẩm không đúng (cần slug, không phải số)
- Cookie chưa được set
- Sản phẩm chưa có trong giỏ hàng
- Format tham số không đúng

**Giải pháp:**
1. Lấy cookie trước khi thao tác
2. Chọn sản phẩm từ dropdown
3. Thêm sản phẩm trước khi xóa/update
4. Sử dụng cùng ID đã thêm thành công

## Xử lý Authentication

Hệ thống tự động xác định trạng thái đăng nhập:
- Nếu đã đăng nhập: Sử dụng userid và pass
- Nếu chưa đăng nhập: Sử dụng cookie

## Services

### ApiCartService
- Quản lý tất cả tương tác với API cart bên ngoài
- Xử lý cookie management
- Parse response từ API

### CartController
- Cung cấp endpoints theo yêu cầu
- Tích hợp với ApiCartService
- Hỗ trợ cả GET và POST methods

### WishlistController
- Cung cấp endpoints cho wishlist
- Tương tự CartController nhưng cho wishlist

## Cookie Management

- Cookie được thiết lập với thời gian 365 ngày
- Tự động gửi cookie trong các request
- Cookie được quản lý bởi Laravel Cookie system

## Error Handling

- Tất cả API đều có error handling
- Response format nhất quán
- Log chi tiết cho debugging