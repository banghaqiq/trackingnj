<?php

namespace App\Http\Controllers;

use App\Enums\PaketStatus;
use App\Models\Paket;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $baseQuery = Paket::query();
        if ($user) {
            $baseQuery->visibleTo($user);
        }

        $today = Carbon::today();

        $todayDiterima = (clone $baseQuery)
            ->whereDate('tanggal_diterima', $today)
            ->count();

        $todayDiambil = (clone $baseQuery)
            ->whereDate('tanggal_diambil', $today)
            ->count();

        $belumDiambil = (clone $baseQuery)
            ->where('status', PaketStatus::DIANTAR)
            ->count();

        $salahWilayah = (clone $baseQuery)
            ->where(function ($q) {
                $q->whereNull('wilayah_id')
                    ->orWhere('tanpa_wilayah', true);
            })
            ->count();

        return view('dashboard.index', [
            'todayDiterima' => $todayDiterima,
            'todayDiambil' => $todayDiambil,
            'belumDiambil' => $belumDiambil,
            'salahWilayah' => $salahWilayah,
        ]);
    }

    public function chartData(Request $request): JsonResponse
    {
        $user = $request->user();
        $period = $request->query('period', 'daily');

        $baseQuery = Paket::query();
        if ($user) {
            $baseQuery->visibleTo($user);
        }

        $driver = DB::connection()->getDriverName();

        if ($period === 'weekly') {
            $start = Carbon::now()->startOfWeek()->subWeeks(7);
            $end = Carbon::now()->endOfWeek();

            $groupExpr = $driver === 'sqlite'
                ? "strftime('%Y-W%W', tanggal_diterima)"
                : "DATE_FORMAT(tanggal_diterima, '%x-W%v')";

            $rows = (clone $baseQuery)
                ->whereBetween('tanggal_diterima', [$start, $end])
                ->selectRaw("{$groupExpr} as label")
                ->selectRaw('count(*) as total')
                ->groupBy('label')
                ->orderBy('label')
                ->get();
        } elseif ($period === 'monthly') {
            $start = Carbon::now()->startOfMonth()->subMonths(11);
            $end = Carbon::now()->endOfMonth();

            $groupExpr = $driver === 'sqlite'
                ? "strftime('%Y-%m', tanggal_diterima)"
                : "DATE_FORMAT(tanggal_diterima, '%Y-%m')";

            $rows = (clone $baseQuery)
                ->whereBetween('tanggal_diterima', [$start, $end])
                ->selectRaw("{$groupExpr} as label")
                ->selectRaw('count(*) as total')
                ->groupBy('label')
                ->orderBy('label')
                ->get();
        } else {
            $start = Carbon::today()->subDays(13);
            $end = Carbon::today()->endOfDay();

            $rows = (clone $baseQuery)
                ->whereBetween('tanggal_diterima', [$start, $end])
                ->selectRaw('date(tanggal_diterima) as label')
                ->selectRaw('count(*) as total')
                ->groupBy('label')
                ->orderBy('label')
                ->get();
        }

        return response()->json([
            'labels' => $rows->pluck('label')->values(),
            'data' => $rows->pluck('total')->values(),
        ]);
    }
}
