<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Paket;
use App\Enums\PaketStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class PaketController extends Controller
{
    /**
     * Display a listing of paket.
     */
    public function index(Request $request)
    {
        $query = Paket::with(['wilayah', 'asrama', 'diterimaOleh', 'diantarOleh']);

        $user = auth()->user();

        // Filter by user role
        if ($user->isKeamanan()) {
            $query->where('wilayah_id', $user->wilayah_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by wilayah
        if ($request->has('wilayah_id') && $request->wilayah_id) {
            $query->where('wilayah_id', $request->wilayah_id);
        }

        // Filter by asrama
        if ($request->has('asrama_id') && $request->asrama_id) {
            $query->where('asrama_id', $request->asrama_id);
        }

        // Filter by date range
        if ($request->has('tanggal_dari') && $request->tanggal_dari) {
            $query->whereDate('tanggal_diterima', '>=', $request->tanggal_dari);
        }

        if ($request->has('tanggal_sampai') && $request->tanggal_sampai) {
            $query->whereDate('tanggal_diterima', '<=', $request->tanggal_sampai);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_resi', 'like', "%{$search}%")
                  ->orWhere('nama_penerima', 'like', "%{$search}%")
                  ->orWhere('telepon_penerima', 'like', "%{$search}%");
            });
        }

        // Special filters
        if ($request->has('tanpa_wilayah')) {
            $query->where('tanpa_wilayah', $request->boolean('tanpa_wilayah'));
        }

        if ($request->has('keluarga')) {
            $query->where('keluarga', $request->boolean('keluarga'));
        }

        $pakets = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $pakets
        ]);
    }

    /**
     * Display the specified paket.
     */
    public function show(Paket $paket)
    {
        $user = auth()->user();

        // Check access based on role
        if (!$user->canAccessWilayah($paket->wilayah_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $paket->load([
            'wilayah',
            'asrama',
            'diterimaOleh',
            'diantarOleh',
            'statusLogs.diubahOleh'
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'paket' => $paket
            ]
        ]);
    }

    /**
     * Store a newly created paket.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        // Only Admin and Petugas Pos can create packages
        if (!$user->isAdmin() && !$user->isPetugasPos()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $request->validate([
            'kode_resi' => 'required|string|max:50|unique:paket,kode_resi',
            'nama_penerima' => 'required|string|max:255',
            'telepon_penerima' => 'required|string|max:20',
            'wilayah_id' => 'nullable|exists:wilayah,id',
            'asrama_id' => 'nullable|exists:asrama,id',
            'nomor_kamar' => 'nullable|string|max:20',
            'status' => 'required|in:diterima,diproses,diantar,selesai,dikembalikan',
            'tanpa_wilayah' => 'boolean',
            'keluarga' => 'boolean',
            'catatan' => 'nullable|string|max:1000',
        ]);

        // Validate wilayah-asrama relationship
        if ($request->wilayah_id && $request->asrama_id) {
            $asrama = \App\Models\Asrama::where('id', $request->asrama_id)
                ->where('wilayah_id', $request->wilayah_id)
                ->first();

            if (!$asrama) {
                throw ValidationException::withMessages([
                    'asrama_id' => ['Asrama does not belong to the selected wilayah.']
                ]);
            }
        }

        // Validate without wilayah
        if ($request->boolean('tanpa_wilayah') && ($request->wilayah_id || $request->asrama_id)) {
            throw ValidationException::withMessages([
                'tanpa_wilayah' => ['Tanpa wilayah paket cannot have wilayah or asrama.']
            ]);
        }

        $paket = Paket::create([
            'kode_resi' => $request->kode_resi,
            'nama_penerima' => $request->nama_penerima,
            'telepon_penerima' => $request->telepon_penerima,
            'wilayah_id' => $request->wilayah_id,
            'asrama_id' => $request->asrama_id,
            'nomor_kamar' => $request->nomor_kamar,
            'status' => $request->status,
            'tanpa_wilayah' => $request->boolean('tanpa_wilayah', false),
            'keluarga' => $request->boolean('keluarga', false),
            'catatan' => $request->catatan,
            'tanggal_diterima' => now(),
            'diterima_oleh' => $user->id,
        ]);

        // Create initial status log
        $paket->statusLogs()->create([
            'status_sebelum' => null,
            'status_sesudah' => $request->status,
            'diubah_oleh' => $user->id,
            'catatan' => 'Paket dibuat',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Paket created successfully',
            'data' => [
                'paket' => $paket->load(['wilayah', 'asrama', 'diterimaOleh'])
            ]
        ], 201);
    }

    /**
     * Update the specified paket.
     */
    public function update(Request $request, Paket $paket)
    {
        $user = auth()->user();

        // Check access based on role
        if (!$user->canAccessWilayah($paket->wilayah_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $request->validate([
            'nama_penerima' => 'sometimes|required|string|max:255',
            'telepon_penerima' => 'sometimes|required|string|max:20',
            'wilayah_id' => 'nullable|exists:wilayah,id',
            'asrama_id' => 'nullable|exists:asrama,id',
            'nomor_kamar' => 'nullable|string|max:20',
            'tanpa_wilayah' => 'boolean',
            'keluarga' => 'boolean',
            'catatan' => 'nullable|string|max:1000',
        ]);

        // Validate wilayah-asrama relationship
        if ($request->wilayah_id && $request->asrama_id) {
            $asrama = \App\Models\Asrama::where('id', $request->asrama_id)
                ->where('wilayah_id', $request->wilayah_id)
                ->first();

            if (!$asrama) {
                throw ValidationException::withMessages([
                    'asrama_id' => ['Asrama does not belong to the selected wilayah.']
                ]);
            }
        }

        $updateData = $request->only([
            'nama_penerima', 'telepon_penerima', 'wilayah_id', 'asrama_id', 
            'nomor_kamar', 'tanpa_wilayah', 'keluarga', 'catatan'
        ]);

        $paket->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Paket updated successfully',
            'data' => [
                'paket' => $paket->fresh()->load(['wilayah', 'asrama'])
            ]
        ]);
    }

    /**
     * Update paket status.
     */
    public function updateStatus(Request $request, Paket $paket)
    {
        $user = auth()->user();

        // Check access based on role
        if (!$user->canAccessWilayah($paket->wilayah_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $request->validate([
            'status' => 'required|in:diterima,diproses,diantar,selesai,dikembalikan',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $newStatus = PaketStatus::from($request->status);
        
        // Update paket status using the model method
        $paket->updateStatus(
            $newStatus,
            $user->id,
            $request->catatan
        );

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'data' => [
                'paket' => $paket->fresh()->load(['wilayah', 'asrama', 'diterimaOleh', 'diantarOleh'])
            ]
        ]);
    }

    /**
     * Remove the specified paket.
     */
    public function destroy(Paket $paket)
    {
        $user = auth()->user();

        // Only Admin can delete packages
        if (!$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $paket->delete();

        return response()->json([
            'success' => true,
            'message' => 'Paket deleted successfully'
        ]);
    }

    /**
     * Restore soft deleted paket.
     */
    public function restore(Paket $paket)
    {
        $user = auth()->user();

        // Only Admin can restore packages
        if (!$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $paket->restore();

        return response()->json([
            'success' => true,
            'message' => 'Paket restored successfully',
            'data' => [
                'paket' => $paket->fresh()->load(['wilayah', 'asrama'])
            ]
        ]);
    }

    /**
     * Get paket status logs.
     */
    public function getStatusLogs(Paket $paket)
    {
        $user = auth()->user();

        // Check access based on role
        if (!$user->canAccessWilayah($paket->wilayah_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $statusLogs = $paket->statusLogs()
            ->with('diubahOleh:id,name,username')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'status_logs' => $statusLogs
            ]
        ]);
    }

    /**
     * Get paket statistics summary.
     */
    public function getStatsSummary(Request $request)
    {
        $user = auth()->user();

        $query = Paket::query();

        // Filter by user role
        if ($user->isKeamanan()) {
            $query->where('wilayah_id', $user->wilayah_id);
        }

        // Filter by date range
        if ($request->has('tanggal_dari') && $request->tanggal_dari) {
            $query->whereDate('tanggal_diterima', '>=', $request->tanggal_dari);
        }

        if ($request->has('tanggal_sampai') && $request->tanggal_sampai) {
            $query->whereDate('tanggal_diterima', '<=', $request->tanggal_sampai);
        }

        $stats = [
            'total' => $query->count(),
            'diterima' => (clone $query)->where('status', PaketStatus::DITERIMA)->count(),
            'diproses' => (clone $query)->where('status', PaketStatus::DIPROSES)->count(),
            'diantar' => (clone $query)->where('status', PaketStatus::DIANTAR)->count(),
            'selesai' => (clone $query)->where('status', PaketStatus::SELESAI)->count(),
            'dikembalikan' => (clone $query)->where('status', PaketStatus::DIKEMBALIKAN)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats
            ]
        ]);
    }

    /**
     * Get paket statistics by status.
     */
    public function getStatsByStatus(Request $request)
    {
        $user = auth()->user();

        $query = Paket::query();

        // Filter by user role
        if ($user->isKeamanan()) {
            $query->where('wilayah_id', $user->wilayah_id);
        }

        // Filter by date range
        if ($request->has('tanggal_dari') && $request->tanggal_dari) {
            $query->whereDate('tanggal_diterima', '>=', $request->tanggal_dari);
        }

        if ($request->has('tanggal_sampai') && $request->tanggal_sampai) {
            $query->whereDate('tanggal_diterima', '<=', $request->tanggal_sampai);
        }

        $stats = $query->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats
            ]
        ]);
    }

    /**
     * Get paket statistics by wilayah.
     */
    public function getStatsByWilayah(Request $request)
    {
        $user = auth()->user();

        $query = Paket::with('wilayah');

        // Filter by user role
        if ($user->isKeamanan()) {
            $query->where('wilayah_id', $user->wilayah_id);
        }

        // Filter by date range
        if ($request->has('tanggal_dari') && $request->tanggal_dari) {
            $query->whereDate('tanggal_diterima', '>=', $request->tanggal_dari);
        }

        if ($request->has('tanggal_sampai') && $request->tanggal_sampai) {
            $query->whereDate('tanggal_diterima', '<=', $request->tanggal_sampai);
        }

        $stats = $query->selectRaw('wilayah_id, COUNT(*) as total_paket')
            ->groupBy('wilayah_id')
            ->with('wilayah')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats
            ]
        ]);
    }
}