<?php

namespace App\Http\Controllers;

use App\Services\ApiClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use App\Models\BaiDang;
class PublicController extends Controller
{
    private ApiClient $api;

    public function __construct(ApiClient $api)
    {
        $this->api = $api;
    }

    /** Trang chủ */
    public function home()
    {
        // Load simple statistics from local DB tables
        try {
            $phongCount = DB::table('phong')->count();
        } catch (\Throwable $e) {
            Log::warning('Không thể đọc bảng `phong`: ' . $e->getMessage());
            $phongCount = 0;
        }

        try {
            $danhgiaCount = DB::table('danh_gia')->count();
        } catch (\Throwable $e) {
            Log::warning('Không thể đọc bảng `danh_gia`: ' . $e->getMessage());
            $danhgiaCount = 0;
        }

        try {
            $diachiCount = DB::table('dia_chi')->count();
        } catch (\Throwable $e) {
            Log::warning('Không thể đọc bảng `dia_chi`: ' . $e->getMessage());
            $diachiCount = 0;
        }

        // Trang chủ hiển thị danh sách bài đăng giống trang "Phòng trọ" nhưng rút gọn.
        // Ưu tiên các phòng vừa được cập nhật (ngay_cap_nhat mới nhất).
        $featured = $this->loadLatestPosts();

        return view('public.home', compact('phongCount', 'danhgiaCount', 'diachiCount', 'featured'));
    }

    /**
     * Normalize images for a single post array to ensure keys 'anh' (array of urls)
     * and 'anh_dai_dien' (first url) are present and consistent with detail page.
     */
    private function normalizePostImages(array $post, bool $allowApiLookup = true): array
    {
        $normalized = [];
        $rawImages = [];

        if (!empty($post['anh'])) {
            $raw = $post['anh'];
            if (!is_array($raw)) {
                $decoded = @json_decode($raw, true);
                if (is_array($decoded)) $rawImages = $decoded;
                elseif (is_string($raw)) $rawImages = [$raw];
            } else {
                $rawImages = $raw;
            }
        }

        if (empty($rawImages) && !empty($post['anh_dai_dien'])) {
            $rawImages = [$post['anh_dai_dien']];
        }

        // Prefer the API detail endpoint first (the API returns asset('storage/...') URLs
        // which match the detail page exactly). If API doesn't provide images, then
        // fall back to reading local DB tables (anh_bai_dang / anh_bai_viet).
        if (empty($rawImages) && !empty($post['id'])) {
            $postId = intval($post['id']);
            try {
                // 1) Try API detail first
                if ($allowApiLookup) {
                    try {
                        $resp = $this->api->get("bai-dang/{$postId}");
                        Log::debug('normalizePostImages: API detail response', ['post_id' => $postId, 'response' => $resp]);
                        if (isset($resp['status']) && $resp['status'] && !empty($resp['data'])) {
                            $data = $resp['data'];
                            Log::debug('normalizePostImages: API detail data fields', ['post_id' => $postId, 'has_anh' => isset($data['anh']), 'has_anh_dai_dien' => isset($data['anh_dai_dien'])]);

                            if (!empty($data['anh'])) {
                                if (is_array($data['anh'])) $rawImages = array_merge($rawImages, $data['anh']);
                                else {
                                    $dec = @json_decode($data['anh'], true);
                                    if (is_array($dec)) $rawImages = array_merge($rawImages, $dec);
                                    elseif (is_string($data['anh'])) $rawImages[] = $data['anh'];
                                }
                            } elseif (!empty($data['anh_dai_dien'])) {
                                $rawImages[] = $data['anh_dai_dien'];
                            }

                            if (!empty($rawImages)) {
                                Log::debug('normalizePostImages: sourced images from API detail', ['post_id' => $postId, 'count' => count($rawImages), 'raw' => $rawImages]);
                            }
                        }
                    } catch (\Throwable $e) {
                        Log::debug('normalizePostImages: API bai-dang lookup failed: ' . $e->getMessage());
                    }
                }

                // 2) If API returned nothing, try DB `anh_bai_dang` / `anh_bai_viet`
                if (empty($rawImages)) {
                    try {
                        $candidates = [
                            ['table' => 'anh_bai_dang', 'col' => 'bai_dang_id'],
                            ['table' => 'anh_bai_dang', 'col' => 'bai_dang_ai'],
                            ['table' => 'anh_bai_viet', 'col' => 'bai_dang_id'],
                            ['table' => 'anh_bai_viet', 'col' => 'bai_viet_id'],
                            ['table' => 'anh_bai_dang', 'col' => 'bai_viet_id'],
                        ];

                        $urls = [];
                        $sourcedFrom = null;

                        foreach ($candidates as $c) {
                            try {
                                $table = $c['table'];
                                $col = $c['col'];
                                $res = DB::table($table)
                                    ->where($col, $postId)
                                    ->orderBy('thu_tu')
                                    ->pluck('url')
                                    ->toArray();

                                if (!empty($res)) {
                                    $urls = $res;
                                    $sourcedFrom = $table . '.' . $col;
                                    break;
                                }
                            } catch (\Throwable $_e) {
                                continue;
                            }
                        }

                        foreach ($urls as $u) {
                            if (!empty($u)) $rawImages[] = $u;
                        }

                        if (!empty($rawImages)) {
                            Log::debug('normalizePostImages: sourced images from DB', ['post_id' => $postId, 'count' => count($rawImages), 'source' => $sourcedFrom, 'raw_urls' => $urls]);
                        }
                    } catch (\Throwable $e) {
                        Log::debug('normalizePostImages: anh_bai_* lookup failed: ' . $e->getMessage());
                    }
                }

            } catch (\Throwable $e) {
                Log::debug('normalizePostImages: unexpected failure while sourcing images: ' . $e->getMessage());
            }
        }

        $apiBase = str_replace('/api', '', config('services.api.base'));

        foreach ($rawImages as $entry) {
            $url = null;
            if (is_array($entry)) {
                $url = $entry['url'] ?? $entry['path'] ?? $entry['file'] ?? null;
            } elseif (is_string($entry)) {
                $url = $entry;
            }
            if (!$url) continue;
            $url = trim((string)$url);

            if (preg_match('/^https?:\/\//i', $url)) {
                $final = $url;
            } else {
                $checkPath = ltrim($url, '/');
                if (file_exists(public_path('storage/' . $checkPath))) {
                    // file exists in this app's public/storage -> serve locally
                    $final = asset('storage/' . $checkPath);
                } else {
                    // file not in this app's public. The images for posts are stored in the API
                    // app under its public/storage/bai_dang path. Build a URL that points to
                    // the API's public storage. Handle cases like:
                    //  - 'storage/bai_dang/xxx.jpg'
                    //  - 'bai_dang/xxx.jpg' (common)
                    //  - 'public/storage/bai_dang/xxx.jpg'
                    $u = $url;
                    // also check sibling paths on disk (when API project is on the same host)
                    $foundInApiStorage = false;
                    $matchedCandidate = null;
                    $candidates = [
                        base_path('../../NhaTro/nha-tro-api/public/storage/' . $checkPath),
                        base_path('../../NhaTro/nha-tro-api/public/' . $checkPath),
                        base_path('../../nha-tro-api/public/storage/' . $checkPath),
                        base_path('../../nha-tro-api/public/' . $checkPath),
                    ];
                    foreach ($candidates as $cand) {
                        if ($cand && file_exists($cand)) {
                            $foundInApiStorage = true;
                            $matchedCandidate = $cand;
                            break;
                        }
                    }
                    // strip leading 'public/' if present
                    if (str_starts_with($u, 'public/')) {
                        $u = substr($u, strlen('public/'));
                    }
                    // if it doesn't already include 'storage/', but references bai_dang, prefix storage
                    if (!str_contains($u, 'storage/') && str_contains($u, 'bai_dang')) {
                        $u = 'storage/' . ltrim($u, '/');
                    }

                    // If we detected the file in the sibling API project's storage, prefer
                    // building a URL to the API's storage path (same result), otherwise still
                    // build the API URL (covers remote API servers).
                    if (str_starts_with($u, '/')) {
                        $final = rtrim($apiBase, '/') . $u;
                    } else {
                        $final = rtrim($apiBase, '/') . '/' . ltrim($u, '/');
                    }

                    // Log if we found a match in the sibling API storage path
                    if (!empty($matchedCandidate)) {
                        Log::debug('normalizePostImages: matched file in API project storage', ['post_id' => $postId, 'candidate' => $matchedCandidate, 'final_url' => $final]);
                    } else {
                        Log::debug('normalizePostImages: built API storage url', ['post_id' => $postId, 'final_url' => $final]);
                    }
                }
            }
            $normalized[] = $final;
        }

        if (count($normalized) === 0) {
            $normalized[] = asset('upload/room1.jpg');
        }

        $post['anh'] = $normalized;
        $post['anh_dai_dien'] = $normalized[0] ?? asset('upload/room1.jpg');

        // Debug: log final image URLs for this post to help trace why homepage images
        // might differ from detail page. Remove or lower level after debugging.
        try {
            Log::debug('normalizePostImages: final image urls', ['post_id' => $post['id'] ?? null, 'urls' => $post['anh']]);
        } catch (\Throwable $__e) {
            // ignore logging errors
        }

        return $post;
    }

    /**
     * Lấy danh sách bài đăng mới nhất theo ngay_cap_nhat (hoặc updated_at nếu thiếu)
     * để hiển thị ở trang chủ.
     */
    private function loadLatestPosts(?int $limit = null): array
    {
        try {
            $query = DB::table('bai_dang as bd')
                ->join('phong as p', 'p.id', '=', 'bd.phong_id')
                ->join('day_tro as d', 'd.id', '=', 'p.day_tro_id')
                ->where('bd.trang_thai', 'dang')
                ->orderByDesc(DB::raw('COALESCE(bd.ngay_cap_nhat, bd.ngay_tao)'))
                ->select('bd.*', 'p.id as phong_id', 'p.dien_tich', 'p.suc_chua', 'd.dia_chi', 'd.tien_ich');

            if ($limit !== null) {
                $query->limit($limit);
            }

            $posts = $query->get()
                ->map(function ($row) {
                    $asArray = (array) $row;
                    /*return $this->normalizePostImages($asArray);*/
                    return $this->normalizePostImages($asArray, false);
                })
                ->values()
                ->toArray();

            return $posts;
        } catch (\Throwable $e) {
            Log::warning('PublicController: loadLatestPosts failed: ' . $e->getMessage());
            return [];
        }
    }

    /** Danh sách bài đăng (Frontend gọi API port 8000) */
    public function listing(Request $request)
    {
        try {
            $mode = $request->input('mode');
            if (!$mode) {
                $mode = session('last_action', 'search');
            }

            if ($mode === 'search') {
                session()->forget(['recommendations', 'recommend_error', 'recommend_payload']);
                session()->put('last_action', 'search');
            } elseif ($mode === 'ai') {
                session()->put('last_action', 'ai');
            }

            $recommendations = session('recommendations', []);
            $recommend_error = session('recommend_error');
            $recommend_payload = session('recommend_payload');
            $showRecommendationsOnly = ($mode === 'ai' || session('last_action') === 'ai') && !empty($recommendations);

            $response = $this->api->get('bai-dang', $request->only('min', 'max', 'page', 'dia_chi', 'area'));

            // Load regions (địa chỉ) from local table to populate datalist/select in view
            try {
                $regions = DB::table('dia_chi')->select('ten_dia_chi')->orderBy('ten_dia_chi')->get()->map(function($r){
                    return is_array($r) ? $r : (array) $r;
                })->toArray();
            } catch (\Throwable $e) {
                Log::warning('Không thể đọc bảng `dia_chi` để hiển thị gợi ý khu vực: ' . $e->getMessage());
                $regions = [];
            }

            $services = $this->loadServicesForPicker();

            if (!isset($response['status']) || !$response['status']) {
                Log::warning('⚠️ API /bai-dang trả về lỗi', ['response' => $response]);
                return view('public.listing', [
                    'data' => [],
                    'meta' => null,
                    'filters' => $request->only('min', 'max', 'dia_chi', 'area'),
                    'error' => $response['message'] ?? 'Không thể tải danh sách bài đăng.',
                    'regions' => $regions,
                    'services' => $services,
                    'recommendations' => $recommendations,
                    'recommend_error' => $recommend_error,
                    'recommend_payload' => $recommend_payload,
                    'showRecommendationsOnly' => $showRecommendationsOnly,
                    'activeMode' => $mode,
                ]);
            }

            $rawData = $response['data'] ?? [];
            if (!is_array($rawData)) {
                $rawData = [];
            }

            $normalizedData = array_map(function ($item) {
                $asArray = is_array($item) ? $item : (array) $item;
                $needsHydration = empty($asArray['anh_dai_dien']);

                /*return $needsHydration ? $this->normalizePostImages($asArray) : $asArray;*/
                return $needsHydration ? $this->normalizePostImages($asArray, false) : $asArray;

            }, $rawData);

            return view('public.listing', [
                'data' => $normalizedData,
                'meta' => $response['meta'] ?? null,
                'filters' => $request->only('min', 'max', 'dia_chi', 'area'),
                'regions' => $regions,
                'services' => $services,
                'recommendations' => $recommendations,
                'recommend_error' => $recommend_error,
                'recommend_payload' => $recommend_payload,
                'showRecommendationsOnly' => $showRecommendationsOnly,
                'activeMode' => $mode,
            ]);
        } catch (\Throwable $e) {
            Log::error('❌ Lỗi khi gọi API /bai-dang', ['error' => $e->getMessage()]);
            // still attempt to load regions even if API fails
            try {
                $regions = DB::table('dia_chi')->select('ten_dia_chi')->orderBy('ten_dia_chi')->get()->map(function($r){
                    return is_array($r) ? $r : (array) $r;
                })->toArray();
            } catch (\Throwable $__e) {
                Log::warning('Không thể đọc bảng `dia_chi`: ' . $__e->getMessage());
                $regions = [];
            }

            $services = $this->loadServicesForPicker();

            return view('public.listing', [
                'data' => [],
                'meta' => null,
                'filters' => $request->only('min', 'max', 'dia_chi', 'area'),
                'error' => 'Không thể kết nối đến máy chủ. Vui lòng thử lại sau.',
                'regions' => $regions,
                'services' => $services,
                'recommendations' => session('recommendations', []),
                'recommend_error' => session('recommend_error'),
                'recommend_payload' => session('recommend_payload'),
                'showRecommendationsOnly' => session('last_action') === 'ai' && !empty(session('recommendations', [])),
                'activeMode' => $request->input('mode', session('last_action', 'search')),
            ]);
        }
    }

    public function recommend(Request $request)
    {
        $validated = $request->validate([
            'budget' => ['required', 'numeric', 'min:0'],
            'area' => ['required', 'numeric', 'min:0'],
            'dich_vu_id' => ['nullable', 'string'],
        ]);

        $serviceIds = array_values(array_filter(array_map(function ($value) {
            $value = trim((string) $value);
            if ($value === '' || !is_numeric($value)) {
                return null;
            }
            $intVal = (int) $value;
            return $intVal > 0 ? $intVal : null;
        }, explode(',', $validated['dich_vu_id'] ?? ''))));

        $selectedServiceNames = $serviceIds
            ? $this->getServiceNamesByIds($serviceIds)
            : [];

        $payload = [
            'budget' => (float) $validated['budget'],
            'area' => (float) $validated['area'],
            'dich_vu_id' => $serviceIds,
            'dich_vu' => $selectedServiceNames,
        ];

        $query = $this->buildListingQuery($request, 'ai');
        $endpoint = env('AI_RECOMMEND_ENDPOINT', 'http://127.0.0.1:8002/recommend_top3');

        try {
            Log::info('🚀 Calling AI recommendation service', [
                'endpoint' => $endpoint,
                'payload' => $payload,
            ]);

            $response = Http::timeout(8)->post($endpoint, $payload);

            if (!$response->successful()) {
                Log::warning('Recommend API failed', [
                    'endpoint' => $endpoint,
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'payload' => $payload,
                ]);

                return redirect()->route('listing', $query)
                    ->with([
                        'recommendations' => [],
                        'recommend_error' => 'Không thể lấy gợi ý phòng. Vui lòng thử lại sau.',
                        'recommend_payload' => $payload,
                    ])
                    ->withInput($request->only('budget', 'area', 'dich_vu_id'));
            }

            $recommendations = $this->hydrateRecommendations((array) $response->json('recommend', []));

            Log::info('✅ Recommend API response nhận thành công', [
                'endpoint' => $endpoint,
                'count' => count($recommendations),
            ]);

            return redirect()->route('listing', $query)
                ->with([
                    'recommendations' => $recommendations,
                    'recommend_error' => null,
                    'recommend_payload' => $payload,
                ])
                ->withInput($request->only('budget', 'area', 'dich_vu_id'));
        } catch (\Throwable $e) {
            Log::error('Recommend API exception', [
                'endpoint' => $endpoint,
                'payload' => $payload,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('listing', $query)
                ->with([
                    'recommendations' => [],
                    'recommend_error' => 'Hệ thống đề xuất đang bận. Vui lòng thử lại sau.',
                    'recommend_payload' => $payload,
                ])
                ->withInput($request->only('budget', 'area', 'dich_vu_id'));
        }
    }

    private function buildListingQuery(Request $request, ?string $modeOverride = null): array
    {
        $query = [
            'dia_chi' => $request->input('dia_chi'),
            'min' => $request->input('min'),
            'max' => $request->input('max'),
            'area' => $request->input('area'),
            'dich_vu_id' => $request->input('dich_vu_id'),
            'sort' => $request->input('sort'),
            'filters' => $request->input('filters', []),
            'page' => $request->input('page'),
            'mode' => $modeOverride ?? $request->input('mode'),
        ];

        return array_filter($query, function ($value) {
            if (is_array($value)) {
                return count(array_filter($value, fn($v) => $v !== null && $v !== '')) > 0;
            }

            return $value !== null && $value !== '';
        });
    }

    private function loadServicesForPicker(): array
    {
        try {
            $nameColumn = Schema::hasColumn('dich_vu', 'ten_dich_vu') ? 'ten_dich_vu' : 'ten';
            $priceColumn = Schema::hasColumn('dich_vu', 'gia') ? 'gia' : 'don_gia';

            return DB::table('dich_vu')
                ->select(
                    'id',
                    DB::raw($nameColumn . ' as ten_hien_thi'),
                    DB::raw($priceColumn . ' as gia_hien_thi'),
                    'don_vi',
                    'ma'
                )
                ->orderBy($nameColumn)
                ->get()
                ->map(function ($service) {
                    return is_array($service) ? $service : (array) $service;
                })
                ->toArray();
        } catch (\Throwable $e) {
            Log::warning('Không thể đọc bảng `dich_vu` để hiển thị gợi ý dịch vụ: ' . $e->getMessage());
            return [];
        }
    }

    private function getServiceNamesByIds(array $serviceIds): array
    {
        if (empty($serviceIds)) {
            return [];
        }

        try {
            $nameColumn = Schema::hasColumn('dich_vu', 'ten_dich_vu') ? 'ten_dich_vu' : 'ten';

            return DB::table('dich_vu')
                ->whereIn('id', $serviceIds)
                ->pluck($nameColumn)
                ->filter()
                ->map(function ($name) {
                    return trim((string) $name);
                })
                ->values()
                ->toArray();
        } catch (\Throwable $e) {
            Log::warning('Không thể load danh sách dịch vụ đã chọn: ' . $e->getMessage());
            return [];
        }
    }

    private function hydrateRecommendations(array $recommendations): array
{
    $hydrated = [];

    foreach ($recommendations as $rec) {

        if (!is_array($rec)) continue;

        // AI trả về post_id hoặc id
        $postId = $rec['id'] ?? $rec['bai_dang_id'] ?? null;

        if ($postId) {
            try {
                $detail = $this->api->get("bai-dang/$postId");

                if (!empty($detail['data'])) {
                    $detailData = is_array($detail['data']) ? $detail['data'] : (array) $detail['data'];
                    $hydrated[] = $this->normalizePostImages($detailData);
                    continue;
                }
            } catch (\Throwable $e) {
                Log::warning("Không load được bài đăng $postId từ API", ['error' => $e->getMessage()]);
            }
        }

        // fallback
        $fallback = [
            'id' => $postId,
            'tieu_de' => $rec['tieu_de'] ?? 'Phòng gợi ý',
            'dia_chi' => $rec['dia_chi'] ?? null,
            'gia_niem_yet' => $rec['gia'] ?? null,
            'dien_tich' => $rec['dien_tich'] ?? null,
            'tien_ich' => $rec['tien_ich'] ?? null,
            'anh_dai_dien' => asset("upload/room1.jpg"),
        ];

        $hydrated[] = $this->normalizePostImages($fallback, false);
    }

    return array_slice($hydrated, 0, 3);
}


    /**Chi tiết bài đăng */
    public function detail($id)
    {
        try {
            $response = $this->api->get("bai-dang/{$id}");

            if (!isset($response['status']) || !$response['status']) {
                Log::warning("⚠️ Bài đăng không tồn tại hoặc đã bị ẩn (ID={$id})", ['response' => $response]);
                abort(404, $response['message'] ?? 'Không tìm thấy bài đăng này.');
            }

            $item = $response['data'] ?? null;

            if (!$item) {
                abort(404, 'Không tìm thấy dữ liệu bài đăng.');
            }

            return view('public.detail', compact('item'));

        } catch (\Throwable $e) {
            Log::error("❌ Lỗi khi gọi API /bai-dang/{$id}", ['error' => $e->getMessage()]);
            abort(500, 'Có lỗi xảy ra khi tải thông tin bài đăng.');
        }
    }
}
