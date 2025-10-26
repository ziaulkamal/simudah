<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use Illuminate\Support\Facades\Session;

class SystemLogController extends Controller
{
    public function index()
    {
        $roleId = Session::get('role_id');

        $logs = SystemLog::where('role_id', $roleId)
            ->latest()
            ->take(20)
            ->get();

        return response()->json(['logs' => $logs]);
    }
}
