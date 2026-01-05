<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin - Đăng nhập</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-blue-50 grid place-items-center">
  <div class="w-full max-w-md bg-white shadow-xl rounded-2xl p-8 border border-gray-100">
    <div class="text-center mb-6">
      <div class="w-12 h-12 mx-auto rounded-full bg-indigo-600 text-white grid place-items-center"><i class="ri-shield-user-line text-2xl"></i></div>
      <h1 class="mt-3 text-xl font-semibold">Đăng nhập Quản trị</h1>
    </div>

    @if(session('success'))
      <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
        {{ session('success') }}
      </div>
    @endif

    @if($errors->any())
      <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
        @foreach($errors->all() as $error)
          <div>{{ $error }}</div>
        @endforeach
      </div>
    @endif

    <form action="{{ route('admin.login.post') }}" method="POST">
      @csrf
      <label class="block text-sm font-medium text-gray-700">Email</label>
      <input name="email" type="email" required value="{{ old('email', 'admin@test.com') }}" 
             class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500" 
             placeholder="admin@example.com" />
      
      <label class="block text-sm font-medium text-gray-700 mt-4">Mật khẩu</label>
      <input name="mat_khau" type="password" required value="admin123"
             class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500" 
             placeholder="••••••••" />
      
      <button type="submit" class="mt-6 w-full py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-medium">
        Đăng nhập
      </button>
    </form>

    <div class="mt-4 text-center text-sm text-gray-500">
      <p>Tài khoản test:</p>
      <p class="text-xs mt-1">Email: admin@test.com / Mật khẩu: admin123</p>
    </div>
  </div>
</body>
</html>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin - Đăng nhập</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-blue-50 grid place-items-center">
  <div class="w-full max-w-md bg-white shadow-xl rounded-2xl p-8 border border-gray-100">
    <div class="text-center mb-6">
      <div class="w-12 h-12 mx-auto rounded-full bg-indigo-600 text-white grid place-items-center"><i class="ri-shield-user-line text-2xl"></i></div>
      <h1 class="mt-3 text-xl font-semibold">Đăng nhập Quản trị</h1>
    </div>

    @if(session('success'))
      <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
        {{ session('success') }}
      </div>
    @endif

    @if($errors->any())
      <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
        @foreach($errors->all() as $error)
          <div>{{ $error }}</div>
        @endforeach
      </div>
    @endif

    <form action="{{ route('admin.login.post') }}" method="POST">
      @csrf
      <label class="block text-sm font-medium text-gray-700">Email</label>
      <input name="email" type="email" required value="{{ old('email', 'admin@test.com') }}" 
             class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500" 
             placeholder="admin@example.com" />
      
      <label class="block text-sm font-medium text-gray-700 mt-4">Mật khẩu</label>
      <input name="mat_khau" type="password" required value="admin123"
             class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500" 
             placeholder="••••••••" />
      
      <button type="submit" class="mt-6 w-full py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-medium">
        Đăng nhập
      </button>
    </form>

    <div class="mt-4 text-center text-sm text-gray-500">
      <p>Tài khoản test:</p>
      <p class="text-xs mt-1">Email: admin@test.com / Mật khẩu: admin123</p>
    </div>
  </div>
</body>
</html>
