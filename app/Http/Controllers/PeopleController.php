<?php

namespace App\Http\Controllers;

use App\Http\Requests\PeopleRequest;
use App\Models\People;
use App\Models\Role;
use Illuminate\Support\Facades\Crypt;

class PeopleController extends Controller
{
    /**
     * Tampilkan data berdasarkan identity_hash
     */
    public function showByHash($identity_hash)
    {
        $people = People::where('identity_hash', $identity_hash)->first();

        if (!$people) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($people);
    }

    /**
     * Simpan data baru
     */
    public function store(PeopleRequest $request)
    {
        $data = $request->validated();

        // Hitung umur
        $data['age'] = (new \DateTime())->diff(new \DateTime($data['birthdate']))->y;

        // Set role_id otomatis jika tidak dikirim
        if (empty($data['role_id'])) {
            $defaultRole = Role::where('status', 'active')
                ->orderByDesc('level')
                ->first();
            $data['role_id'] = $defaultRole->id ?? null;
        }

        $people = People::create($data);
        return response()->json($people, 201);
    }

    /**
     * Update data
     */
    public function update(PeopleRequest $request, $id)
    {
        $people = People::find($id);
        if (!$people) return response()->json(['error' => 'Data tidak ditemukan'], 404);

        $data = $request->validated();


        if (isset($data['birthdate'])) {
            $data['age'] = (new \DateTime())->diff(new \DateTime($data['birthdate']))->y;
        }

        $people->update($data);
        return response()->json($people);
    }

    /**
     * Hapus data
     */
    public function destroy($id)
    {
        $people = People::find($id);
        if (!$people) return response()->json(['error' => 'Data tidak ditemukan'], 404);

        $people->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Test NIK, cari berdasarkan identity_hash
     */
    public function testNik($nik)
    {
        // Cari orang melalui mutator HMAC di model
        $identity_hash = People::generateHmac($nik);

        $people = People::where('identity_hash', $identity_hash)->first();

        if (!$people) {
            return response()->json([
                'success' => false,
                'message' => 'Data dengan NIK ini tidak ditemukan',
                'nik' => $nik,
                'hash' => $identity_hash
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data ditemukan',
            'data' => $people,
            'hash' => $identity_hash
        ]);
    }
}
