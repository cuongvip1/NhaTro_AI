<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đang đăng nhập...</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite('resources/css/app.css') {{-- nếu bạn dùng Vite/Tailwind, không có thì bỏ dòng này --}}
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-50">

    <div class="bg-white shadow-lg rounded-2xl px-8 py-6 text-center max-w-md w-full">
        <h1 class="text-lg font-semibold text-gray-800 mb-2">
            Đang xử lý đăng nhập Google...
        </h1>
        <p class="text-sm text-gray-500">
            Vui lòng đợi trong giây lát, bạn sẽ được chuyển hướng tự động.
        </p>
    </div>

    <script>
        (function () {
            const params = new URLSearchParams(window.location.search);

            // Nếu có lỗi từ API/Google
            if (params.has('google_error')) {
                const error = atob(params.get('google_error'));
                alert('Lỗi đăng nhập Google: ' + error);
                window.location.href = "{{ route('login') }}";
                return;
            }

            const token = params.get('token');
            const userEncoded = params.get('user');

            if (!token || !userEncoded) {
                alert('Không nhận được token hoặc thông tin người dùng từ server.');
                window.location.href = "{{ route('login') }}";
                return;
            }

            try {
                const user = JSON.parse(atob(userEncoded));

                // Lưu vào localStorage để web dùng gọi API
                localStorage.setItem('api_token', token);
                localStorage.setItem('auth_user', JSON.stringify(user));

                // Nếu bạn muốn lưu vào sessionStorage thay vì localStorage:
                // sessionStorage.setItem('api_token', token);
                // sessionStorage.setItem('auth_user', JSON.stringify(user));

                // Chuyển về trang chủ hoặc dashboard
                window.location.href = "{{ route('home') }}";
            } catch (e) {
                console.error(e);
                alert('Lỗi xử lý dữ liệu trả về từ server.');
                window.location.href = "{{ route('login') }}";
            }
        })();
    </script>
</body>
</html>
