<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = \App\Models\Paket::query();

        // Filter by user role
        if ($user->isKeamanan()) {
            $query->where('wilayah_id', $user->wilayah_id);
        }

        // Filter by date range (default: last 30 days)
        $tanggalDari = $request->get('tanggal_dari') ?? now()->subDays(30)->toDateString();
        $tanggalSampai = $request->get('tanggal_sampai') ?? now()->toDateString();

        $query->whereDate('tanggal_diterima', '>=', $tanggalDari)
              ->whereDate('tanggal_diterima', '<=', $tanggalSampai);

        // Overall statistics
        $stats = [
            'total_paket' => (clone $query)->count(),
            'diterima' => (clone $query)->where('status', 'diterima')->count(),
            'diproses' => (clone $query)->where('status', 'diproses')->count(),
            'diantar' => (clone $query)->where('status', 'diantar')->count(),
            'selesai' => (clone $query)->where('status', 'selesai')->count(),
            'dikembalikan' => (clone $query)->where('status', 'dikembalikan')->count(),
        ];

        // Recent paket
        $recentPaket = \App\Models\Paket::with(['wilayah', 'asrama'])
            ->when($user->isKeamanan(), function ($q) use ($user) {
                $q->where('wilayah_id', $user->wilayah_id);
            })
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Quick actions based on user role
        $quickActions = [];
        if ($user->isAdmin() || $user->isPetugasPos()) {
            $quickActions[] = [
                'title' => 'Tambah Paket Baru',
                'url' => route('paket.create'),
                'icon' => 'plus',
                'color' => 'primary'
            ];
        }

        if ($user->isAdmin()) {
            $quickActions[] = [
                'title' => 'Kelola User',
                'url' => route('users.index'),
                'icon' => 'users',
                'color' => 'success'
            ];
        }

        $quickActions[] = [
            'title' => 'Lihat Laporan',
            'url' => route('reports.paket'),
            'icon' => 'chart-bar',
            'color' => 'info'
        ];

        return view('dashboard.index', compact(
            'user', 'stats', 'recentPaket', 'quickActions', 'tanggalDari', 'tanggalSampai'
        ));
    }
}