<?php

namespace App\Http\Controllers;

use App\Enums\PaketStatus;
use App\Models\Paket;
use Illuminate\Http\Request;

class PaketKeluarController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Paket::query()->with(['wilayah', 'asrama']);
        if ($user) {
            $query->visibleTo($user);
        }

        $pakets = $query
            ->whereIn('status', [
                PaketStatus::DIPROSES,
                PaketStatus::DIANTAR,
                PaketStatus::SELESAI,
            ])
            ->orderByDesc('tanggal_diterima')
            ->paginate(25);

        return view('paket.keluar', [
            'pakets' => $pakets,
        ]);
    }
}
