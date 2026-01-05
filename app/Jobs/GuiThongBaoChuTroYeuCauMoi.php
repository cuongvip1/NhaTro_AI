<?php

namespace App\Jobs;

use App\Mail\ThongBaoChuTroYeuCauMoi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class GuiThongBaoChuTroYeuCauMoi implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $chuTro, $khach, $phong, $dayTro;

    /**
     * Tạo job mới.
     */
    public function __construct($chuTro, $khach, $phong, $dayTro)
    {
        $this->chuTro = $chuTro;
        $this->khach = $khach;
        $this->phong = $phong;
        $this->dayTro = $dayTro;
    }

    /**
     * Xử lý job trong hàng đợi.
     */
    public function handle(): void
    {
        Mail::to($this->chuTro->email)->send(
            new ThongBaoChuTroYeuCauMoi(
                $this->chuTro,
                $this->khach,
                $this->phong,
                $this->dayTro
            )
        );
    }
}
