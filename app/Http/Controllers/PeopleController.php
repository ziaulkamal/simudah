<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\People;

class PeopleController extends Controller
{
    public function index(Request $request)
    {
        $query = People::with(['role', 'category']);

        // 🔍 Filter Nama
        if ($request->filled('fullName')) {
            $query->where('fullName', 'like', '%' . $request->fullName . '%');
        }

        // 🔍 Filter Gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // 🔍 Filter Rentang Umur
        if ($request->filled('age_from')) {
            $query->where('age', '>=', $request->age_from);
        }

        if ($request->filled('age_to')) {
            $query->where('age', '<=', $request->age_to);
        }

        // 🔍 Filter Role atau Kategori (opsional)
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $peoples = $query->latest()->paginate(10);

        return view('admin.person', compact('peoples'));
    }
}
