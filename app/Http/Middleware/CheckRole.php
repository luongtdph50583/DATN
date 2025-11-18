<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|array  $roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Bạn chưa đăng nhập');
        }

        // Nếu roles là chuỗi: "admin", "club_manager"
        if (!in_array($user->role, $roles)) {
            abort(403, 'Bạn không có quyền truy cập');
        }

        return $next($request);
    }
}
