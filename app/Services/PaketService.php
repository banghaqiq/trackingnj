<?php

namespace App\Services;

use App\Enums\PaketStatus;
use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\Paket;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PaketService
{
    public function getAllPaket(
        ?string $search = null,
        ?string $status = null,
        ?string $tanggalMulai = null,
        ?string $tanggalAkhir = null,
        int $perPage = 10,
        ?User $user = null
    ): LengthAwarePaginator {
        $query = Paket::with(['wilayah', 'asrama', 'diterimaOleh', 'diantarOleh']);

        if ($user && $user->role === UserRole::KEAMANAN) {
            $query->where('wilayah_id', $user->wilayah_id);
        }

        $this->applyFilters($query, $search, $status, $tanggalMulai, $tanggalAkhir);

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getPaketMasuk(
        ?string $search = null,
        ?string $tanggalMulai = null,
        ?string $tanggalAkhir = null,
        int $perPage = 10,
        ?User $user = null
    ): LengthAwarePaginator {
        $query = Paket::with(['wilayah', 'asrama', 'diterimaOleh', 'diantarOleh'])
            ->whereIn('status', [PaketStatus::BELUM_DIAMBIL, PaketStatus::DIAMBIL]);

        if ($user && $user->role === UserRole::KEAMANAN) {
            $query->where('wilayah_id', $user->wilayah_id);
        }

        $this->applyFilters($query, $search, null, $tanggalMulai, $tanggalAkhir);

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getPaketKeluar(
        ?string $search = null,
        ?string $tanggalMulai = null,
        ?string $tanggalAkhir = null,
        int $perPage = 10,
        ?User $user = null
    ): LengthAwarePaginator {
        $query = Paket::with(['wilayah', 'asrama', 'diterimaOleh', 'diantarOleh'])
            ->whereIn('status', [PaketStatus::DITERIMA, PaketStatus::SELESAI, PaketStatus::DIKEMBALIKAN, PaketStatus::SALAH_WILAYAH]);

        if ($user && $user->role === UserRole::KEAMANAN) {
            $query->where('wilayah_id', $user->wilayah_id);
        }

        $this->applyFilters($query, $search, null, $tanggalMulai, $tanggalAkhir);

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function createPaket(array $data, User $user): Paket
    {
        return DB::transaction(function () use ($data, $user) {
            $data['status'] = PaketStatus::BELUM_DIAMBIL;
            $data['tanggal_diterima'] = now();
            $data['diterima_oleh'] = $user->id;

            $paket = Paket::create($data);

            $paket->statusLogs()->create([
                'status_dari' => null,
                'status_ke' => PaketStatus::BELUM_DIAMBIL,
                'diubah_oleh' => $user->id,
                'catatan' => 'Paket diterima',
            ]);

            AuditLog::log(
                $user,
                'create_paket',
                'Paket',
                $paket->id,
                null,
                $paket->toArray()
            );

            return $paket;
        });
    }

    public function updatePaket(Paket $paket, array $data, User $user): Paket
    {
        return DB::transaction(function () use ($paket, $data, $user) {
            $oldValues = $paket->toArray();

            $paket->update($data);

            AuditLog::log(
                $user,
                'update_paket',
                'Paket',
                $paket->id,
                $oldValues,
                $paket->toArray()
            );

            return $paket->fresh();
        });
    }

    public function updateStatus(Paket $paket, PaketStatus $newStatus, User $user, ?string $catatan = null): void
    {
        if (!$this->canUpdateStatus($paket, $newStatus, $user)) {
            throw new \Exception('Anda tidak memiliki izin untuk mengubah status ini');
        }

        DB::transaction(function () use ($paket, $newStatus, $user, $catatan) {
            $oldStatus = $paket->status;
            
            $updateData = ['status' => $newStatus];
            
            if ($newStatus === PaketStatus::DIAMBIL && !$paket->tanggal_diambil) {
                $updateData['tanggal_diambil'] = now();
            }

            $paket->update($updateData);

            $paket->statusLogs()->create([
                'status_dari' => $oldStatus,
                'status_ke' => $newStatus,
                'diubah_oleh' => $user->id,
                'catatan' => $catatan,
            ]);

            AuditLog::log(
                $user,
                'update_status',
                'Paket',
                $paket->id,
                ['status' => $oldStatus->value],
                ['status' => $newStatus->value, 'catatan' => $catatan]
            );
        });
    }

    public function softDeletePaket(Paket $paket, User $user): void
    {
        DB::transaction(function () use ($paket, $user) {
            $oldValues = $paket->toArray();

            $paket->delete();

            AuditLog::log(
                $user,
                'soft_delete_paket',
                'Paket',
                $paket->id,
                $oldValues,
                ['deleted_at' => $paket->deleted_at]
            );
        });
    }

    public function forceDeletePaket(Paket $paket, User $user): void
    {
        if (!$this->canForceDelete($user)) {
            throw new \Exception('Hanya admin dan petugas pos yang dapat menghapus permanen');
        }

        DB::transaction(function () use ($paket, $user) {
            $oldValues = $paket->toArray();

            AuditLog::log(
                $user,
                'force_delete_paket',
                'Paket',
                $paket->id,
                $oldValues,
                null
            );

            $paket->forceDelete();
        });
    }

    public function restorePaket(int $paketId, User $user): Paket
    {
        return DB::transaction(function () use ($paketId, $user) {
            $paket = Paket::withTrashed()->findOrFail($paketId);
            
            $paket->restore();

            AuditLog::log(
                $user,
                'restore_paket',
                'Paket',
                $paket->id,
                ['deleted_at' => $paket->deleted_at],
                $paket->toArray()
            );

            return $paket;
        });
    }

    private function applyFilters(
        Builder $query,
        ?string $search,
        ?string $status,
        ?string $tanggalMulai,
        ?string $tanggalAkhir
    ): void {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_resi', 'like', "%{$search}%")
                    ->orWhere('nama_penerima', 'like', "%{$search}%")
                    ->orWhere('telepon_penerima', 'like', "%{$search}%")
                    ->orWhere('nama_pengirim', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($tanggalMulai) {
            $query->whereDate('tanggal_diterima', '>=', $tanggalMulai);
        }

        if ($tanggalAkhir) {
            $query->whereDate('tanggal_diterima', '<=', $tanggalAkhir);
        }
    }

    private function canUpdateStatus(Paket $paket, PaketStatus $newStatus, User $user): bool
    {
        if ($newStatus === PaketStatus::DIAMBIL) {
            return $user->role === UserRole::PETUGAS_POS;
        }

        if ($newStatus === PaketStatus::DITERIMA || $newStatus === PaketStatus::SALAH_WILAYAH) {
            return in_array($user->role, [UserRole::KEAMANAN, UserRole::PETUGAS_POS]);
        }

        return $user->role === UserRole::ADMIN || $user->role === UserRole::PETUGAS_POS;
    }

    private function canForceDelete(User $user): bool
    {
        return in_array($user->role, [UserRole::ADMIN, UserRole::PETUGAS_POS]);
    }
}
