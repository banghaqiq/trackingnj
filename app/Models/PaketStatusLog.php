<?php

namespace App\Models;

use App\Enums\PaketStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaketStatusLog extends Model
{
    use HasFactory;

    protected $table = 'paket_status_logs';

    public $timestamps = false;

    protected $fillable = [
        'paket_id',
        'status_dari',
        'status_ke',
        'diubah_oleh',
        'catatan',
    ];

    protected $casts = [
        'status_dari' => PaketStatus::class,
        'status_ke' => PaketStatus::class,
        'created_at' => 'datetime',
    ];

    public function paket(): BelongsTo
    {
        return $this->belongsTo(Paket::class);
    }

    public function diubahOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }
}
