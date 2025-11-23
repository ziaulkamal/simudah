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

            session()->flash('role_modal', [
                'title' => 'Akses Ditolak',
                'message' => 'Level Anda tidak diperbolehkan mengakses halaman ini.'
            ]);

            if (url()->previous() !== url()->current()) {
                return redirect()->back()->withInput();
            }

            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
