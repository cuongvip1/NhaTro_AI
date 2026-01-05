<?php

namespace App\Http\Controllers\ChuTro;

use App\Http\Controllers\Controller;
use App\Services\ApiClient;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected ApiClient $api;


    public function __construct(ApiClient $api)
    {
        $this->api = $api;
    }

    public function index(Request $request)
    {
        $this->api->setToken(session('api_token'));
        $stats = $this->api->get('chu-tro/dashboard');

        return view('chutro.dashboard', compact('stats'));
    }


}
