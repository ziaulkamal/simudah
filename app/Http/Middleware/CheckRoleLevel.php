<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckRoleLevel
{
    public function handle(Request $request, Closure $next, ...$allowedLevels)
    {
        $roleLevel = session()->get('role_level');

        $allowedLevels = array_map('intval', $allowedLevels);

        if (!in_array((int)$roleLevel, $allowedLevels)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Anda tidak dibenarkan mengakses resource ini.'
                ], 403);
            }

            session()->flash('role_modal', [
                'title' => 'Akses Ditolak',
                'message' => 'Level Anda tidak diperbolehkan mengakses halaman ini.'
            ]);

            return redirect()->back(); // fallback web
        }

        return $next($request);
    }
}
