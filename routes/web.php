<?php

use App\Http\Middleware\EnsureWebAuthenticated;
use App\Http\Middleware\EnsureAdminAuthenticated;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\AuthWebController;
use App\Http\Controllers\ChuTroController;
use App\Http\Controllers\ChuTro\ProfileController;
use App\Http\Controllers\ChuTro\DayTroController;
use App\Http\Controllers\ChuTro\PhongController;
use App\Http\Controllers\ChuTro\HopDongController;
use App\Http\Controllers\ChuTro\YeuCauThueController;
use App\Http\Controllers\ChuTro\KhachThueController;
use App\Http\Controllers\ChuTro\BaiDangWebController;
use App\Http\Controllers\ChuTro\ChisoController;
use App\Http\Controllers\ChuTro\HoadonController;
use App\Http\Controllers\ChuTro\NguoiThanViewController;
use App\Http\Controllers\ChuTro\ThongBaoController;
use App\Http\Controllers\ChuTro\DichVuController;
use App\Http\Controllers\ChuTro\DichVuDinhKyController;
use App\Http\Controllers\ChuTro\TienIchController;
use App\Http\Controllers\Admin\AdminWebController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\YeuThichWebController;
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| 🌐 PUBLIC ROUTES (Trang công khai)
|--------------------------------------------------------------------------
| Trang chủ, danh sách bài đăng, chi tiết phòng
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/bai-dang', [PublicController::class, 'listing'])->name('listing');
Route::post('/de-xuat-phong', [PublicController::class, 'recommend'])->name('listing.recommend');
Route::get('/bai-dang/{id}', [PublicController::class, 'detail'])->name('room.detail');
// Standalone search panel (moved off the homepage)
Route::view('/search-panel', 'public.search-panel')->name('search.panel');



/*
|--------------------------------------------------------------------------
| 👤 AUTH ROUTES (Đăng nhập / Đăng ký / Đăng xuất)
|--------------------------------------------------------------------------
*/
Route::get('/register', [AuthWebController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthWebController::class, 'register'])->name('register.post');


Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthWebController::class, 'login']);

Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');

Route::get('/auth/google/callback-handler', function (Request $request) {
    if ($request->has('google_error')) {
        $msg = base64_decode($request->query('google_error'));
        return redirect()->route('login')->with('error', $msg);
    }

    $token = $request->query('token');
    $encodedUser = $request->query('user');

    if (!$token || !$encodedUser) {
        return redirect()->route('login')->with('error', 'Không nhận được token hoặc user từ API.');
    }

    $userData = json_decode(base64_decode($encodedUser), true);

    session([
        'api_token' => $token,
        'user'      => $userData,  
    ]);

    return redirect()
        ->route('khach-thue.dashboard')
        ->with('ok', 'Đăng nhập Google thành công!');
})->name('auth.google.callback.handler');

/*
|--------------------------------------------------------------------------
| 🏠 CHỦ TRỌ (OWNER PANEL)
|--------------------------------------------------------------------------
| Các route dành riêng cho chủ trọ — yêu cầu đăng nhập
|--------------------------------------------------------------------------
*/
Route::middleware([EnsureWebAuthenticated::class])
    ->prefix('chu-tro')
    ->name('chu-tro.')
    ->group(function () {
        Route::redirect('/', '/chu-tro/dashboard')->name('index');

        Route::get('/dashboard', [ChuTroController::class, 'dashboard'])->name('dashboard');


        //Route::get('/create', fn() => redirect()->route('chu-tro.bai-dang.create'))->name('create');
        Route::get('/khach-thue/{id}', [KhachThueController::class, 'show'])
            ->name('api.khach-thue.show');


        // Quản lý dãy trọ
        Route::resource('day-tro', DayTroController::class)
            ->names('day-tro')
            ->parameters(['day-tro' => 'id']);
        Route::get('/chu-tro/day-tro/{id}', [DayTroController::class, 'show'])
            ->name('chu-tro.day-tro.show');


        /*
       |--------------------------------------------------------------------------
       | Quản lý Phòng
       |--------------------------------------------------------------------------
       */
        Route::get('/phong', [PhongController::class, 'index'])->name('phong.index');
        Route::get('/phong/tao', [PhongController::class, 'create'])->name('phong.create');
        Route::post('/phong', [PhongController::class, 'store'])->name('phong.store');
        Route::get('/phong/{id}', [PhongController::class, 'show'])->name('phong.show');
        Route::get('/phong/{id}/sua', [PhongController::class, 'edit'])->name('phong.edit');
        Route::put('/phong/{id}', [PhongController::class, 'update'])->name('phong.update');
        Route::delete('/phong/{id}', [PhongController::class, 'destroy'])->name('phong.destroy');

        // Quản lý Hợp đồng
        Route::get('/hop-dong', [HopDongController::class, 'index'])->name('hop-dong.index');
        Route::get('/hop-dong/tao', [HopDongController::class, 'create'])->name('hop-dong.create');
        Route::post('/hop-dong', [HopDongController::class, 'store'])->name('hop-dong.store');
        Route::get('/hop-dong/{id}', [HopDongController::class, 'show'])->name('hop-dong.show');
        Route::delete('/hop-dong/{id}', [HopDongController::class, 'destroy'])->name('hop-dong.destroy');
        Route::get('/hop-dong/{id}/sua', [HopDongController::class, 'edit'])->name('hop-dong.edit');
        Route::put('/hop-dong/{id}', [HopDongController::class, 'update'])->name('hop-dong.update');

        // KHÁCH THUÊ
        Route::get('/khachthue', [KhachThueController::class, 'index'])->name('khachthue.index');
        Route::get('/khachthue/tao', [KhachThueController::class, 'create'])->name('khachthue.create');
        Route::post('/khachthue', [KhachThueController::class, 'store'])->name('khachthue.store');
        Route::get('/khachthue/{id}', [KhachThueController::class, 'show'])->name('khachthue.show');
        Route::get('/khachthue/{id}/sua', [KhachThueController::class, 'edit'])->name('khachthue.edit');
        Route::put('/khachthue/{id}', [KhachThueController::class, 'update'])->name('khachthue.update');
        Route::delete('/khachthue/{id}', [KhachThueController::class, 'destroy'])->name('khachthue.destroy');

        Route::get('dich-vu', [DichVuController::class, 'index'])->name('dich-vu.index');
        Route::get('dich-vu/create', [DichVuController::class, 'create'])->name('dich-vu.create');
        Route::post('dich-vu', [DichVuController::class, 'store'])->name('dich-vu.store');
        Route::get('dich-vu/{id}/edit', [DichVuController::class, 'edit'])->name('dich-vu.edit');
        Route::put('dich-vu/{id}', [DichVuController::class, 'update'])->name('dich-vu.update');
        Route::delete('dich-vu/{id}', [DichVuController::class, 'destroy'])->name('dich-vu.destroy');

        // Quản lý tiện ích
        Route::get('tien-ich', [TienIchController::class, 'index'])->name('tien-ich.index');
        Route::post('tien-ich', [TienIchController::class, 'store'])->name('tien-ich.store');
        Route::delete('tien-ich/{id}', [TienIchController::class, 'destroy'])->name('tien-ich.destroy');

        // Gán tiện ích cho phòng
        Route::get('tien-ich/phong/{id}', [TienIchController::class, 'phong'])->name('tien-ich.phong');
        Route::post('tien-ich/phong/{id}', [TienIchController::class, 'ganTienIch'])->name('tien-ich.gan');
        // Quản lý dịch vụ định kỳ
        Route::get('dich-vu-dinh-ky', [DichVuDinhKyController::class, 'index'])->name('dichvu-dinhky.index');
        Route::get('dich-vu-dinh-ky/{phong_id}', [DichVuDinhKyController::class, 'show'])->name('dichvu-dinhky.show');
        Route::post('dich-vu-dinh-ky/{phong_id}', [DichVuDinhKyController::class, 'store'])->name('dichvu-dinhky.store');
        Route::put('/dich-vu-dinh-ky/{id}', [DichVuDinhKyController::class, 'update']);
        Route::delete('dich-vu-dinh-ky/{id}', [DichVuDinhKyController::class, 'destroy'])->name('dichvu-dinhky.destroy');
        // Quản lý bài đăng
        Route::get('/bai-dang', [BaiDangWebController::class, 'index'])->name('bai-dang.index');
        Route::get('/bai-dang/tao', [BaiDangWebController::class, 'create'])->name('bai-dang.create');
        Route::post('/bai-dang', [BaiDangWebController::class, 'store'])->name('bai-dang.store');
        Route::get('/bai-dang/{id}', [BaiDangWebController::class, 'show'])->name('bai-dang.show');
        Route::get('/bai-dang/{id}/sua', [BaiDangWebController::class, 'edit'])->name('bai-dang.edit');
        Route::put('/bai-dang/{id}', [BaiDangWebController::class, 'update'])->name('bai-dang.update');

        // Ẩn / hiện / xóa bài đăng
        Route::post('/bai-dang/{id}/toggle', [BaiDangWebController::class, 'toggle'])->name('bai-dang.toggle');
        Route::delete('/bai-dang/{id}', [BaiDangWebController::class, 'destroy'])->name('bai-dang.destroy');

        // Các trang placeholder tạm thời
        //Route::view('/khach', 'chu-tro.placeholder')->name('khach.index');
        //Route::view('/hoa-don', 'chu-tro.placeholder')->name('hoa-don.index');
        Route::view('/thong-ke', 'chu-tro.placeholder')->name('thong-ke.index');
        //Route::view('/nguoi-than', 'chu-tro.placeholder')->name('nguoi-than.index');
        //Route::view('/yeu-cau-thue', 'chu-tro.placeholder')->name('yeu-cau-thue.index');
        // Route::view('/hop-dong', 'chu-tro.placeholder')->name('hop-dong.index');
        //Route::view('/dien-nuoc', 'chu-tro.placeholder')->name('dien-nuoc.index');
        //Route::view('dich-vu', 'chu-tro.placeholder')->name('dich-vu.index');
    

        // Ảnh bài đăng
        Route::post('/bai-dang/{id}/anh', [ChuTroController::class, 'upload'])->name('bai-dang.upload');

        //  Yêu cầu thuê
        Route::prefix('yeu-cau-thue')->name('yeu-cau-thue.')->group(function () {
            // Danh sách yêu cầu thuê của chủ trọ
            Route::get('/', [YeuCauThueController::class, 'index'])->name('index');

            // Xem chi tiết yêu cầu thuê (mở modal hoặc trang riêng)
            Route::get('/{id}', [YeuCauThueController::class, 'show'])->name('show');

            // Chủ trọ chấp nhận yêu cầu → tự động tạo hợp đồng và gửi mail cho khách
            Route::post('/{id}/chap-nhan', [YeuCauThueController::class, 'chapNhan'])->name('chap-nhan');

            // Chủ trọ từ chối yêu cầu → gửi mail thông báo cho khách
            Route::post('/{id}/tu-choi', [YeuCauThueController::class, 'tuChoi'])->name('tu-choi');
            Route::get('/{id}/xem-hop-dong', 
    [YeuCauThueController::class, 'xemHopDong']
)->name('xem-hop-dong');

        });


        //Thông báo
        Route::get('/thong-bao', [ThongBaoController::class, 'index'])->name('thong-bao.index');
        Route::get('/thong-bao/{id}/xem', [ThongBaoController::class, 'daXem'])->name('thong-bao.xem');
        Route::post('/thong-bao/{id}/mark-as-read', [ThongBaoController::class, 'markAsRead'])
            ->name('thong-bao.mark-as-read');
        Route::post('/thong-bao/mark-all-read', [ThongBaoController::class, 'markAllRead'])
            ->name('thong-bao.mark-all-read');
        Route::delete('/thong-bao/xoa-da-doc', [ThongBaoController::class, 'xoaDaDoc'])
            ->name('thong-bao.xoa-da-doc');

        //Chỉ số-hóa đơn
        Route::get('/chiso', [ChisoController::class, 'index'])->name('dien-nuoc.index');
        Route::get('/hoadon', [HoadonController::class, 'index'])->name('hoa-don.index');
        Route::get('/hoa-don/export/excel', [HoadonController::class, 'export'])->name('hoa-don.export');


        Route::get('/hoa-don/{id}', [HoadonController::class, 'show'])->name('hoa-don.show');

        //Người thân (sống cùng khách thuê)
        Route::prefix('nguoi-than')->name('nguoi-than.')->group(function () {
            Route::get('/', [NguoiThanViewController::class, 'listAll'])->name('index');

            Route::get('/{khachThueId}', [NguoiThanViewController::class, 'index'])->name('byKhach');
            Route::get('/{khachThueId}/tao', [NguoiThanViewController::class, 'create'])->name('create');
            Route::post('/', [NguoiThanViewController::class, 'store'])->name('store');
            Route::get('/{id}/{khachThueId}/sua', [NguoiThanViewController::class, 'edit'])->name('edit');
            Route::put('/{id}/{khachThueId}', [NguoiThanViewController::class, 'update'])->name('update');
            Route::delete('/{id}/{khachThueId}', [NguoiThanViewController::class, 'destroy'])->name('destroy');
        });



        /*
        |--------------------------------------------------------------------------
        | 👤 HỒ SƠ CÁ NHÂN
        |--------------------------------------------------------------------------
        */
        Route::get('/ho-so', [ProfileController::class, 'show'])->name('profile.show');
        Route::post('/ho-so', [ProfileController::class, 'update'])->name('profile.update');
        Route::get('/profile/bank', [ProfileController::class, 'bankInfo'])->name('profile.bank');
        Route::post('/profile/bank', [ProfileController::class, 'updateBank'])->name('profile.bank.update');

    });

/*
|--------------------------------------------------------------------------
| 🧍 KHÁCH THUÊ (TENANT PANEL)
|--------------------------------------------------------------------------
| Các route dành riêng cho người thuê trọ (tenant)
|--------------------------------------------------------------------------
*/

Route::middleware([EnsureWebAuthenticated::class])
    ->prefix('khach-thue')
    ->name('khach-thue.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\KhachThue\DashboardController::class, 'index'])->name('dashboard');

        // Phòng trọ
        Route::get('/phong', [App\Http\Controllers\KhachThue\PhongController::class, 'index'])->name('phong.index');
        Route::get('/phong/{id}', [App\Http\Controllers\KhachThue\PhongController::class, 'show'])->name('phong.show');

        // Yêu cầu thuê
        Route::get('/yeu-cau-thue', [App\Http\Controllers\KhachThue\YeuCauThueController::class, 'index'])->name('yeu-cau-thue.index');
        Route::post('/yeu-cau-thue', [App\Http\Controllers\KhachThue\YeuCauThueController::class, 'store'])
            ->name('yeu-cau-thue.store');
        Route::delete('/yeu-cau-thue/{id}/huy', [App\Http\Controllers\KhachThue\YeuCauThueController::class, 'huy'])->name('yeu-cau-thue.huy');
        // Yêu thích
        Route::get('/yeu-thich', [App\Http\Controllers\YeuThichWebController::class, 'index'])->name('yeu-thich');
        Route::post('/yeu-thich/{baiDangId}', [YeuThichWebController::class, 'add'])->name('yeu-thich.add');
        Route::delete('/yeu-thich/{baiDangId}', [YeuThichWebController::class, 'remove'])->name('yeu-thich.remove');

        // Hợp đồng
        Route::get('/hop-dong', [App\Http\Controllers\KhachThue\HopDongController::class, 'index'])->name('hop-dong.index');
        Route::get('/hop-dong/{id}', [App\Http\Controllers\KhachThue\HopDongController::class, 'show'])->name('hop-dong.show');

        // Hóa đơn
        Route::get('/hoa-don', [App\Http\Controllers\KhachThue\HoaDonController::class, 'index'])->name('hoa-don.index');
        Route::get('/hoa-don/{id}', [App\Http\Controllers\KhachThue\HoaDonController::class, 'show'])->name('hoa-don.show');

        // Đánh giá
        Route::get('/danh-gia', [App\Http\Controllers\KhachThue\DanhGiaController::class, 'index'])->name('danh-gia.index');
        Route::post('/danh-gia', [App\Http\Controllers\KhachThue\DanhGiaController::class, 'store'])->name('danh-gia.store');

        // Thông báo
        Route::get('/thong-bao', [App\Http\Controllers\KhachThue\ThongBaoController::class, 'index'])
            ->name('thong-bao.index');

        Route::post('/thong-bao/{id}/mark-as-read', [App\Http\Controllers\KhachThue\ThongBaoController::class, 'markAsRead'])
            ->name('thong-bao.mark-as-read');

        Route::post('/thong-bao/mark-all-read', [App\Http\Controllers\KhachThue\ThongBaoController::class, 'markAllRead'])
            ->name('thong-bao.mark-all-read');

        Route::delete('/thong-bao/xoa-da-doc', [App\Http\Controllers\KhachThue\ThongBaoController::class, 'xoaDaDoc'])
            ->name('thong-bao.xoa-da-doc');

        // Hồ sơ cá nhân
        Route::get('/ho-so', [App\Http\Controllers\KhachThue\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/ho-so', [App\Http\Controllers\KhachThue\ProfileController::class, 'update'])->name('profile.update');
    });


/*
|--------------------------------------------------------------------------
| 🧑‍💼 ADMIN DASHBOARD (tùy chọn)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Trang đăng nhập admin
        Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        // Các route quản trị yêu cầu đăng nhập admin
        Route::middleware([EnsureAdminAuthenticated::class])->group(function () {
            // Dashboard và các trang quản trị
            Route::get('/', fn() => redirect()->route('admin.dashboard'));
            Route::get('/dashboard', [AdminWebController::class, 'dashboard'])->name('dashboard');

            // Quản lý Bài viết
            Route::get('/posts', [AdminWebController::class, 'posts'])->name('posts');
            Route::get('/posts/{id}', [AdminWebController::class, 'showPost'])->name('posts.show');
            Route::get('/posts/{id}/edit', [AdminWebController::class, 'editPost'])->name('posts.edit');
            Route::post('/posts/{id}/upload', [AdminWebController::class, 'uploadPostImage'])->name('posts.upload');
            Route::patch('/posts/{id}', [AdminWebController::class, 'updatePost'])->name('posts.update');
            // Update only status
            Route::patch('/posts/{id}/status', [AdminWebController::class, 'changePostStatus'])->name('posts.status');
            Route::delete('/posts/{id}', [AdminWebController::class, 'deletePost'])->name('posts.delete');

            // Xét duyệt
            Route::get('/approvals', [AdminWebController::class, 'approvals'])->name('approvals');
            Route::get('/approvals/{id}', [AdminWebController::class, 'showApproval'])->name('approvals.show');
            Route::post('/approvals/{id}/approve', [AdminWebController::class, 'approve'])->name('approvals.approve');
            Route::post('/approvals/{id}/reject', [AdminWebController::class, 'reject'])->name('approvals.reject');

            // Quản lý tài khoản
            Route::get('/accounts', [AdminWebController::class, 'accounts'])->name('accounts');
            Route::patch('/accounts/{id}/status', [AdminWebController::class, 'changeAccountStatus'])->name('accounts.status');
            Route::patch('/accounts/{id}/role', [AdminWebController::class, 'changeAccountRole'])->name('accounts.role');
            Route::delete('/accounts/{id}', [AdminWebController::class, 'deleteAccount'])->name('accounts.delete');

            // Khu vực (đã di chuyển sang admin/cau-hinh)
            Route::get('/regions', [AdminWebController::class, 'regions'])->name('regions');
            // Thêm khu vực
            Route::post('/regions', [AdminWebController::class, 'storeRegion'])->name('regions.store');
            // Cập nhật và xóa khu vực
            Route::put('/regions/{id}', [AdminWebController::class, 'updateRegion'])->name('regions.update');
            Route::delete('/regions/{id}', [AdminWebController::class, 'deleteRegion'])->name('regions.destroy');

            // Phân quyền (đã di chuyển sang admin/cau-hinh)
            Route::get('/permissions', fn() => view('admin.cau-hinh.permissions'))->name('permissions');
        });
    });
