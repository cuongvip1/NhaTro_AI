@extends('layouts.app')

@section('title', 'Trang chủ - Hệ Thống Trọ')

@section('content')
<div x-data="{
  showLoginModal: false,
  message: 'Vui lòng đăng nhập để tiếp tục',
  openPopup(msg) {
    this.message = msg;
    this.showLoginModal = true;
  }
}">

{{-- ====== Animation helpers (nhẹ, chỉ dùng tại view này) ====== --}}
<style>
  @keyframes fadeInUp { from{opacity:0;transform:translateY(18px)} to{opacity:1;transform:translateY(0)} }
  .animate-fadeInUp { animation: fadeInUp .8s ease-out both; }
  .anim-delay-150 { animation-delay: .15s }
  .anim-delay-300 { animation-delay: .3s }
  @keyframes shine { 0%,100%{opacity:.2;transform:translateX(-30%) rotate(5deg)} 50%{opacity:.5;transform:translateX(30%) rotate(-5deg)} }
</style>
{{-- ============== HERO SECTION ============== --}}
<section class="relative overflow-hidden text-white reveal">

  {{-- 🌆 Background có hiệu ứng sáng nhẹ --}}
  <div class="absolute inset-0">
    <img src="{{ asset('images/bg-tro.jpg') }}" alt="Background"
         class="w-full h-full object-cover opacity-90 transition-all duration-[4000ms] ease-in-out reveal-bg">
<script>
// Listen for Alpine-dispatched events from the featured carousel
document.addEventListener('show-chutro', function(e) {
  if (e && e.detail) {
    showChuTroInfo(e.detail.ten, e.detail.sdt);
  }
});
</script>
    <div class="absolute inset-0 bg-gradient-to-r from-indigo-800/90 via-purple-700/80 to-blue-600/80"></div>
  </div>

  {{-- ✨ Content --}}
  <div class="relative z-10 max-w-7xl mx-auto px-6 py-20 grid md:grid-cols-2 gap-12 items-center">
    {{-- LEFT: Title --}}
    <div class="text-center md:text-left reveal-left">
      <span class="inline-flex items-center bg-white/20 backdrop-blur-sm px-4 py-1 rounded-full text-sm mb-5">
        <img src="{{ asset('images/Logo.png') }}" alt="Hệ Thống Trọ" class="w-5 h-5 object-contain mr-2" onerror="this.onerror=null;this.src='/images/default-avatar.png'"> Nền tảng tìm phòng trọ #1 tại TP.HCM
      </span>

      <h1 class="text-5xl md:text-6xl font-extrabold leading-tight mb-4">
        Tìm phòng trọ <span class="text-yellow-300 drop-shadow-md">hoàn hảo</span><br> chỉ trong vài phút
      </h1>

      <p class="text-white/90 mb-8 text-lg leading-relaxed">
        Khám phá hàng ngàn phòng trọ chất lượng cao với giá cả phù hợp.<br>
  Danh sách phòng trọ thông minh, thuê nhanh chóng, sống thoải mái.
      </p>

      <div class="flex flex-wrap justify-center md:justify-start gap-4">
        <a href="{{ route('listing') }}"

           class="px-6 py-3 bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold rounded-xl shadow-lg transition transform hover:scale-[1.05] hover:shadow-yellow-300/40 flex items-center">
          <i class="ri-search-line mr-2"></i> Bắt đầu danh sách phòng trọ
        </a>
       @guest
          <button 
              @click="openPopup('Vui lòng đăng nhập để tiếp tục')"
              class="px-6 py-3 border-2 border-white/80 hover:bg-white/15 rounded-xl font-semibold transition transform hover:scale-[1.05]">
              <i class="ri-add-line mr-2"></i> Đăng tin cho thuê
          </button>
        @else
          @if (Auth::user()->vai_tro === 'chu_tro')
              <a href="{{ route('chu-tro.index') }}"
                class="px-6 py-3 border-2 border-white/80 hover:bg-white/15 rounded-xl font-semibold transition transform hover:scale-[1.05]">
                <i class="ri-add-line mr-2"></i> Đăng tin cho thuê
              </a>
          @else
              <button 
                  @click="openPopup('Chỉ chủ trọ mới được đăng tin')"
                  class="px-6 py-3 border-2 border-white/80 hover:bg-white/15 rounded-xl font-semibold transition transform hover:scale-[1.05]">
                  <i class="ri-error-warning-line mr-2"></i> Chỉ chủ trọ mới được đăng tin
              </button>
          @endif
        @endguest

      </div>

      <div class="flex items-center justify-center md:justify-start mt-8 space-x-6 text-sm text-white/80">
        <div class="flex items-center">
          <div class="flex -space-x-2">
            @foreach([1,2,3] as $img)
              <img class="w-8 h-8 rounded-full border-2 border-white"
                   src="https://i.pravatar.cc/40?img={{ $img }}">
            @endforeach
          </div>
          <span class="ml-3">1000+ khách hàng tin tưởng</span>
        </div>
        <span>⭐ 4.9/5 đánh giá</span>
      </div>
    </div>

    {{-- RIGHT: Features --}}
    <div class="space-y-5 reveal-right">
      @foreach ([
        ['icon' => 'ri-search-line', 'color' => 'bg-yellow-400/90', 'title' => 'Tìm kiếm thông minh', 'desc' => 'Lọc theo vị trí, giá cả, tiện ích'],
        ['icon' => 'ri-shield-check-line', 'color' => 'bg-green-400/90', 'title' => 'An toàn & Tin cậy', 'desc' => 'Xác minh chủ nhà, đảm bảo chất lượng'],
        ['icon' => 'ri-headphone-line', 'color' => 'bg-purple-400/90', 'title' => 'Hỗ trợ 24/7', 'desc' => 'Tư vấn miễn phí, hỗ trợ tận tình']
      ] as $item)
        <div class="flex items-start bg-white/10 backdrop-blur-2xl rounded-2xl p-5 border border-white/20 hover:bg-white/20 transition transform hover:-translate-y-1 hover:shadow-lg">
          <div class="flex-shrink-0 w-12 h-12 rounded-full {{ $item['color'] }} flex items-center justify-center text-gray-900 text-xl mr-4 shadow-inner">
            <i class="{{ $item['icon'] }}"></i>
          </div>
          <div>
            <h3 class="font-semibold text-lg">{{ $item['title'] }}</h3>
            <p class="text-white/80 text-sm">{{ $item['desc'] }}</p>
          </div>
        </div>
      @endforeach
    </div>
  </div>

  {{-- Decorative Wave --}}
  <svg class="absolute bottom-0 left-0 w-full text-white" viewBox="0 0 1440 320" fill="currentColor">
    <path fill-opacity="0.2"
      d="M0,160L48,144C96,128,192,96,288,85.3C384,75,480,85,576,117.3C672,149,768,203,864,197.3C960,192,1056,128,1152,122.7C1248,117,1344,171,1392,197.3L1440,224V320H0Z">
    </path>
  </svg>
</section>


{{-- ============== STATISTICS ============== --}}
{{-- <section class="py-12 bg-white dark:bg-gray-900 text-center border-b dark:border-gray-700"> --}}
<section class="py-12 bg-white dark:bg-gray-900 text-center border-b dark:border-gray-700 reveal">
  <div class="max-w-6xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-8 reveal-stagger">
    {{-- Dynamic stats: phòng (phong), đánh giá (danh_gia), hỗ trợ (static), địa chỉ (dia_chi) --}}
    <div class="transform transition-all duration-700 ease-out hover:scale-105">
      <h3 class="text-3xl font-bold text-indigo-600 counter" data-target="{{ intval($phongCount ?? 0) }}">{{ number_format($phongCount ?? 0) }}</h3>
      <p class="text-gray-600 dark:text-gray-300">Phòng trọ</p>
    </div>

    <div class="transform transition-all duration-700 ease-out hover:scale-105">
      <h3 class="text-3xl font-bold text-green-600 counter" data-target="{{ intval($danhgiaCount ?? 0) }}">{{ number_format($danhgiaCount ?? 0) }}+</h3>
      <p class="text-gray-600 dark:text-gray-300">Khách hàng hài lòng</p>
    </div>

    <div class="transform transition-all duration-700 ease-out hover:scale-105">
      <h3 class="text-3xl font-bold text-purple-600 counter" data-target="24">24/7</h3>
      <p class="text-gray-600 dark:text-gray-300">Hỗ trợ</p>
    </div>

    <div class="transform transition-all duration-700 ease-out hover:scale-105">
      <h3 class="text-3xl font-bold text-orange-500 counter" data-target="{{ intval($diachiCount ?? 0) }}">{{ number_format($diachiCount ?? 0) }}</h3>
      <p class="text-gray-600 dark:text-gray-300">Quận / Huyện</p>
    </div>
  </div>
</section>


{{-- Search panel intentionally hidden on homepage (moved to a dedicated page) --}}

{{-- ============== FEATURED ROOMS ============== --}}
<section class="py-20 bg-white dark:bg-gray-900 reveal">
  <div class="max-w-7xl mx-auto px-6">
    <div class="flex items-center justify-between mb-10">
      <div class="reveal-left">
        <h2 class="text-3xl font-extrabold text-gray-800 dark:text-white">
          Phòng trọ mới nhất
        </h2>
        <p class="text-gray-500 dark:text-gray-300 mt-1">
          Các bài đăng được cập nhật gần đây
        </p>
      </div>
      {{-- (removed static results badge) --}}
    </div>

    {{-- Hiệu ứng xuất hiện lần lượt từng card --}}
    <script>
      // Alpine factory to avoid large inline x-data quoting issues
      window.featuredCarousel = function() {
        return {
          featured: @json($featured),
          perPage: 6,
          currentPage: 0,
          dir: null, // "left" | "right"
          animStage: null, // null | 'out' | 'in'
          animDuration: 420, // ms
          pages() { return Math.ceil(this.featured.length / this.perPage) },
          prev() {
            if (this.currentPage > 0 && !this.animStage) {
              this.dir = "left";
              this.animStage = 'out';
              setTimeout(() => {
                this.currentPage--;
                this.animStage = 'in';
                setTimeout(() => { this.animStage = null; this.dir = null }, this.animDuration);
              }, this.animDuration);
            }
          },
          next() {
            if (this.currentPage < this.pages() - 1 && !this.animStage) {
              this.dir = "right";
              this.animStage = 'out';
              setTimeout(() => {
                this.currentPage++;
                this.animStage = 'in';
                setTimeout(() => { this.animStage = null; this.dir = null }, this.animDuration);
              }, this.animDuration);
            }
          }
        }
      }
    </script>

    <div x-data="featuredCarousel()" class="relative">

  {{-- Page indicator removed per request --}}

  <div class="tab-transition grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 reveal-stagger"
     :class="animStage === 'out' ? (dir === 'right' ? 'animate-slide-out-left' : 'animate-slide-out-right') : (animStage === 'in' ? (dir === 'right' ? 'animate-slide-in-left' : 'animate-slide-in-right') : '')">
        <template x-for="(item, idx) in featured" :key="idx">
          <div x-show="idx >= currentPage * perPage && idx < (currentPage + 1) * perPage" x-cloak class="group bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden border border-gray-100 dark:border-gray-700 hover:-translate-y-2 hover:shadow-2xl transition-all duration-500 ease-out">
            <div class="relative overflow-hidden">
              <img :src="item.anh_dai_dien || '{{ asset('upload/room1.jpg') }}'" onerror="this.src='{{ asset('upload/room1.jpg') }}'" :alt="item.tieu_de || item.title || 'Phòng trọ'" class="w-full h-56 object-cover transform group-hover:scale-110 transition-all duration-700 ease-out">
              <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-70 transition duration-700"></div>
              <span class="absolute top-3 left-3 bg-red-500 text-white text-xs font-semibold px-3 py-1 rounded-full shadow animate-pulse">🔥 HOT</span>
              <span class="absolute bottom-3 left-3 bg-indigo-600/90 text-white text-xs px-3 py-1 rounded-full flex items-center gap-1"><i class="ri-ruler-line text-sm"></i> <span x-text="(item.dien_tich ? (item.dien_tich + 'm²') : '—')"></span></span>
              <button class="absolute top-3 right-3 bg-white/80 hover:bg-white text-gray-600 hover:text-red-500 rounded-full p-2 shadow transition"><i class="ri-heart-line text-lg"></i></button>
            </div>

            <div class="p-5">
                <div class="flex items-center justify-between mb-1">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white leading-snug group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:from-indigo-500 group-hover:to-purple-600 transition-all duration-500" x-text="item.tieu_de || item.title || 'Phòng trọ'"></h3>
                <div class="flex items-center space-x-3">
                  <div class="flex items-center text-yellow-500 text-sm font-medium"><i class="ri-star-fill mr-1"></i> <span x-text="(item.rating || 4.8)"></span></div>
                  <!-- AI award badge from DQN model -->
                  <template x-if="item.award !== undefined && item.award !== null">
                    <div class="text-xs bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-200 px-2 py-1 rounded-full font-semibold">
                      AI: <span x-text="(Math.round((item.award || 0) * 1000)/1000)"></span>
                    </div>
                  </template>
                </div>
              </div>

              <div class="flex items-center text-gray-500 dark:text-gray-400 text-sm mb-3"><i class="ri-map-pin-line mr-1 text-indigo-500"></i> <span x-text="item.dia_chi || 'TP.HCM'"></span></div>

              <div class="flex flex-wrap gap-2 mb-4">
                <template x-for="(t, ti) in (item.tien_ich ? item.tien_ich.split(',').map(s => s.trim()).filter(Boolean).slice(0,3) : [])" :key="ti">
                  <span class="px-3 py-1 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-300 text-xs font-medium rounded-full border border-indigo-100 dark:border-indigo-800 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition" x-text="t"></span>
                </template>
                <template x-if="!(item.tien_ich && item.tien_ich.length)">
                  <span class="text-xs text-gray-400 italic">Chưa có tiện ích</span>
                </template>
              </div>

              <div class="flex items-center justify-between">
                <div>
                  <p class="text-xl font-bold text-indigo-600 dark:text-indigo-400">
                  <span
                    x-text="
                      item.gia_niem_yet
                        ? Number(item.gia_niem_yet).toLocaleString('vi-VN') + ' đ'
                        : '0 đ'
                    "
                  ></span>
                  <span class="text-sm text-gray-400 font-normal">/ tháng</span>
                </p>

                  <p class="text-xs text-gray-400">~<span x-text="(item.gia_niem_yet && item.dien_tich ? Math.round(item.gia_niem_yet / Math.max(item.dien_tich,1)) : Math.floor(45 + Math.random()*30))"></span>,000đ/m²</p>
                </div>
                <a :href="'/bai-dang/' + (item.id || (idx+1))" class="px-5 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 text-white text-sm font-semibold rounded-xl shadow hover:shadow-lg hover:scale-[1.05] transition"><i class="ri-eye-line mr-1"></i> Xem chi tiết</a>
              </div>

              <div class="border-t dark:border-gray-700 mt-4 pt-3 flex justify-between text-sm text-gray-600 dark:text-gray-400">
                <button @click="$dispatch('show-chutro', {ten: item.chu_tro || 'Chưa rõ', sdt: item.sdt_chu_tro || 'Chưa có SĐT'})" class="flex items-center gap-1 hover:text-indigo-600 transition"><i class="ri-phone-line"></i> Gọi ngay</button>
                <button @click="$dispatch('show-chutro', {ten: item.chu_tro || 'Chưa rõ', sdt: item.sdt_chu_tro || 'Chưa có SĐT'})" class="flex items-center gap-1 hover:text-indigo-600 transition"><i class="ri-chat-1-line"></i> Nhắn tin</button>
                <button class="flex items-center gap-1 hover:text-indigo-600 transition"><i class="ri-calendar-line"></i> Đặt lịch</button>
              </div>
            </div>
          </div>
        </template>
      </div>

      <div class="flex flex-wrap gap-3 items-center justify-end mt-8 text-sm text-gray-600 dark:text-gray-300" x-show="pages() > 1">
        <button type="button" @click="prev()" :disabled="currentPage === 0"
          class="px-4 py-2 rounded-lg border border-indigo-200 text-indigo-600 hover:bg-indigo-50 disabled:opacity-40 disabled:cursor-not-allowed">
          « Trước
        </button>
        <span>Trang <span x-text="currentPage + 1"></span> / <span x-text="pages()"></span></span>
        <button type="button" @click="next()" :disabled="currentPage >= pages() - 1"
          class="px-4 py-2 rounded-lg border border-indigo-200 text-indigo-600 hover:bg-indigo-50 disabled:opacity-40 disabled:cursor-not-allowed">
          Sau »
        </button>
      </div>
    </div>
  </div>
</section>


{{-- 🌟 CTA SECTION: Bạn có phòng trọ cần cho thuê? --}}
<style>
/* ===== BASE ANIMATION ===== */
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(25px); }
  to { opacity: 1; transform: translateY(0); }
}
@keyframes ring {
  0%,100% { transform: rotate(0); }
  10%,30%,50%,70%,90% { transform: rotate(-3deg); }
  20%,40%,60%,80% { transform: rotate(3deg); }
}
.animate-fadeInUp { animation: fadeInUp 1s ease-out forwards; }
.animate-ring { animation: ring 1.5s ease-in-out infinite; animation-delay: 2s; }

/* ===== HIỆU ỨNG NỀN “THỞ + ÁNH SÁNG” ===== */
@keyframes fadeFocus {
  0%, 100% {
    transform: scale(1);
    filter: brightness(0.9) blur(0px);
    opacity: 0.9;
  }
  50% {
    transform: scale(1.05);
    filter: brightness(1.25) blur(1.5px);
    opacity: 1;
  }
}
.bg-panel {
  position: absolute;
  inset: 0;
  overflow: hidden;
  animation: fadeFocus 8s ease-in-out infinite;
  transition: filter 1s ease, opacity 1s ease, transform 1s ease;
}
.bg-panel::after {
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(
    120deg,
    rgba(255,255,255,0) 0%,
    rgba(255,255,255,0.15) 50%,
    rgba(255,255,255,0) 100%
  );
  mix-blend-mode: overlay;
  animation: shimmerLight 10s ease-in-out infinite;
}
/* Tab slide animations for featured carousel */
@keyframes slideOutLeft { from { transform: translateX(0); opacity: 1; } to { transform: translateX(-30%); opacity: 0; } }
@keyframes slideOutRight { from { transform: translateX(0); opacity: 1; } to { transform: translateX(30%); opacity: 0; } }
.tab-transition { overflow: hidden; }
.animate-slide-out-left { animation: slideOutLeft .42s cubic-bezier(.22,.9,.3,1) both; }
.animate-slide-out-right { animation: slideOutRight .42s cubic-bezier(.22,.9,.3,1) both; }

@keyframes slideInLeft { from { transform: translateX(-30%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
@keyframes slideInRight { from { transform: translateX(30%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
.animate-slide-in-left { animation: slideInLeft .42s cubic-bezier(.22,.9,.3,1) both; }
.animate-slide-in-right { animation: slideInRight .42s cubic-bezier(.22,.9,.3,1) both; }
@keyframes shimmerLight {
  0% { transform: translateX(-100%) rotate(10deg); opacity: 0; }
  50% { transform: translateX(100%) rotate(10deg); opacity: 0.4; }
  100% { transform: translateX(200%) rotate(10deg); opacity: 0; }
}

/* ===== GLOW & SHIMMER TEXT ===== */
h2.bg-clip-text {
  position: relative;
  text-shadow: 0 0 15px rgba(255,255,255,0.3),
               0 0 25px rgba(255,255,0,0.2);
  animation: titleGlow 3s ease-in-out infinite alternate;
}
@keyframes titleGlow {
  0% { text-shadow: 0 0 15px rgba(255,255,255,0.3),0 0 25px rgba(255,255,150,0.2); }
  100% { text-shadow: 0 0 25px rgba(255,255,255,0.6),0 0 45px rgba(255,255,150,0.4); }
}
h2.bg-clip-text::after {
  content: "";
  position: absolute;
  top: 0;
  left: -75%;
  width: 50%;
  height: 100%;
  background: linear-gradient(120deg,
    rgba(255,255,255,0) 0%,
    rgba(255,255,255,0.6) 50%,
    rgba(255,255,255,0) 100%);
  animation: shimmer 4s infinite;
  transform: skewX(-20deg);
}
@keyframes shimmer {
  0% { left: -75%; }
  100% { left: 125%; }
}

/* ===== NÚT & HIỆU ỨNG ===== */
a.bg-white {
  position: relative;
  overflow: hidden;
  z-index: 1;
}
a.bg-white::before {
  content: "";
  position: absolute;
  inset: -2px;
  background: linear-gradient(90deg, #6366f1, #a855f7, #3b82f6);
  background-size: 300% 300%;
  border-radius: 1rem;
  opacity: 0;
  transition: opacity 0.3s ease-in-out;
  z-index: -1;
}
a.bg-white:hover::before {
  opacity: 1;
  animation: gradientShift 3s linear infinite;
}
@keyframes gradientShift {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}
a.animate-ring {
  animation: ring 1.5s ease-in-out infinite,
             pulseBorder 2s ease-in-out infinite alternate;
}
@keyframes pulseBorder {
  0% { box-shadow: 0 0 5px rgba(255,255,255,0.3),
               0 0 10px rgba(255,255,255,0.2); }
  100% { box-shadow: 0 0 20px rgba(255,255,255,0.5),
               0 0 40px rgba(255,255,255,0.3); }
}

/* ===== PARTICLES ===== */
.particles {
  position: absolute;
  inset: 0;
  overflow: hidden;
  pointer-events: none;
  z-index: 2;
}
.particle {
  position: absolute;
  border-radius: 9999px;
  background: radial-gradient(circle,rgba(255,255,255,0.8)0%,rgba(255,255,255,0)70%);
  opacity: 0.6;
  animation: floatParticle linear infinite;
}
@keyframes floatParticle {
  0% { transform: translateY(0) scale(1); opacity: 0.7; }
  50% { transform: translateY(-20px) scale(1.1); opacity: 1; }
  100% { transform: translateY(0) scale(1); opacity: 0.7; }
}

/* ===== DARK MODE ===== */
.dark .particle {
  background: radial-gradient(circle, rgba(147,197,253,0.8) 0%, rgba(255,255,255,0) 70%);
  opacity: 0.4;
}
.dark .bg-panel {
  animation: fadeFocusDark 12s ease-in-out infinite;
  filter: brightness(0.85) saturate(1.2);
}
@keyframes fadeFocusDark {
  0%, 100% { filter: brightness(0.85) blur(0px); }
  50% { filter: brightness(1.1) blur(1.5px); }
}
.dark h2.bg-clip-text {
  background: linear-gradient(to right,#a5b4fc,#c4b5fd,#93c5fd);
  -webkit-background-clip: text;
  color: transparent;
}
/* === SCROLL REVEAL ANIMATION === */
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(40px); }
  to { opacity: 1; transform: translateY(0); }
}
@keyframes fadeInLeft {
  from { opacity: 0; transform: translateX(-40px); }
  to { opacity: 1; transform: translateX(0); }
}
@keyframes fadeInRight {
  from { opacity: 0; transform: translateX(40px); }
  to { opacity: 1; transform: translateX(0); }
}

.reveal { opacity: 0; }
.reveal.active { opacity: 1; animation: fadeInUp 0.8s ease-out forwards; }
.reveal-left.active { animation: fadeInLeft 0.8s ease-out forwards; }
.reveal-right.active { animation: fadeInRight 0.8s ease-out forwards; }

/* === STAGGERED APPEAR FOR MULTIPLE ITEMS === */
.reveal-stagger > * {
  opacity: 0;
  transform: translateY(25px);
  transition: all 0.6s ease-out;
}
.reveal-stagger.active > * {
  opacity: 1;
  transform: translateY(0);
}
.reveal-stagger.active > *:nth-child(1) { transition-delay: 0.1s; }
.reveal-stagger.active > *:nth-child(2) { transition-delay: 0.2s; }
.reveal-stagger.active > *:nth-child(3) { transition-delay: 0.3s; }
.reveal-stagger.active > *:nth-child(4) { transition-delay: 0.4s; }
.reveal-stagger.active > *:nth-child(5) { transition-delay: 0.5s; }
.reveal-stagger.active > *:nth-child(6) { transition-delay: 0.6s; }
.reveal-up {
  opacity: 0;
  transform: translateY(40px);
  transition: all 1s ease-out;
}
.reveal.active .reveal-up {
  opacity: 1;
  transform: translateY(0);
}
.reveal-bg {
  opacity: 0.4;
  transform: scale(1.1);
  transition: all 2s ease-out;
}
.reveal.active .reveal-bg {
  opacity: 1;
  transform: scale(1);
}

</style>

{{-- 🌟 CTA SECTION: Bạn có phòng trọ cần cho thuê? --}}
<section class="relative py-24 text-white text-center overflow-hidden reveal">

  {{-- 🌌 NỀN --}}  
  <div class="absolute inset-0 overflow-hidden reveal-bg">
    <div class="bg-panel">
      <img src="{{ asset('upload/room-bg.jpg') }}"
           alt="Background"
           class="w-full h-full object-cover">
    </div>
    <div class="absolute inset-0 bg-gradient-to-r from-indigo-700/70 via-purple-600/70 to-blue-700/70"></div>
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_80%,rgba(255,255,255,0.15),transparent_70%)] 
                animate-[float_10s_ease-in-out_infinite_alternate]"></div>
  </div>

  {{-- ✨ PARTICLES --}}  
  <div class="particles reveal-stagger">
    @for ($i = 0; $i < 25; $i++)
      <span class="particle"
        style="
          width: {{ rand(6,14) }}px;
          height: {{ rand(6,14) }}px;
          top: {{ rand(0,100) }}%;
          left: {{ rand(0,100) }}%;
          animation-duration: {{ rand(8,14) }}s;
          animation-delay: -{{ rand(0,14) }}s;
        ">
      </span>
    @endfor
  </div>

  {{-- 🌟 NỘI DUNG --}}
  <div class="relative z-10 max-w-4xl mx-auto text-center text-white px-6 reveal-up">
    <h2 class="relative text-4xl md:text-5xl font-extrabold mb-5 drop-shadow-[0_2px_8px_rgba(0,0,0,0.4)]
               bg-clip-text text-transparent bg-gradient-to-r from-yellow-300 via-white to-indigo-200">
      Bạn có phòng trọ cần cho thuê?
    </h2>

    <p class="mb-10 text-white/90 text-lg leading-relaxed">
      Đăng tin miễn phí và tiếp cận hàng nghìn khách hàng tiềm năng ngay hôm nay 🌟
    </p>

    <div class="flex flex-wrap justify-center gap-6">
      @guest
        <button 
            @click="window.showLoginPopup('Vui lòng đăng nhập để tiếp tục');"
            class="px-6 py-3 border-2 border-white/80 hover:bg-white/15 rounded-xl font-semibold transition transform hover:scale-[1.05]">
            <i class="ri-add-line mr-2"></i> Đăng tin miễn phí
        </button>
      @else
        @if (Auth::user()->vai_tro === 'chu_tro')
            <a href="{{ route('chu-tro.index') }}"
              class="px-6 py-3 border-2 border-white/80 hover:bg-white/15 rounded-xl font-semibold transition transform hover:scale-[1.05]">
              <i class="ri-add-line mr-2"></i> Đăng tin miễn phí
            </a>
        @else
            <button 
                @click="window.showLoginPopup('Chỉ chủ trọ mới được đăng tin');"
                class="px-6 py-3 border-2 border-white/80 hover:bg-white/15 rounded-xl font-semibold transition transform hover:scale-[1.05]">
                <i class="ri-error-warning-line mr-2"></i> Chỉ chủ trọ mới được đăng tin
            </button>
        @endif
      @endguest

      <a href="#"
         class="relative border-2 border-white/70 px-8 py-3 rounded-2xl font-semibold text-white 
                hover:bg-white/10 hover:shadow-lg hover:shadow-white/30 transition-all duration-300 
                hover:scale-[1.08] animate-ring">
        <i class="ri-phone-line mr-2"></i> Liên hệ tư vấn
      </a>
    </div>
  </div>
</section>

{{-- ============== GOOGLE MAP (HCMC) ============== --}}
<section class="py-16 bg-gray-50 dark:bg-gray-900 reveal">
  <div class="max-w-7xl mx-auto px-6 text-center">
    <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-3 reveal-up">
      📍 Bản đồ phòng trọ tại Thành phố Hồ Chí Minh
    </h3>
    <p class="text-gray-500 dark:text-gray-400 mb-6 reveal-up">
      Kéo, phóng to hoặc di chuyển để khám phá các khu vực trọ phổ biến
    </p>

    <div
      class="rounded-2xl overflow-hidden shadow-xl border border-gray-200 dark:border-gray-700 hover:shadow-2xl transition-all duration-700 ease-out reveal-map">
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.502388917805!2d106.6598629!3d10.7768894!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f3872e5b41f%3A0x6e5c70e2a546f1e4!2zVGjDoG5oIHBo4buRIDIgSCDEkMO0IENow60gTWluaCBDaXR5!5e0!3m2!1svi!2s!4v1696772290169!5m2!1svi!2s"
        width="100%" height="450" style="border:0;" allowfullscreen loading="lazy"
        referrerpolicy="no-referrer-when-downgrade" class="w-full h-[450px] rounded-2xl">
      </iframe>
    </div>
  </div>
</section>


<script>
document.addEventListener("DOMContentLoaded", () => {
  const reveals = document.querySelectorAll(".reveal, .reveal-left, .reveal-right, .reveal-stagger");
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add("active");
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.15 });

  reveals.forEach(el => observer.observe(el));
});
</script>
<!-- 🪟 Popup đăng nhập -->
<div
  x-show="showLoginModal"
  x-transition.opacity
  x-cloak
  class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-[999]"
>
  <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-[90%] max-w-md p-8 text-center relative">
    <button @click="showLoginModal = false"
            class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
      <i class="ri-close-line text-2xl"></i>
    </button>

    <i class="ri-error-warning-line text-4xl text-yellow-500 mb-3"></i>
    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-2">Thông báo</h2>
    <p x-text="message" class="text-gray-500 dark:text-gray-300 mb-6"></p>

    <div class="flex justify-center gap-3">
      <a href="{{ route('login') }}"
         class="px-5 py-2 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition">
        <i class="ri-login-box-line mr-1"></i> Đăng nhập
      </a>
      <button @click="showLoginModal = false"
              class="px-5 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-xl hover:bg-gray-300 dark:hover:bg-gray-600 transition">
        Hủy
      </button>
    </div>
  </div>
</div>

</div>

@endsection
