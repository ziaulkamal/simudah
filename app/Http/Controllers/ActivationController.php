<?php

namespace App\Http\Controllers;

use App\Models\People;
use App\Models\TemporaryPeople;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;


class ActivationController extends Controller
{
    public function index(Request $request)
    {
        // Ambil nilai per_page dari request (default 10)
        $perPage = $request->get('per_page', 10);

        // Ambil data TemporaryPeople beserta dokumen
        $peoples = TemporaryPeople::with('documents')
            ->latest()
            ->paginate($perPage)
            ->appends($request->except('page'));

        // Ambil semua identity_hash dari tabel People
        $activatedHashes = People::pluck('identity_hash')->toArray();

        // Tambahkan status ke setiap data
        $peoples->getCollection()->transform(function ($temporary) use ($activatedHashes) {
            $temporary->status = in_array($temporary->identity_hash, $activatedHashes)
                ? 'aktif'
                : 'belum aktif';
            return $temporary;
        });

        return view('admin.activation', [
            'title'         => 'Aktivasi User',
            'menu'          => 'Addons',
            'submenu'       => 'Daftar Permohonan Aktivasi',
            'titleMenus'    => 'Manajemen Pengguna',
            'sectionMenu'   => 'secondary-menu',
            'peoples'       => $peoples,
            'activatedHashes' => $activatedHashes,
        ]);
    }


    public function show($id)
    {
        $person = TemporaryPeople::with('documents')->findOrFail($id);
        return view('activation-show', compact('person'));
    }

    public function activate($id)
    {
        $person = TemporaryPeople::findOrFail($id);
        $person->update(['is_verified' => true]);
        return redirect()->route('activation.index')->with('success', 'User berhasil diaktivasi.');
    }

    public function data(Request $request)
    {
        $peoples = TemporaryPeople::with('documents')->latest()->paginate(10);

        $activatedHashes = People::pluck('identity_hash')->toArray();

        $mappedData = $peoples->map(function ($person) use ($activatedHashes) {
            return [
                'id' => Crypt::encryptString($person->id),
                'fullName' => $person->fullName,
                'identityNumber' => $person->identityNumber,
                'phoneNumber' => $person->phoneNumber,
                'gender' => $person->gender ?? '-',
                'status' => in_array($person->identity_hash, $activatedHashes) ? 'aktif' : 'belum aktif',
                'documents' => $person->documents->map(fn($d) => [
                    'id' => $d->id,
                    'type' => $d->type,
                    'file_url' => $d->id ? route('storage.file', ['id' => $d->id]) : '#',
                ]),
            ];
        });

        return response()->json([
            'data' => $mappedData,
            'from' => $peoples->firstItem(),
            'pagination' => (string) $peoples->links('components.pagination-simple'),
        ]);
    }
}
