<?php

namespace App\Http\Controllers;

use App\Enums\PaketStatus;
use App\Models\Paket;
use App\Models\Wilayah;
use App\Models\Asrama;
use App\Services\PaketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaketController extends Controller
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
        $status = $request->input('status');
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalAkhir = $request->input('tanggal_akhir');

        $paket = $this->paketService->getAllPaket(
            $search,
            $status,
            $tanggalMulai,
            $tanggalAkhir,
            $perPage,
            Auth::user()
        );

        return view('paket.index', compact('paket'));
    }

    public function create()
    {
        $wilayah = Wilayah::all();
        $asrama = Asrama::with('wilayah')->get();

        return view('paket.create', compact('wilayah', 'asrama'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_resi' => 'required|string|max:255|unique:paket,kode_resi',
            'nama_penerima' => 'required|string|max:255',
            'telepon_penerima' => 'required|string|max:20',
            'wilayah_id' => 'nullable|exists:wilayah,id',
            'asrama_id' => 'nullable|exists:asrama,id',
            'nomor_kamar' => 'nullable|string|max:20',
            'alamat_lengkap' => 'nullable|string',
            'tanpa_wilayah' => 'boolean',
            'keluarga' => 'boolean',
            'nama_pengirim' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $paket = $this->paketService->createPaket($validated, Auth::user());

        return redirect()
            ->route('paket.index')
            ->with('success', __('paket.created_successfully'));
    }

    public function show(Paket $paket)
    {
        $paket->load(['wilayah', 'asrama', 'diterimaOleh', 'diantarOleh', 'statusLogs.diubahOleh']);

        return view('paket.show', compact('paket'));
    }

    public function edit(Paket $paket)
    {
        $wilayah = Wilayah::all();
        $asrama = Asrama::with('wilayah')->get();

        return view('paket.edit', compact('paket', 'wilayah', 'asrama'));
    }

    public function update(Request $request, Paket $paket)
    {
        $validated = $request->validate([
            'kode_resi' => 'required|string|max:255|unique:paket,kode_resi,' . $paket->id,
            'nama_penerima' => 'required|string|max:255',
            'telepon_penerima' => 'required|string|max:20',
            'wilayah_id' => 'nullable|exists:wilayah,id',
            'asrama_id' => 'nullable|exists:asrama,id',
            'nomor_kamar' => 'nullable|string|max:20',
            'alamat_lengkap' => 'nullable|string',
            'tanpa_wilayah' => 'boolean',
            'keluarga' => 'boolean',
            'nama_pengirim' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $this->paketService->updatePaket($paket, $validated, Auth::user());

        return redirect()
            ->route('paket.show', $paket)
            ->with('success', __('paket.updated_successfully'));
    }

    public function updateStatus(Request $request, Paket $paket)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', PaketStatus::values()),
            'catatan' => 'nullable|string',
        ]);

        try {
            $this->paketService->updateStatus(
                $paket,
                PaketStatus::from($validated['status']),
                Auth::user(),
                $validated['catatan'] ?? null
            );

            return back()->with('success', __('paket.status_updated_successfully'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Paket $paket)
    {
        $this->paketService->softDeletePaket($paket, Auth::user());

        return redirect()
            ->route('paket.index')
            ->with('success', __('paket.deleted_successfully'));
    }

    public function forceDestroy($id)
    {
        $paket = Paket::withTrashed()->findOrFail($id);

        try {
            $this->paketService->forceDeletePaket($paket, Auth::user());

            return redirect()
                ->route('paket.index')
                ->with('success', __('paket.permanently_deleted_successfully'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function restore($id)
    {
        $this->paketService->restorePaket($id, Auth::user());

        return redirect()
            ->route('paket.index')
            ->with('success', __('paket.restored_successfully'));
    }

    public function checkResi(Request $request)
    {
        $kodeResi = $request->input('kode_resi');
        $exists = Paket::where('kode_resi', $kodeResi)->exists();

        return response()->json(['exists' => $exists]);
    }
}
