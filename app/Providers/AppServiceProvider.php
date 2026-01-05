<?php

namespace App\Providers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\ThongBao;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    /* public function boot(): void
     {
         View::composer('*', function ($view) {
             if (Auth::check()) {
                 $userId = Auth::id();

                 $thong_bao = ThongBao::where('nguoi_nhan_id', $userId)
                     ->orderBy('ngay_tao', 'desc')
                     ->take(5)
                     ->get();

                 $thong_bao_chua_xem = ThongBao::where('nguoi_nhan_id', $userId)
                     ->where('da_xem', 0)
                     ->count();

                 $view->with(compact('thong_bao', 'thong_bao_chua_xem'));
             }
         });
     }*/
    public function boot(): void
    {
        \Log::info('✅ AppServiceProvider::boot chạy rồi');

        // ⚡ Nạp thông báo cho TẤT CẢ các view của chủ trọ
        \View::composer('layouts.chu-tro', function ($view) {
    if (\Auth::check() && \Auth::user()->vai_tro === 'chu_tro') {
        $user = \Auth::user();

        $thong_bao = ThongBao::where('nguoi_nhan_id', $user->id)
            ->orderByDesc('ngay_tao')
            ->take(10)
            ->get();

        $thong_bao_chua_xem = ThongBao::where('nguoi_nhan_id', $user->id)
            ->where('da_xem', 0)
            ->count();

        $view->with(compact('thong_bao', 'thong_bao_chua_xem'));
    }
});

    }


}
