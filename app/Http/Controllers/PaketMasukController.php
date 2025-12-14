<?php

namespace App\Http\Controllers;

use App\Services\PaketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaketMasukController extends Controller
{
    protected PaketService $paketService;

    public function __construct(PaketService $paketService)
    {
        $this->paketService = $paketService;
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalAkhir = $request->input('tanggal_akhir');

        $paket = $this->paketService->getPaketMasuk(
            $search,
            $tanggalMulai,
            $tanggalAkhir,
            $perPage,
            Auth::user()
        );

        return view('paket.masuk', compact('paket'));
    }
}
