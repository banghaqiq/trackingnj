<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Paket;
use App\Enums\PaketStatus;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display dashboard data.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Paket::query();

        // Filter by user role
        if ($user->isKeamanan()) {
            $query->where('wilayah_id', $user->wilayah_id);
        }

        // Filter by date range (default: last 30 days)
        $tanggalDari = $request->tanggal_dari ?? now()->subDays(30)->toDateString();
        $tanggalSampai = $request->tanggal_sampai ?? now()->toDateString();

        $query->whereDate('tanggal_diterima', '>=', $tanggalDari)
              ->whereDate('tanggal_diterima', '<=', $tanggalSampai);

        // Overall statistics
        $stats = [
            'total_paket' => (clone $query)->count(),
            'diterima' => (clone $query)->where('status', PaketStatus::DITERIMA)->count(),
            'diproses' => (clone $query)->where('status', PaketStatus::DIPROSES)->count(),
            'diantar' => (clone $query)->where('status', PaketStatus::DIANTAR)->count(),
            'selesai' => (clone $query)->where('status', PaketStatus::SELESAI)->count(),
            'dikembalikan' => (clone $query)->where('status', PaketStatus::DIKEMBALIKAN)->count(),
        ];

        // Daily statistics for chart
        $dailyStats = Paket::selectRaw('DATE(tanggal_diterima) as date, COUNT(*) as total')
            ->whereDate('tanggal_diterima', '>=', $tanggalDari)
            ->whereDate('tanggal_diterima', '<=', $tanggalSampai)
            ->when($user->isKeamanan(), function ($q) use ($user) {
                $q->where('wilayah_id', $user->wilayah_id);
            })
            ->groupByRaw('DATE(tanggal_diterima)')
            ->orderBy('date')
            ->get();

        // Status distribution
        $statusDist = Paket::selectRaw('status, COUNT(*) as count')
            ->when($user->isKeamanan(), function ($q) use ($user) {
                $q->where('wilayah_id', $user->wilayah_id);
            })
            ->whereDate('tanggal_diterima', '>=', $tanggalDari)
            ->whereDate('tanggal_diterima', '<=', $tanggalSampai)
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // Recent activity (last 10 paket)
        $recentPaket = Paket::with(['wilayah', 'asrama'])
            ->when($user->isKeamanan(), function ($q) use ($user) {
                $q->where('wilayah_id', $user->wilayah_id);
            })
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Performance metrics
        $performanceMetrics = [];
        if (!$user->isKeamanan()) {
            // Only show performance metrics for Admin and Petugas Pos
            $totalSelesai = (clone $query)->where('status', PaketStatus::SELESAI)->count();
            
            $avgProcessingTime = Paket::selectRaw('AVG(DATEDIFF(tanggal_diambil, tanggal_diterima)) as avg_days')
                ->whereNotNull('tanggal_diambil')
                ->where('status', PaketStatus::SELESAI)
                ->when($user->isKeamanan(), function ($q) use ($user) {
                    $q->where('wilayah_id', $user->wilayah_id);
                })
                ->first()->avg_days ?? 0;

            $performanceMetrics = [
                'total_selesai' => $totalSelesai,
                'avg_processing_days' => round($avgProcessingTime, 1),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'daily_stats' => $dailyStats,
                'status_distribution' => $statusDist,
                'recent_paket' => $recentPaket,
                'performance_metrics' => $performanceMetrics,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role->value,
                    'wilayah_id' => $user->wilayah_id,
                    'wilayah' => $user->wilayah?->nama,
                ],
                'date_range' => [
                    'tanggal_dari' => $tanggalDari,
                    'tanggal_sampai' => $tanggalSampai,
                ]
            ]
        ]);
    }
}