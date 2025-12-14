<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wilayah;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    /**
     * Display a listing of wilayah.
     */
    public function index(Request $request)
    {
        $query = Wilayah::withCount('asrama');

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%");
        }

        $wilayah = $query->orderBy('nama')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $wilayah
        ]);
    }

    /**
     * Display the specified wilayah.
     */
    public function show(Wilayah $wilayah)
    {
        $wilayah->loadCount('asrama', 'paket');

        return response()->json([
            'success' => true,
            'data' => [
                'wilayah' => $wilayah
            ]
        ]);
    }

    /**
     * Store a newly created wilayah.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:wilayah',
            'kode' => 'required|string|max:10|unique:wilayah',
            'is_active' => 'boolean',
        ]);

        $wilayah = Wilayah::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Wilayah created successfully',
            'data' => [
                'wilayah' => $wilayah
            ]
        ], 201);
    }

    /**
     * Update the specified wilayah.
     */
    public function update(Request $request, Wilayah $wilayah)
    {
        $request->validate([
            'nama' => 'sometimes|required|string|max:255|unique:wilayah,nama,' . $wilayah->id,
            'kode' => 'sometimes|required|string|max:10|unique:wilayah,kode,' . $wilayah->id,
            'is_active' => 'boolean',
        ]);

        $wilayah->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Wilayah updated successfully',
            'data' => [
                'wilayah' => $wilayah->fresh()
            ]
        ]);
    }

    /**
     * Remove the specified wilayah.
     */
    public function destroy(Wilayah $wilayah)
    {
        // Check if wilayah has dependencies
        if ($wilayah->asrama()->count() > 0 || $wilayah->paket()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete wilayah with existing asrama or paket'
            ], 422);
        }

        $wilayah->delete();

        return response()->json([
            'success' => true,
            'message' => 'Wilayah deleted successfully'
        ]);
    }
}