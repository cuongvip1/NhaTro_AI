<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ApiClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\DiaChi;
use Illuminate\Support\Facades\Schema;

class AdminWebController extends Controller
{
    protected $api;

    public function __construct(ApiClient $api)
    {
        $this->api = $api;
    }

    private function normalizeImages(array $images): array
    {
        $apiBase = str_replace('/api', '', config('services.api.base'));
        foreach ($images as $k => $img) {
            $raw = is_array($img) ? $img : (array) $img;

            $url = $raw['url'] ?? $raw['path'] ?? $raw['link'] ?? $raw['file'] ?? '';
            $url = trim((string) $url);

            if ($url !== '') {
                if (preg_match('/^https?:\/\//i', $url)) {
                    // keep as-is
                } else {
                    $url = rtrim($apiBase, '/') . '/' . ltrim($url, '/');
                }
            } else {
                $url = null;
            }

            $raw['url'] = $url;
            $images[$k] = $raw;
        }

        return $images;
    }

    public function dashboard()
    {
        try {
            // Try to load stats from API first (if available), but always compute from local DB as fallback/source of truth
            $stats = [];
            try {
                $stats = $this->api->get('admin/dashboard');
            } catch (\Exception $e) {
                Log::info('admin/dashboard not available: ' . $e->getMessage());
            }

            // Compute local stats from database tables
            try {
                $now = now();

                $postsThisMonth = DB::table('bai_dang')
                    ->whereYear('ngay_tao', $now->year)
                    ->whereMonth('ngay_tao', $now->month)
                    ->count();

                $pending = DB::table('bai_dang')->where('trang_thai', 'cho_duyet')->count();
                $users = DB::table('nguoi_dung')->count();
                $regions = DB::table('dia_chi')->count();

                // quick stats
                $totalPosts = DB::table('bai_dang')->count();
                $approved = DB::table('bai_dang')->where('trang_thai', 'dang')->count();
                $hidden = DB::table('bai_dang')->where('trang_thai', 'tu_choi')->count();

                $local = [
                    'posts_this_month' => $postsThisMonth,
                    'dang_cho_duyet' => $pending,
                    'nguoi_dung' => $users,
                    'khu_vuc' => $regions,
                    'total_posts' => $totalPosts,
                    'approved' => $approved,
                    'hidden' => $hidden,
                ];

                // Merge API stats with local (local overrides missing keys)
                $stats = array_merge(is_array($stats) ? $stats : [], $local);
            } catch (\Exception $e) {
                Log::error('Error computing local dashboard stats: ' . $e->getMessage());
            }

            // Fetch recent posts that were most recently approved (ngay_duyet) from API if possible
            $recentPosts = [];
            try {
                $query = http_build_query([
                    'per_page' => 5,
                    // load recent posts regardless of status so admin can see all recent activity
                    'sort_by' => 'ngay_tao',
                    'sort_order' => 'desc',
                ]);
                $recentPosts = $this->api->get("admin/posts?{$query}");
            } catch (\Exception $e) {
                Log::info('Could not load recent posts for dashboard: ' . $e->getMessage());
            }

            return view('admin.tong-quan.index', compact('stats', 'recentPosts'));
        } catch (\Exception $e) {
            return view('admin.tong-quan.index', ['stats' => null, 'error' => $e->getMessage()]);
        }
    }

    public function posts(Request $request)
    {
        try {
            // sanitize sort_by to avoid table aliases that some API SQL builders may not accept
            $sortByRaw = $request->get('sort_by', 'ngay_tao');
            // remove any table prefix like 'bd.' so API receives a simple column name
            $sortBy = preg_replace('/^[^.]+\./', '', (string)$sortByRaw);

            $queryParams = [
                'per_page' => $request->get('per_page', 15),
                // note: do not forward 'trang_thai' by default to avoid unexpected API errors
                'q' => $request->get('q'),
                'chu_tro' => $request->get('chu_tro'),
                'dia_chi' => $request->get('dia_chi'),
                'sort_by' => $sortBy,
                'sort_order' => $request->get('sort_order', 'desc'),
                'page' => $request->get('page'),
            ];

            $response = $this->api->get('admin/posts', $queryParams);

            // Load owners (chủ trọ) and regions locally to populate filter selects
            $owners = DB::table('nguoi_dung')->where('vai_tro', 'chu_tro')->select('id', 'ho_ten')->orderBy('ho_ten')->get();
            $regions = DiaChi::orderBy('ten_dia_chi')->get();

            // Use Vietnamese-structured view for posts index
            return view('admin.bai-dang.index', [
                'posts' => $response,
                'owners' => $owners,
                'regions' => $regions,
            ]);
        } catch (\Exception $e) {
            return view('admin.bai-dang.index', ['posts' => null, 'owners' => collect(), 'regions' => collect(), 'error' => $e->getMessage()]);
        }
    }

    public function approvals(Request $request)
    {
        try {
            $tab = $request->get('tab', 'pending');
            $endpoint = $tab === 'rejected'
                ? 'admin/approvals/rejected'
                : 'admin/approvals/pending';

            $queryParams = [
                'per_page' => $request->get('per_page', 15),
                // only filter by district (dia_chi) and pagination for approvals UI
                'suspected' => $request->get('suspected') ? 1 : 0,
                'page' => $request->get('page'),
            ];

            // allow filtering by district (dia_chi)
            if ($request->filled('dia_chi')) {
                $queryParams['dia_chi'] = $request->get('dia_chi');
            }

            // Use ApiClient's query param support (pass array as second arg) so logs and requests
            // clearly show which query parameters are sent.
            $response = $this->api->get($endpoint, $queryParams);
            $stats = $this->api->get('admin/approvals/statistics');

            // Load local regions from DB to populate filter select
            $regions = DiaChi::orderBy('ten_dia_chi')->get();

            // Use Vietnamese-structured view for approvals
            return view('admin.phe-duyet.index', [
                'posts' => $response,
                'stats' => $stats,
                'tab' => $tab,
                'regions' => $regions,
            ]);
        } catch (\Exception $e) {
            return view('admin.phe-duyet.index', [
                'posts' => null,
                'stats' => null,
                'error' => $e->getMessage(),
                'regions' => collect(),
            ]);
        }
    }

    public function showApproval($id)
    {
        try {
            $resp = $this->api->get("admin/approvals/{$id}");
            $post = is_array($resp) && isset($resp['post']) ? $resp['post'] : $resp;
            if (is_array($resp)) {
                $images = $resp['images'] ?? [];
                $images = $this->normalizeImages(is_array($images) ? $images : (array) $images);

                if (!isset($post['anh_dai_dien']) || empty($post['anh_dai_dien'])) {
                    $first = $images[0]['url'] ?? null;
                    if ($first)
                        $post['anh_dai_dien'] = $first;
                }

                // Normalize a few fields expected by the Blade view so missing nested keys
                // from the API won't cause '---' to show up.
                $post = array_merge($post, [
                    'images' => $images,
                    'tien_ich' => $resp['tien_ich'] ?? [],
                    // owner name/email fallbacks
                    'chu_tro' => $post['chu_tro'] ?? data_get($post, 'chu_tro.ho_ten') ?? data_get($post, 'chuTro.ho_ten') ?? data_get($post, 'nguoi_dung.ho_ten') ?? ($post['tac_gia'] ?? null),
                    'sdt_chu_tro' => $post['sdt_chu_tro'] ?? data_get($post, 'chu_tro.so_dien_thoai') ?? data_get($post, 'chuTro.so_dien_thoai') ?? data_get($post, 'nguoi_dung.so_dien_thoai') ?? ($post['sdt'] ?? null),
                    // daytro / phong mappings
                    'ten_day_tro' => $post['ten_day_tro'] ?? data_get($post, 'phong.day_tro.ten_day_tro') ?? data_get($post, 'phong.dayTro.ten_day_tro') ?? data_get($post, 'day_tro.ten_day_tro') ?? data_get($post, 'ten_daytro'),
                    'so_phong' => $post['so_phong'] ?? data_get($post, 'phong.so_phong') ?? data_get($post, 'phong.soPhong') ?? null,
                    'dien_tich' => $post['dien_tich'] ?? data_get($post, 'phong.dien_tich') ?? null,
                    'tang' => $post['tang'] ?? data_get($post, 'phong.tang') ?? null,
                    'suc_chua' => $post['suc_chua'] ?? data_get($post, 'phong.suc_chua') ?? null,
                    'dia_chi' => $post['dia_chi'] ?? data_get($post, 'phong.day_tro.dia_chi') ?? data_get($post, 'phong.dayTro.dia_chi') ?? data_get($post, 'dia_chi') ?? null,
                    'gia_hien_thi' => $post['gia_hien_thi'] ?? (isset($post['gia_niem_yet']) ? number_format($post['gia_niem_yet'],0,',','.') . ' đ' : null),
                    'ngay_hien_thi' => $post['ngay_hien_thi'] ?? (isset($post['ngay_tao']) ? (is_string($post['ngay_tao']) ? date('d/m/Y', strtotime($post['ngay_tao'])) : $post['ngay_tao']->format('d/m/Y')) : null),
                ]);
            }

            // Use Vietnamese-structured view for approval detail
            return view('admin.phe-duyet.show', ['post' => $post]);
        } catch (\Exception $e) {
            return redirect()->route('admin.approvals')->with('error', 'Lỗi khi tải chi tiết: ' . $e->getMessage());
        }
    }

    public function approve($id)
    {
        try {
            $this->api->post("admin/approvals/{$id}/approve");
            return redirect()->route('admin.approvals')->with('success', 'Đã phê duyệt bài viết');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'ly_do' => 'required|string|max:500'
        ]);

        try {
            $this->api->post("admin/approvals/{$id}/reject", [
                'ly_do' => $request->ly_do
            ]);
            return redirect()->route('admin.approvals')->with('success', 'Đã từ chối bài viết');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function showPost($id)
    {
        try {
            $post = \DB::table('bai_dang as bd')
                ->join('phong as p', 'p.id', '=', 'bd.phong_id')
                ->join('day_tro as d', 'd.id', '=', 'p.day_tro_id')
                ->leftJoin('nguoi_dung as nd', 'nd.id', '=', 'd.chu_tro_id')
                ->select(
                    'bd.*',
                    'p.id as phong_id',
                    'p.so_phong',
                    'p.dien_tich',
                    'p.tang',
                    'p.suc_chua',
                    'p.trang_thai as trang_thai_phong',
                    'd.ten_day_tro',
                    'd.dia_chi',
                    'nd.ho_ten as chu_tro',
                    'nd.so_dien_thoai as sdt_chu_tro'
                )
                ->where('bd.id', $id)
                ->first();

            if (!$post) {
                return back()->with('error', 'Không tìm thấy bài đăng.');
            }

            $post->anh = \DB::table('anh_bai_dang')
                ->where('bai_dang_id', $id)
                ->orderBy('thu_tu')
                ->pluck('url')
                ->map(function ($u) {
                    $apiBase = config('services.api.base', 'http://127.0.0.1:8000/api');
                    $apiBase = str_replace('/api', '', $apiBase);

                    $u = ltrim($u, '/');
                    if (!str_starts_with($u, 'storage/')) {
                        $u = 'storage/' . $u;
                    }

                    return rtrim($apiBase, '/') . '/' . $u;
                })
                ->toArray();

            // 💡 Dịch vụ
            $post->dich_vu = \DB::table('dich_vu_dinh_ky as dvdk')
                ->join('dich_vu as dv', 'dv.id', '=', 'dvdk.dich_vu_id')
                ->where('dvdk.phong_id', $post->phong_id)
                ->select('dv.ten', 'dvdk.don_gia as gia', 'dv.don_vi')
                ->get()
                ->map(fn($dv) => [
                    'ten' => $dv->ten,
                    'gia' => (float) $dv->gia,
                    'don_vi' => $dv->don_vi,
                ]);

            // ⭐ Đánh giá
            $post->danh_gia = \DB::table('danh_gia as dg')
                ->join('hop_dong as hd', 'hd.id', '=', 'dg.hop_dong_id')
                ->join('nguoi_dung as nd', 'nd.id', '=', 'hd.khach_thue_id')
                ->where('hd.phong_id', $post->phong_id)
                ->select('nd.ho_ten as nguoi_danh_gia', 'dg.diem_so', 'dg.binh_luan', 'dg.ngay_tao')
                ->orderByDesc('dg.ngay_tao')
                ->get();

            // 📊 Thông tin hiển thị
            $post->rating = round(\DB::table('danh_gia as dg')
                ->join('hop_dong as hd', 'hd.id', '=', 'dg.hop_dong_id')
                ->where('hd.phong_id', $post->phong_id)
                ->avg('dg.diem_so'), 1);

            $post->gia_hien_thi = number_format($post->gia_niem_yet, 0, ',', '.') . ' đ/tháng';
            $post->ngay_hien_thi = date('d/m/Y', strtotime($post->ngay_tao));

            return view('admin.legacy.post-detail', ['post' => (array) $post]);
        } catch (\Throwable $e) {
            \Log::error('AdminWebController.showPost error: ' . $e->getMessage());
            return back()->with('error', 'Không thể lấy chi tiết bài đăng.');
        }
    }

    public function editPost($id)
    {
        if (!session('api_token')) {
            return redirect()->route('admin.login')->with('error', 'Bạn cần đăng nhập để chỉnh sửa bài viết');
        }
        try {
            $resp = $this->api->get("admin/posts/{$id}");
            $post = is_array($resp) && isset($resp['post']) ? $resp['post'] : $resp;
            // Debug/log raw API response to help diagnose missing id issues in dev
            try {
                Log::info('AdminWebController.editPost api raw response', ['requested_id' => $id, 'resp' => $resp]);
            } catch (\Throwable $e) {
                // ignore logging errors
            }
            if (is_array($resp)) {
                $images = $resp['images'] ?? [];
                $images = $this->normalizeImages(is_array($images) ? $images : (array) $images);

                if (!isset($post['anh_dai_dien']) || empty($post['anh_dai_dien'])) {
                    $first = $images[0]['url'] ?? null;
                    if ($first)
                        $post['anh_dai_dien'] = $first;
                }

                $post = array_merge($post, [
                    'images' => $images,
                    'tien_ich' => $resp['tien_ich'] ?? []
                ]);
            }
            // Ensure post is an array for the Blade view (blade expects array indexes)
            $post = is_array($post) ? $post : (array) $post;

            // Ensure post is an array for the Blade view (blade expects array indexes)
            $post = is_array($post) ? $post : (array) $post;

            // If API omitted the id for some reason, use the requested route id as a fallback.
            if (empty($post['id'])) {
                $post['id'] = $id;
            }

            // Log the final post keys for debugging
            try {
                Log::info('AdminWebController.editPost prepared post', ['requested_id' => $id, 'post_keys' => array_keys($post), 'post_id' => $post['id']]);
            } catch (\Throwable $e) {
                // ignore
            }

            // Use Vietnamese-structured view path
            return view('admin.bai-dang.edit', ['post' => $post]);
        } catch (\Exception $e) {
            return redirect()->route('admin.posts')->with('error', 'Lỗi khi tải dữ liệu sửa bài: ' . $e->getMessage());
        }
    }

    public function uploadPostImage(Request $request, $id)
    {
        if (!session('api_token')) {
            return redirect()->route('admin.login')->with('error', 'Bạn cần đăng nhập để thực hiện thao tác này');
        }

        $request->validate([
            'anh' => 'required',
            'anh.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096'
        ]);

        $files = is_array($request->file('anh')) ? $request->file('anh') : [$request->file('anh')];
        $saved = [];

        $thu_tu_base = DB::table('anh_bai_dang')->where('bai_dang_id', $id)->max('thu_tu');
        $thu_tu = is_numeric($thu_tu_base) ? (int) $thu_tu_base + 1 : 0;

        $hasCreatedAt = Schema::hasColumn('anh_bai_dang', 'created_at');

        foreach ($files as $file) {
            // store under storage/app/public/bai_dang/{post_id}/...
            $path = $file->store('bai_dang/' . $id, 'public');
            $url = '/storage/' . $path;

            $data = [
                'bai_dang_id' => $id,
                'url' => $url,
                'thu_tu' => $thu_tu,
            ];
            if ($hasCreatedAt) {
                $data['created_at'] = now();
            }

            $rowId = DB::table('anh_bai_dang')->insertGetId($data);

            $saved[] = ['id' => $rowId, 'url' => url($url), 'relative_url' => $url];
            $thu_tu++;
        }

        return redirect()->back()->with('success', 'Đã upload ' . count($saved) . ' ảnh');
    }

    public function updatePost(Request $request, $id)
    {
        $request->validate([
            'tieu_de' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'gia_niem_yet' => 'nullable|numeric',
            'trang_thai' => 'nullable|string|in:nhap,dang,cho_duyet,an,tu_choi'
        ]);

        try {
            $payload = [
                'tieu_de' => $request->tieu_de,
                'mo_ta' => $request->mo_ta,
            ];
            if ($request->filled('gia_niem_yet'))
                $payload['gia_niem_yet'] = $request->gia_niem_yet;
            if ($request->filled('trang_thai')) {
                $payload['trang_thai'] = $request->trang_thai;
            }

            Log::info('AdminWebController.updatePost called', ['id' => $id, 'payload' => $payload]);

            $res = $this->api->patch("admin/posts/{$id}", $payload);

            Log::info('AdminWebController.updatePost api response', ['id' => $id, 'response' => $res]);

            return redirect()->route('admin.posts')->with('success', 'Đã cập nhật bài viết');
        } catch (\Exception $e) {
            Log::error('AdminWebController.updatePost error', ['id' => $id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Lỗi khi cập nhật: ' . $e->getMessage());
        }
    }

    public function accounts(Request $request)
    {
        try {
            $queryParams = [
                'per_page' => $request->get('per_page', 15),
                'vai_tro' => $request->get('vai_tro'),
                'trang_thai' => $request->get('trang_thai'),
                'q' => $request->get('q'),
                'page' => $request->get('page'),
            ];

            $response = $this->api->get('admin/accounts', $queryParams);
            $stats = $this->api->get('admin/accounts/statistics');

            // Use Vietnamese-structured view for accounts
            return view('admin.tai-khoan.index', [
                'accounts' => $response,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            return view('admin.tai-khoan.index', [
                'accounts' => null,
                'stats' => null,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Hiển thị danh sách khu vực (dia_chi) từ database.
     */
    public function regions(Request $request)
    {
        try {
            $q = trim((string) $request->get('q', ''));

            // Get regions with count of posts that reference the same ten_dia_chi
            $query = DB::table('dia_chi as d')
                ->leftJoin('bai_dang as bd', 'bd.dia_chi', '=', 'd.ten_dia_chi')
                // table `dia_chi` only has id and ten_dia_chi - select ten_dia_chi as display
                ->select('d.id', 'd.ten_dia_chi', DB::raw('COUNT(bd.id) as post_count'))
                ->groupBy('d.id', 'd.ten_dia_chi')
                ->orderBy('d.ten_dia_chi');

            if ($q !== '') {
                $query->where('d.ten_dia_chi', 'like', "%{$q}%");
            }

            $regions = $query->get();

            return view('admin.cau-hinh.regions', ['regions' => $regions, 'q' => $q]);
        } catch (\Exception $e) {
            return view('admin.cau-hinh.regions', ['regions' => collect(), 'error' => $e->getMessage()]);
        }
    }

    /**
     * Store a new region (dia_chi).
     */
    public function storeRegion(Request $request)
    {
        $request->validate([
            'ten_dia_chi' => 'required|string|max:255'
        ]);

        try {
            $region = DiaChi::create([
                'ten_dia_chi' => $request->ten_dia_chi
            ]);

            return redirect()->route('admin.regions')->with('success', 'Đã thêm khu vực mới');
        } catch (\Exception $e) {
            \Log::error('Error creating region: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Lỗi khi thêm khu vực: ' . $e->getMessage());
        }
    }

    /**
     * Cập nhật tên khu vực
     */
    public function updateRegion(Request $request, $id)
    {
        $request->validate([
            'ten_dia_chi' => 'required|string|max:255'
        ]);

        try {
            $region = DiaChi::find($id);
            if (!$region) {
                return redirect()->route('admin.regions')->with('error', 'Không tìm thấy khu vực.');
            }

            $region->ten_dia_chi = $request->ten_dia_chi;
            $region->save();

            return redirect()->route('admin.regions')->with('success', 'Đã cập nhật khu vực');
        } catch (\Exception $e) {
            \Log::error('Error updating region: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Lỗi khi cập nhật khu vực: ' . $e->getMessage());
        }
    }

    /**
     * Xóa khu vực (nếu không có bài đăng tham chiếu)
     */
    public function deleteRegion($id)
    {
        try {
            $region = DiaChi::find($id);
            if (!$region) {
                return redirect()->route('admin.regions')->with('error', 'Không tìm thấy khu vực.');
            }

            // Kiểm tra nếu còn bài đăng tham chiếu theo tên khu vực
            $hasPosts = DB::table('bai_dang')->where('dia_chi', $region->ten_dia_chi)->exists();
            if ($hasPosts) {
                return redirect()->route('admin.regions')->with('error', 'Không thể xóa khu vực vì còn bài viết đang tham chiếu.');
            }

            $region->delete();

            return redirect()->route('admin.regions')->with('success', 'Đã xóa khu vực');
        } catch (\Exception $e) {
            \Log::error('Error deleting region: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Lỗi khi xóa khu vực: ' . $e->getMessage());
        }
    }

    public function changeAccountStatus(Request $request, $id)
    {
        try {
            $this->api->patch("admin/accounts/{$id}/status", [
                'trang_thai' => $request->trang_thai
            ]);
            return redirect()->route('admin.accounts')->with('success', 'Đã cập nhật trạng thái');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function changeAccountRole(Request $request, $id)
    {
        try {
            $this->api->patch("admin/accounts/{$id}/role", [
                'vai_tro' => $request->vai_tro
            ]);
            return redirect()->route('admin.accounts')->with('success', 'Đã cập nhật vai trò');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Update only the post status (trang_thai) without requiring full post payload.
     */
    public function changePostStatus(Request $request, $id)
    {
        $request->validate([
            'trang_thai' => 'required|string|in:nhap,dang,cho_duyet,an,tu_choi'
        ]);

        try {
            // The API's update endpoint requires a full payload (tieu_de is required).
            // Fetch the current post from API, merge the new status, then send a full update
            $resp = $this->api->get("admin/posts/{$id}");
            $post = is_array($resp) && isset($resp['post']) ? $resp['post'] : $resp;
            $post = is_array($post) ? $post : (array) $post;

            // Build payload ensuring required fields are present for the API validation
            $payload = [
                'tieu_de' => $post['tieu_de'] ?? '',
                'mo_ta' => $post['mo_ta'] ?? null,
            ];
            if (isset($post['gia_niem_yet'])) {
                $payload['gia_niem_yet'] = $post['gia_niem_yet'];
            }
            $payload['trang_thai'] = $request->trang_thai;

            // If API returned empty title, try loading from local DB as fallback
            if (empty($payload['tieu_de'])) {
                try {
                    $localTitle = DB::table('bai_dang')->where('id', $id)->value('tieu_de');
                    if (!empty($localTitle)) {
                        $payload['tieu_de'] = $localTitle;
                    } else {
                        return redirect()->back()->with('error', 'Không thể cập nhật trạng thái: tiêu đề bài viết trống. Vui lòng cập nhật tiêu đề trước.');
                    }
                } catch (\Throwable $e) {
                    return redirect()->back()->with('error', 'Lỗi khi truy vấn tiêu đề từ cơ sở dữ liệu: ' . $e->getMessage());
                }
            }

            \Log::info('AdminWebController.changePostStatus payload', ['id' => $id, 'payload' => $payload]);

            $this->api->patch("admin/posts/{$id}", $payload);

            return redirect()->route('admin.posts.show', $id)->with('success', 'Đã cập nhật trạng thái');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi cập nhật trạng thái: ' . $e->getMessage());
        }
    }

    public function deletePost($id)
    {
        try {
            $this->api->delete("admin/posts/{$id}");
            return redirect()->route('admin.posts')->with('success', 'Đã xóa bài viết');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function deleteAccount($id)
    {
        try {
            $this->api->delete("admin/accounts/{$id}");
            return redirect()->route('admin.accounts')->with('success', 'Đã xóa tài khoản');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}
