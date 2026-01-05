<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin - Đăng nhập</title>
  {{-- favicon for login page --}}
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/Logo.png') }}">
  <link rel="shortcut icon" href="{{ asset('images/Logo.png') }}">
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-sky-50 flex items-center justify-center py-12 px-4">
  <div class="w-full max-w-md">
    <div class="bg-white/80 backdrop-blur-md shadow-2xl rounded-3xl overflow-hidden border border-gray-100">
      <div class="p-6 sm:p-8">
        <div class="flex items-center gap-4">
          <div class="w-14 h-14 flex items-center justify-center rounded-lg bg-gradient-to-br from-indigo-600 to-sky-500 text-white">
            <i class="ri-shield-user-line text-2xl"></i>
          </div>
          <div>
            <h1 class="text-2xl font-semibold text-slate-700">Bảng Quản trị</h1>
            <p class="text-sm text-slate-500">Đăng nhập để quản lý nội dung và phản duyệt</p>
          </div>
        </div>

        @if(session('success'))
          <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-md text-green-700 text-sm">
            {{ session('success') }}
          </div>
        @endif

        @if($errors->any())
          <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-md text-red-700 text-sm">
            @foreach($errors->all() as $error)
              <div>{{ $error }}</div>
            @endforeach
          </div>
        @endif

        <form action="{{ route('admin.login.post') }}" method="POST" class="mt-6">
          @csrf
          <div class="space-y-4">
            <label class="block text-sm font-medium text-slate-600">Email</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-slate-400"><i class="ri-mail-line"></i></span>
              <input name="email" type="email" required value="{{ old('email') }}" autofocus
                class="pl-10 pr-3 py-2.5 w-full rounded-lg border border-gray-200 bg-white/60 focus:outline-none focus:ring-2 focus:ring-indigo-300"
                placeholder="admin@example.com" />
            </div>

            <label class="block text-sm font-medium text-slate-600">Mật khẩu</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-slate-400"><i class="ri-lock-2-line"></i></span>
              <input name="mat_khau" type="password" required value=""
                class="pl-10 pr-3 py-2.5 w-full rounded-lg border border-gray-200 bg-white/60 focus:outline-none focus:ring-2 focus:ring-indigo-300"
                placeholder="••••••••" />
            </div>

            <div class="flex items-center text-sm">
              <label class="inline-flex items-center text-slate-600">
                <input type="checkbox" name="remember" class="form-checkbox h-4 w-4 text-indigo-600" {{ old('remember') ? 'checked' : '' }} />
                <span class="ml-2">Ghi nhớ</span>
              </label>
            </div>

            <button type="submit" class="mt-2 w-full py-2.5 rounded-lg bg-gradient-to-r from-indigo-600 to-sky-500 text-white font-semibold shadow hover:opacity-95">
              Đăng nhập
            </button>
          </div>
        </form>

        <div class="mt-6 text-center text-xs text-slate-400">
          <span>© {{ date('Y') }} NhàTrọ - Bảo trì bởi team nội bộ</span>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Small UX: focus first input on load
    document.addEventListener('DOMContentLoaded', function(){
      const el = document.querySelector('input[name="email"]');
      if(el) el.focus();
    });
  </script>
</body>
</html>
