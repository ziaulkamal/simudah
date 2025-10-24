<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Kemendagri\Districts;
use App\Models\Kemendagri\Villages;
use App\Models\People;
use App\Models\Role;
use App\Models\SecureUser;
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

        if ($request->filled('search')) {
            $query->where('fullName', 'like', "%{$request->search}%");
        }

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

        $villages = collect();
        if ($request->filled('district_id')) {
            $villages = Villages::where('district_id', $request->district_id)
                ->orderBy('name')
                ->get(['id', 'name'])
                ->unique('id'); // 🔹 pastikan unique
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

    function viewsPeople($hash)
    {

        $query = People::with([
            'province',
            'regencie',
            'district',
            'village',
            'location',
            'category',
            'categoryHistories.category',
            'transactions' => function ($q) {
                // 🔽 Ambil 15 transaksi terakhir, urut dari terbaru
                $q->orderBy('id', 'desc')->limit(15);
            },
        ])->where('identity_hash', $hash)->firstOrFail();

        $categories = Category::where('status', 'active')->get();
        $categoryHistories = $query->categoryHistories->map(function ($h) {
            return [
                'id' => $h->id,
                'category' => [
                    'id' => optional($h->category)->id,
                    'name' => optional($h->category)->name ?? '-',
                    'price' => optional($h->category)->price ?? 0,
                ],
                'changed_at' => $h->created_at->format('d/m/Y H:i'),
            ];
        });
        // dd($query->transactions);
        $data = [
            'title'         => 'Detail Pelanggan',
            'menu'          => 'pelanggan',
            'submenu'       => 'Data Pelanggan',
            'titleMenus'    => 'Pelanggan',
            'sectionMenu'   => 'main-menu',
            'people'        => $query,
            'categories'    => $categories,
            'categoryHistories' => $categoryHistories,
            'transactions'  => $query->transactions,
        ];
        return view('admin.person-view', $data);
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

    function transaction()  {
        $categories = Category::where('status', 'active')->get();
        $data = [
            'title'         => 'Riwayat Transaksi',
            'menu'          => 'dashboard',
            'submenu'       => '',
            'titleMenus'    => 'Transaksi Pelanggan',
            'sectionMenu'   => 'main-menu',
            'categories'    => $categories,
        ];
        return view('admin.transaction', $data);
    }


    public function viewTransactions()
    {
        $data = [
            'title'         => 'Detail Transaksi Pelanggan',
            'menu'          => 'Detail Transaksi Pelanggan',
            'submenu'       => '',
            'titleMenus'    => 'Transaksi Pelanggan',
            'sectionMenu'   => 'main-menu',
        ];
        return view('admin.search-people', $data);
    }

    public function userForm()
    {

        $roles = Role::where('level', '!=', 0)
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $peoples = People::select('id', 'fullName', 'identityNumber')->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'label' => strtoupper($p->fullName) . ' (' . $p->identityNumber . ')',
            ];
        });

        // Data untuk dropdown status
        $statuses = [
            'active' => 'Aktif',
            'inactive' => 'Tidak Aktif',
        ];

        $data = [
            'title'         => 'Buat User Baru',
            'menu'          => 'user',
            'submenu'       => 'Daftarkan User',
            'titleMenus'    => 'User Management',
            'sectionMenu'   => 'secondary-menu',
            'roles' => $roles,
            'peoples' => $peoples,
            'statuses' => $statuses,
        ];
        return view('admin.user-form', $data);
    }

    /**
     * Halaman daftar user.
     */
    public function userList(Request $request)
    {

        $query = SecureUser::with('role');

        // Filter opsional
        if ($request->filled('username')) {
            $query->where('username', 'like', '%' . $request->username . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        $users = $query->paginate(10)->withQueryString();
        $roles = Role::all();


        $data = [
            'title'         => 'User',
            'menu'          => 'user',
            'submenu'       => 'List User',
            'titleMenus'    => 'User Management',
            'sectionMenu'   => 'secondary-menu',
            'users'         => $users,
            'roles'         => $roles,
        ];
        return view('admin.user-list', $data);
    }
}
