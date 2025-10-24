<?php

namespace App\Http\Controllers;

use App\Models\SecureUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Traits\HasGlobalSignature; // ← tambahkan ini


class SecureUserController extends Controller
{
    use HasGlobalSignature; // ← aktifkan trait-nya
    /**
     * Tampilkan semua pengguna.
     */
    public function index()
    {
        $users = SecureUser::with(['role', 'people'])->get();
        return response()->json($users);
    }

    /**
     * Simpan pengguna baru.
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
            'people_id.unique' => 'Orang ini sudah memiliki akun pengguna.',
            'people_id.exists' => 'Data orang yang dipilih tidak valid.',
        ];

        $request->validate([
            'username' => [
                'required',
                'string',
                'regex:/^[^\s]+$/',
                'unique:secure_user,username',
            ],
            'password' => 'required|string|min:6',
            'role_id' => 'nullable|exists:roles,id',
            'people_id' => 'nullable|exists:peoples,id|unique:secure_user,people_id', // ✅ tidak boleh sama
        ], $messages);

        $data = $request->only(['username', 'role_id', 'people_id', 'status']);
        $signature = $this->generateSignature($data);

        $user = SecureUser::create([
            ...$data,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'User berhasil dibuat dengan aman.',
            'signature' => $signature,
            'user' => $user,
        ], 201);
    }



    /**
     * Tampilkan pengguna tertentu.
     */
    public function show($id)
    {
        $user = SecureUser::with(['role', 'people'])->findOrFail($id);
        return response()->json($user);
    }

    /**
     * Update pengguna tertentu.
     */
    public function update(Request $request, $id)
    {
        $user = SecureUser::findOrFail($id);

        $request->validate([
            'username' => 'sometimes|required|string|unique:secure_user,username,' . $user->id,
            'password' => 'sometimes|nullable|string|min:6',
            'role_id' => 'nullable|exists:roles,id',
            'people_id' => 'nullable|exists:peoples,id',
            'status' => 'nullable|in:active,inactive',
        ]);

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->update($request->only(['username', 'role_id', 'people_id', 'status']));

        return response()->json($user);
    }

    /**
     * Hapus pengguna.
     */
    public function destroy($id)
    {
        $user = SecureUser::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully.']);
    }
}
