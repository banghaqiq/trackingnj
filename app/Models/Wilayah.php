<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wilayah extends Model
{
    use HasFactory;

    protected $table = 'wilayah';

    protected $fillable = [
        'nama',
        'kode',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function asrama(): HasMany
    {
        return $this->hasMany(Asrama::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function paket(): HasMany
    {
        return $this->hasMany(Paket::class);
    }
}
