<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;
use App\Models\People;

class RedirectPeopleCustomer
{
    public function handle($request, Closure $next)
    {
        // Jika akses sudah merupakan halaman detail pelanggan -> jangan redirect lagi
        if ($request->routeIs('customer.view')) {
            return $next($request);
        }

        $roleLevel = Session::get('role_level');
        $peopleSession = Session::get('people');

        // Jika bukan pelanggan → lewatkan saja
        if ($roleLevel !== 3 || !$peopleSession) {
            return $next($request);
        }

        // Ambil data people berdasarkan ID dari session
        $people = People::find($peopleSession['id']);

        if (!$people) {
            return $next($request);
        }

        // Redirect langsung ke halaman detail pelanggan
        return redirect()->route('customer.view', [
            'hash' => $people->identity_hash
        ]);
    }
}
