<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asrama extends Model
{
    use HasFactory;

    protected $table = 'asrama';

    protected $fillable = [
        'wilayah_id',
        'nama',
        'kode',
        'alamat',
        'kapasitas',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'kapasitas' => 'integer',
    ];

    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function paket(): HasMany
    {
        return $this->hasMany(Paket::class);
    }
}
