<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'wilayah_id',
        'is_active',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => UserRole::class,
        'is_active' => 'boolean',
    ];

    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function paketDiterima(): HasMany
    {
        return $this->hasMany(Paket::class, 'diterima_oleh');
    }

    public function paketDiantar(): HasMany
    {
        return $this->hasMany(Paket::class, 'diantar_oleh');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(PaketStatusLog::class, 'diubah_oleh');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isPetugasPos(): bool
    {
        return $this->role === UserRole::PETUGAS_POS;
    }

    public function isKeamanan(): bool
    {
        return $this->role === UserRole::KEAMANAN;
    }
}
