<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asrama;
use Illuminate\Http\Request;

class AsramaController extends Controller
{
    /**
     * Display a listing of asrama.
     */
    public function index(Request $request)
    {
        $query = Asrama::with('wilayah');

        // Filter by wilayah
        if ($request->has('wilayah_id') && $request->wilayah_id) {
            $query->where('wilayah_id', $request->wilayah_id);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%");
            });
        }

        $asrama = $query->orderBy('wilayah_id')->orderBy('nama')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $asrama
        ]);
    }

    /**
     * Get asrama by wilayah.
     */
    public function getByWilayah($wilayahId)
    {
        $currentUser = auth()->user();

        // Check access to this wilayah
        if (!$currentUser->canAccessWilayah($wilayahId)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $asrama = Asrama::where('wilayah_id', $wilayahId)
            ->select('id', 'nama', 'kode', 'kapasitas')
            ->orderBy('nama')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'asrama' => $asrama
            ]
        ]);
    }

    /**
     * Display the specified asrama.
     */
    public function show(Asrama $asrama)
    {
        $currentUser = auth()->user();

        // Check access to this asrama's wilayah
        if (!$currentUser->canAccessWilayah($asrama->wilayah_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $asrama->load('wilayah', 'paket');

        return response()->json([
            'success' => true,
            'data' => [
                'asrama' => $asrama
            ]
        ]);
    }

    /**
     * Store a newly created asrama.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:10|unique:asrama',
            'wilayah_id' => 'required|exists:wilayah,id',
            'kapasitas' => 'nullable|integer|min:1',
        ]);

        $asrama = Asrama::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Asrama created successfully',
            'data' => [
                'asrama' => $asrama->load('wilayah')
            ]
        ], 201);
    }

    /**
     * Update the specified asrama.
     */
    public function update(Request $request, Asrama $asrama)
    {
        $request->validate([
            'nama' => 'sometimes|required|string|max:255|unique:asrama,nama,' . $asrama->id,
            'kode' => 'sometimes|required|string|max:10|unique:asrama,kode,' . $asrama->id,
            'wilayah_id' => 'sometimes|required|exists:wilayah,id',
            'kapasitas' => 'nullable|integer|min:1',
        ]);

        $asrama->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Asrama updated successfully',
            'data' => [
                'asrama' => $asrama->fresh()->load('wilayah')
            ]
        ]);
    }

    /**
     * Remove the specified asrama.
     */
    public function destroy(Asrama $asrama)
    {
        // Check if asrama has dependencies
        if ($asrama->paket()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete asrama with existing paket'
            ], 422);
        }

        $asrama->delete();

        return response()->json([
            'success' => true,
            'message' => 'Asrama deleted successfully'
        ]);
    }
}