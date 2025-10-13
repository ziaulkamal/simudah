<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Kemendagri\Districts;
use App\Models\Kemendagri\Villages;
use App\Models\People;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    function dashboard()  {
        $data = [
            'title'         => 'Dashboard',
            'menu'          => 'dashboard',
            'submenu'       => '',
            'titleMenus'    => 'Application',
            'sectionMenu'   => 'main-menu',
        ];
        return view('admin.dashboard', $data);
    }

    public function peoples(Request $request)
    {
        $query = People::with([
            'role',
            'category',
            'district:id,name,regency_id',
            'village:id,name,district_id',
        ]);

        if ($request->filled('fullName')) {
            $query->where('fullName', 'like', "%{$request->fullName}%");
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('nik')) {
            $identity_hash = People::generateHmac($request->nik);
            $query->where('identity_hash', $identity_hash);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('district_id')) {
            $query->where('districtId', $request->district_id);
        }

        if ($request->filled('village_id')) {
            $query->where('villageId', $request->village_id);
        }

        $perPage = $request->get('per_page', 10);
        $peoples = $query->latest()->paginate($perPage);
        $peoples->appends($request->except('page'));


        $districts = Districts::where('regency_id', 1112)
            ->orderBy('name')
            ->get(['id', 'name']);


        $villages = collect(); // default kosong
        if ($request->filled('district_id')) {
            $villages = Villages::where('district_id', $request->district_id)
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        $categories = Category::orderBy('name')->get(['id', 'name']);
        $data = [
            'title'       => 'Pelanggan',
            'menu'        => 'pelanggan',
            'submenu'     => 'Data Pelanggan',
            'titleMenus'  => 'Pelanggan',
            'sectionMenu' => 'main-menu',
            'peoples'     => $peoples,
            'districts'   => $districts,
            'villages'    => $villages,
            'categories'  => $categories, // ← tambahkan
        ];

        return view('admin.person', $data);
    }


    function insertPeoples()
    {
        $data = [
            'title'         => 'Pelanggan',
            'menu'          => 'pelanggan',
            'submenu'       => 'Daftarkan Pelanggan',
            'titleMenus'    => 'Pelanggan',
            'sectionMenu'   => 'main-menu',
        ];
        return view('admin.person-form', $data);
    }

    function addons()  {
        $data = [
            'title'         => 'Dashboard',
            'menu'          => 'dashboard',
            'submenu'       => '',
            'titleMenus'    => 'Addons',
            'sectionMenu'   => 'secondary-menu',
        ];
        return view('admin.dashboard', $data);
    }
}
