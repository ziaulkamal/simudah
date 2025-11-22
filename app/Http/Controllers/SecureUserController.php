<?php

namespace App\Http\Controllers;

use App\Models\SecureUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;

class SecureUserController extends GlobalController
{
    public function __construct()
    {
        parent::__construct(SecureUser::class);
    }

    /**
     * ✅ GET - Ambil semua pengguna
     */
    public function index()
    {
        try {
            $users = $this->modelClass::with(['role', 'people'])
                ->whereHas('role', function ($query) {
                    $query->where('level', '!=', 99); // exclude role level 99
                })
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data pengguna berhasil diambil.',
                'data' => $users,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data pengguna.',
            ], 500);
        }
    }


    /**
     * ✅ POST - Simpan pengguna baru
     */
    public function store(Request $request)
    {
        $messages = [
            'username.required' => 'Username wajib diisi.',
            'username.string' => 'Username harus berupa teks.',
            'username.unique' => 'Username sudah digunakan.',
            'username.regex' => 'Username tidak boleh mengandung spasi.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'people_id.unique' => 'Orang ini sudah memiliki akun pengguna.',
            'people_id.exists' => 'Data orang yang dipilih tidak valid.',
            'role_id.required' => 'Wajib Pilih Level Akses.',
        ];

        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'regex:/^[^\s]+$/',
                'unique:secure_user,username',
            ],
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required', // opsional, tapi baik ditambah
            'role_id' => 'required|exists:roles,id',
            'people_id' => 'nullable|exists:peoples,id|unique:secure_user,people_id',
            'status' => 'nullable|in:active,inactive',
        ], $messages);

        $validated['password'] = Hash::make($validated['password']);

        try {
            $user = $this->modelClass::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Data pengguna berhasil disimpan.',
                'data' => $user,
            ], 201);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal! Username atau data pengguna sudah terdaftar.',
                ], 409);
            }

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data pengguna.',
            ], 500);
        }
    }

    /**
     * ✅ GET - Ambil 1 pengguna
     */
    public function edit($id)
    {
        $user = $this->modelClass::with(['role', 'people'])->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Data pengguna tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data pengguna berhasil diambil.',
            'data' => $user,
        ]);
    }

    /**
     * ✅ PUT - Update data pengguna
     */
    public function update(Request $request, $id)
    {
        $user = $this->modelClass::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Data pengguna tidak ditemukan.',
            ], 404);
        }

        $validated = $request->validate([
            'username' => 'sometimes|required|string|unique:secure_user,username,' . $user->id,
            'password' => 'sometimes|nullable|string|min:6',
            'role_id' => 'nullable|exists:roles,id',
            'people_id' => 'nullable|exists:peoples,id|unique:secure_user,people_id,' . $user->id,
            'status' => 'nullable|in:active,inactive',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        try {
            $user->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Data pengguna berhasil diperbarui.',
                'data' => $user,
            ]);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal! Username atau data pengguna sudah terdaftar.',
                ], 409);
            }

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data pengguna.',
            ], 500);
        }
    }

    /**
     * ✅ DELETE - Hapus data pengguna
     */
    public function destroy($id)
    {
        $user = $this->modelClass::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Data pengguna tidak ditemukan.',
            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data pengguna berhasil dihapus.',
        ]);
    }
}
