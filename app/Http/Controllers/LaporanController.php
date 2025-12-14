<?php

namespace App\Http\Controllers;

use App\Exports\PaketLaporanExport;
use App\Models\Paket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        [$query, $periode, $from, $to] = $this->buildFilteredQuery($request);

        $pakets = (clone $query)
            ->with(['wilayah', 'asrama', 'diterimaOleh', 'diantarOleh'])
            ->orderByDesc('tanggal_diterima')
            ->paginate(25)
            ->withQueryString();

        return view('laporan.index', [
            'pakets' => $pakets,
            'periode' => $periode,
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function exportExcel(Request $request)
    {
        [$query, $periode, $from, $to] = $this->buildFilteredQuery($request);

        $fileName = sprintf(
            'laporan_paket_%s_%s_%s.xlsx',
            $periode,
            $from->format('Y-m-d'),
            $to->format('Y-m-d'),
        );

        return Excel::download(new PaketLaporanExport($query), $fileName);
    }

    public function exportPdf(Request $request)
    {
        [$query, $periode, $from, $to] = $this->buildFilteredQuery($request);

        Carbon::setLocale(app()->getLocale());

        $pakets = (clone $query)
            ->with(['wilayah', 'asrama', 'diterimaOleh', 'diantarOleh'])
            ->orderByDesc('tanggal_diterima')
            ->get();

        $fileName = sprintf(
            'laporan_paket_%s_%s_%s.pdf',
            $periode,
            $from->format('Y-m-d'),
            $to->format('Y-m-d'),
        );

        $pdf = Pdf::loadView('laporan.pdf', [
            'pakets' => $pakets,
            'periode' => $periode,
            'from' => $from,
            'to' => $to,
        ])->setPaper('a4', 'landscape');

        return $pdf->download($fileName);
    }

    /**
     * @return array{0: \Illuminate\Database\Eloquent\Builder, 1: string, 2: \Carbon\Carbon, 3: \Carbon\Carbon}
     */
    private function buildFilteredQuery(Request $request): array
    {
        $periode = $request->query('periode', 'day');

        $request->validate([
            'periode' => 'nullable|in:day,week,month',
            'tanggal' => 'nullable|date_format:Y-m-d',
        ]);

        $tanggal = $request->query('tanggal');
        $baseDate = $tanggal
            ? Carbon::createFromFormat('Y-m-d', $tanggal)
            : Carbon::today();

        if ($periode === 'week') {
            $from = $baseDate->copy()->startOfWeek();
            $to = $baseDate->copy()->endOfWeek();
        } elseif ($periode === 'month') {
            $from = $baseDate->copy()->startOfMonth();
            $to = $baseDate->copy()->endOfMonth();
        } else {
            $from = $baseDate->copy()->startOfDay();
            $to = $baseDate->copy()->endOfDay();
        }

        $query = Paket::query()->whereBetween('tanggal_diterima', [$from, $to]);

        if ($request->user()) {
            $query->visibleTo($request->user());
        }

        return [$query, $periode, $from, $to];
    }
}
