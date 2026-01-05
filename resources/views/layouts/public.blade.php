<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Phòng trọ')</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-800">
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-3 flex justify-between items-center">
            <a href="{{ route('public.bai-dang.index') }}" class="text-xl font-bold text-indigo-600">🏠 Khi Yêu Người
                Nói Rằng...</a>
        </div>
    </nav>

    <main>@yield('content')</main>

    <footer class="mt-16 py-6 text-center text-gray-500 text-sm border-t">
        © {{ date('Y') }} Khi Yêu Người Nói Rằng... — All rights reserved.
    </footer>
</body>

</html>