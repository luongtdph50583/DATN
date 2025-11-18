<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckClubManager
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        // Lấy tất cả CLB mà user quản lý
        $managedClubs = $user->getManagedClubs();

        if ($managedClubs->count() === 0) {
            return redirect()->back()->with('error', 'Bạn không phải Chủ nhiệm CLB.');
        }

        // Gán CLB quản lý vào request để Controller dùng
        $request->merge(['managed_clubs' => $managedClubs]);

        return $next($request);
    }
}
