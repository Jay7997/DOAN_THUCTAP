@extends('layouts.app')

@section('title', 'Debug Đặt Hàng')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">🐛 Debug Hệ Thống Đặt Hàng</h2>

            <div class="card mb-4">
                <div class="card-header">
                    <h5>🧪 Test API Đặt Hàng</h5>
                </div>
                <div class="card-body">
                    <p>Click vào nút bên dưới để test API đặt hàng trực tiếp:</p>
                    <button id="testApiBtn" class="btn btn-primary">Test API Đặt Hàng</button>
                    <div id="apiResult" class="mt-3"></div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5>📧 Test Gửi Email</h5>
                </div>
                <div class="card-body">
                    <p>Test chức năng gửi email xác nhận:</p>
                    <button id="testEmailBtn" class="btn btn-success">Test Gửi Email</button>
                    <div id="emailResult" class="mt-3"></div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5>📋 Thông Tin Hệ Thống</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>🔧 Cấu hình Email:</h6>
                            <ul class="list-unstyled">
                                <li><strong>Driver:</strong> {{ config('mail.default') }}</li>
                                <li><strong>From Address:</strong> {{ config('mail.from.address') }}</li>
                                <li><strong>From Name:</strong> {{ config('mail.from.name') }}</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>🌐 API Endpoint:</h6>
                            <ul class="list-unstyled">
                                <li><strong>Trạng thái:</strong> Đã chuyển sang xử lý cục bộ</li>
                                <li><strong>Ghi chú:</strong> Không còn sử dụng API bên ngoài</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>📝 Hướng Dẫn Debug</h5>
                </div>
                <div class="card-body">
                    <ol>
                        <li><strong>Test API:</strong> Click "Test API Đặt Hàng" để kiểm tra kết nối API</li>
                        <li><strong>Test Email:</strong> Click "Test Gửi Email" để kiểm tra chức năng email</li>
                        <li><strong>Kiểm tra Log:</strong> Xem file <code>storage/logs/laravel.log</code> để xem chi tiết lỗi</li>
                        <li><strong>Kiểm tra Console:</strong> Mở Developer Tools để xem response từ API</li>
                    </ol>

                    <div class="alert alert-info">
                        <strong>💡 Tip:</strong> Nếu API trả về lỗi, hãy kiểm tra:
                        <ul class="mb-0 mt-2">
                            <li>URL API có đúng không</li>
                            <li>API có hoạt động không</li>
                            <li>Format dữ liệu gửi đi có đúng không</li>
                            <li>Response từ API có đúng định dạng không</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('testApiBtn').addEventListener('click', function() {
        const btn = this;
        const resultDiv = document.getElementById('apiResult');

        btn.disabled = true;
        btn.textContent = 'Đang test...';
        resultDiv.innerHTML = '<div class="alert alert-info">Đang test API...</div>';

        fetch('/test-order-api')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const apiResponse = data.api_response;
                    let html = '<div class="alert alert-success"><h6>✅ API Test Thành Công!</h6>';
                    html += '<p><strong>Status Code:</strong> ' + apiResponse.status_code + '</p>';
                    html += '<p><strong>Response Body:</strong></p>';
                    html += '<pre class="bg-light p-2 rounded">' + JSON.stringify(apiResponse.body, null, 2) + '</pre>';

                    if (apiResponse.json_parsed) {
                        html += '<p><strong>JSON Parsed:</strong></p>';
                        html += '<pre class="bg-light p-2 rounded">' + JSON.stringify(apiResponse.json_parsed, null, 2) + '</pre>';
                    } else {
                        html += '<p><strong>JSON Error:</strong> ' + apiResponse.json_error + '</p>';
                    }

                    html += '</div>';
                    resultDiv.innerHTML = html;
                } else {
                    resultDiv.innerHTML = '<div class="alert alert-danger"><h6>❌ API Test Thất Bại!</h6><p>' + data.message + '</p></div>';
                }
            })
            .catch(error => {
                resultDiv.innerHTML = '<div class="alert alert-danger"><h6>❌ Lỗi Kết Nối!</h6><p>' + error.message + '</p></div>';
            })
            .finally(() => {
                btn.disabled = false;
                btn.textContent = 'Test API Đặt Hàng';
            });
    });

    document.getElementById('testEmailBtn').addEventListener('click', function() {
        const btn = this;
        const resultDiv = document.getElementById('emailResult');

        btn.disabled = true;
        btn.textContent = 'Đang test...';
        resultDiv.innerHTML = '<div class="alert alert-info">Đang test email...</div>';

        fetch('/test-email')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    resultDiv.innerHTML = '<div class="alert alert-success"><h6>✅ Email Test Thành Công!</h6><p>' + data.message + '</p></div>';
                } else {
                    resultDiv.innerHTML = '<div class="alert alert-danger"><h6>❌ Email Test Thất Bại!</h6><p>' + data.message + '</p></div>';
                }
            })
            .catch(error => {
                resultDiv.innerHTML = '<div class="alert alert-danger"><h6>❌ Lỗi Kết Nối!</h6><p>' + error.message + '</p></div>';
            })
            .finally(() => {
                btn.disabled = false;
                btn.textContent = 'Test Gửi Email';
            });
    });
</script>
@endsection
