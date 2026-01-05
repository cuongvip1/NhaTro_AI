<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();

                // Use Role helper for clarity and future-proofing
                if (\App\Support\Role::isOwner($user)) {
                    return redirect()->route('chu-tro.dashboard');
                }
                if (\App\Support\Role::isTenant($user)) {
                    // If tenant hasn't created their preference yet, send them
                    // to the preference page first (checked by nguoi_dung_id).
                    try {
                        $userId = session('user')['id'] ?? null;
                        if (!$userId) {
                            // fallback to Auth id if session not present
                            $userId = Auth::id();
                        }
                        if ($userId) {
                            $hasPref = DB::table('so_thich')->where('nguoi_dung_id', $userId)->exists();
                            if (!$hasPref) {
                                return redirect()->route('khach-thue.loai-nha-tro-quan-tam.index');
                            }
                        }
                    } catch (\Exception $e) {
                        // On DB error, fall back to dashboard so we don't block login.
                    }
                    return redirect()->route('khach-thue.dashboard');
                }
                if (\App\Support\Role::isAdmin($user)) {
                    return redirect()->route('admin.dashboard');
                }

                return redirect('/');
            }
        }

        return $next($request);
    }
}