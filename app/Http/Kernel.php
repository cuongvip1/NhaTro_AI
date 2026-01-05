
protected $middlewareAliases = [
'auth' => \App\Http\Middleware\Authenticate::class,
'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
'can' => \Illuminate\Auth\Middleware\Authorize::class,
'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

// Đảm bảo dòng này nằm chính xác ở đây
'webauth' => \App\Http\Middleware\EnsureWebAuthenticated::class,
	'adminweb' => \App\Http\Middleware\EnsureAdminAuthenticated::class,
];