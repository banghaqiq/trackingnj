<?php

namespace App\Models;

use App\Enums\PaketStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paket extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'paket';

    protected $fillable = [
        'kode_resi',
        'nama_penerima',
        'telepon_penerima',
        'wilayah_id',
        'asrama_id',
        'nomor_kamar',
        'alamat_lengkap',
        'tanpa_wilayah',
        'keluarga',
        'nama_pengirim',
        'keterangan',
        'status',
        'tanggal_diterima',
        'tanggal_diambil',
        'diterima_oleh',
        'diantar_oleh',
    ];

    protected $casts = [
        'tanpa_wilayah' => 'boolean',
        'keluarga' => 'boolean',
        'status' => PaketStatus::class,
        'tanggal_diterima' => 'datetime',
        'tanggal_diambil' => 'datetime',
    ];

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isKeamanan()) {
            return $query->where('wilayah_id', $user->wilayah_id);
        }

        return $query;
    }

    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function asrama(): BelongsTo
    {
        return $this->belongsTo(Asrama::class);
    }

    public function diterimaOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diterima_oleh');
    }

    public function diantarOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diantar_oleh');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(PaketStatusLog::class);
    }

    public function updateStatus(PaketStatus $newStatus, ?int $userId = null, ?string $catatan = null): void
    {
        $oldStatus = $this->status;
        
        $this->update(['status' => $newStatus]);

        PaketStatusLog::create([
            'paket_id' => $this->id,
            'status_dari' => $oldStatus,
            'status_ke' => $newStatus,
            'diubah_oleh' => $userId,
            'catatan' => $catatan,
        ]);
    }

    public function isDiterima(): bool
    {
        return $this->status === PaketStatus::DITERIMA;
    }

    public function isDiproses(): bool
    {
        return $this->status === PaketStatus::DIPROSES;
    }

    public function isDiantar(): bool
    {
        return $this->status === PaketStatus::DIANTAR;
    }

    public function isSelesai(): bool
    {
        return $this->status === PaketStatus::SELESAI;
    }

    public function isDikembalikan(): bool
    {
        return $this->status === PaketStatus::DIKEMBALIKAN;
    }
}
