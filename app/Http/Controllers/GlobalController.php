<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class GlobalController extends Controller
{
    protected string $modelClass;

    public function __construct(string $modelClass)
    {
        $this->modelClass = $modelClass;
    }

    // 🔹 Ambil semua data
    public function index()
    {
        $items = $this->modelClass::all();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diambil.',
            'data' => $items,
        ], 200);
    }

    // 🔹 Simpan data baru
    public function store(Request $request)
    {
        try {
            $item = $this->modelClass::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan.',
                'data' => $item,
            ], 201);
        } catch (QueryException $e) {
            // Duplicate entry
            if ($e->getCode() == '23000') {
                return response()->json([
                    'success' => false,
                    'message' => 'Data dengan nilai yang sama sudah ada. Silakan gunakan nama lain.',
                ], 409);
            }

            Log::error('Kesalahan saat menyimpan data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.',
            ], 500);
        }
    }

    // 🔹 Ambil data untuk edit
    public function edit($id)
    {
        $item = $this->modelClass::find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diambil.',
            'data' => $item,
        ]);
    }

    // 🔹 Update data
    public function update(Request $request, $id)
    {
        $item = $this->modelClass::find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        try {
            $item->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui.',
                'data' => $item,
            ], 200);
        } catch (QueryException $e) {
            if ($e->getCode() == '23000') {
                return response()->json([
                    'success' => false,
                    'message' => 'Data dengan nilai yang sama sudah ada. Silakan gunakan nama lain.',
                ], 409);
            }

            Log::error('Kesalahan saat memperbarui data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data.',
            ], 500);
        }
    }

    // 🔹 Hapus data
    public function destroy($id)
    {
        $item = $this->modelClass::find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        try {
            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus.'
            ]);
        } catch (QueryException $e) {
            if ($e->getCode() == '23000') {
                // Foreign key constraint
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak dapat dihapus karena masih digunakan dalam relasi lain.'
                ], 409);
            }

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data.'
            ], 500);
        }
    }
}
