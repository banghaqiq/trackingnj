<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
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

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'role' => $this->role->value,
            'wilayah_id' => $this->wilayah_id,
            'is_active' => $this->is_active,
        ];
    }

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

    public function canAccessWilayah($wilayahId): bool
    {
        if ($this->isAdmin() || $this->isPetugasPos()) {
            return true;
        }

        return $this->isKeamanan() && $this->wilayah_id === $wilayahId;
    }

    public function canManageUser(User $user): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if ($this->isPetugasPos()) {
            return !$user->isAdmin();
        }

        if ($this->isKeamanan()) {
            return $user->id === $this->id || 
                   ($user->isKeamanan() && $user->wilayah_id === $this->wilayah_id);
        }

        return false;
    }
}