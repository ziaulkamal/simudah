<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class GlobalController extends Controller
{
    protected string $modelClass;

    public function __construct(string $modelClass)
    {
        $this->modelClass = $modelClass;
    }

    // Tampilkan semua data
    public function index()
    {
        return response()->json($this->modelClass::all());
    }

    // Simpan data baru
    public function store(Request $request)
    {
        $data = $request->all();
        $item = $this->modelClass::create($data);

        return response()->json($item, 201);
    }

    // Ambil data untuk edit
    public function edit($id)
    {
        $item = $this->modelClass::find($id);
        if (!$item) {
            return response()->json(['error' => 'Not found'], 404);
        }
        return response()->json($item);
    }

    // Update data
    public function update(Request $request, $id)
    {
        $item = $this->modelClass::find($id);
        if (!$item) return response()->json(['error' => 'Not found'], 404);

        $item->update($request->all());
        return response()->json($item);
    }

    // Delete data
    public function destroy($id)
    {
        $item = $this->modelClass::find($id);
        if (!$item) return response()->json(['error' => 'Not found'], 404);

        $item->delete();
        return response()->json(['success' => true]);
    }
}
