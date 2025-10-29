<?php

namespace App\Http\Controllers;

use App\Models\People;
use App\Models\TemporaryPeople;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;


class ActivationController extends Controller
{
    public function index()
    {
        // Ambil data temporary beserta dokumen
        $peoples = TemporaryPeople::with('documents')->latest()->paginate(10);

        // Ambil semua identity_hash dari tabel people untuk perbandingan
        $activatedHashes = People::pluck('identity_hash')->toArray();

        // Tambahkan status aktivasi ke setiap temporary_people
        $peoples->getCollection()->transform(function ($temporary) use ($activatedHashes) {
            $temporary->status = in_array($temporary->identity_hash, $activatedHashes)
                ? 'aktif'
                : 'belum aktif';
            return $temporary;
        });

        return view('admin.activation', [
            'title' => 'Aktivasi User',
            'menu' => 'dashboard',
            'submenu' => '',
            'titleMenus' => 'Aktivasi',
            'sectionMenu' => 'main-menu',
            'peoples' => $peoples,
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

    public function data()
    {
        $peoples = TemporaryPeople::with('documents')->latest()->paginate(10);

        // Ambil semua identity_hash dari People
        $activatedHashes = People::pluck('identity_hash')->toArray();

        $peoplesData = $peoples->map(function($person) use ($activatedHashes) {
            return [
                'id' => Crypt::encryptString($person->id),
                'fullName' => $person->fullName,
                'identityNumber' => $person->identityNumber,
                'phoneNumber' => $person->phoneNumber,
                'gender' => $person->gender ?? '-',
                'created_at' => $person->created_at->format('d M Y H:i'),
                'status' => in_array($person->identity_hash, $activatedHashes) ? 'aktif' : 'belum aktif',
                'documents' => $person->documents->map(fn($d) => [
                    'id' => $d->id,
                    'type' => $d->type,
                    'file_url' => $d->id ? route('storage.file', ['id' => $d->id]) : '#',
                ]),
            ];
        });

        return response()->json([
            'data' => $peoplesData,
            'links' => (string) $peoples->links('components.paginations'), // optional pagination html
        ]);
    }

}
