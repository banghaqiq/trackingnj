# Eloquent Models Documentation

## Overview

This document provides detailed information about all Eloquent models in the package management system, including their properties, relationships, methods, and usage examples.

## Table of Contents

1. [Wilayah](#wilayah)
2. [Asrama](#asrama)
3. [User](#user)
4. [Paket](#paket)
5. [PaketStatusLog](#paketstatuslog)
6. [AuditLog](#auditlog)
7. [Enums](#enums)

---

## Wilayah

**Location:** `app/Models/Wilayah.php`

Represents a region/area within the campus.

### Properties

| Property   | Type    | Description          |
|------------|---------|----------------------|
| id         | int     | Primary key          |
| nama       | string  | Region name          |
| kode       | string  | Region code          |
| deskripsi  | string  | Description          |
| is_active  | boolean | Active status        |
| created_at | Carbon  | Creation timestamp   |
| updated_at | Carbon  | Update timestamp     |

### Fillable

```php
['nama', 'kode', 'deskripsi', 'is_active']
```

### Casts

```php
['is_active' => 'boolean']
```

### Relationships

**hasMany:**
- `asrama()` - Returns all dormitories in this region
- `users()` - Returns all users assigned to this region
- `paket()` - Returns all packages in this region

### Usage Examples

```php
use App\Models\Wilayah;

// Create new wilayah
$wilayah = Wilayah::create([
    'nama' => 'Wilayah Utara',
    'kode' => 'WLY-UTR',
    'deskripsi' => 'Wilayah bagian utara kampus',
    'is_active' => true,
]);

// Get all asrama in this wilayah
$asrama = $wilayah->asrama;

// Get all users (keamanan) in this wilayah
$users = $wilayah->users;

// Get all packages in this wilayah
$packages = $wilayah->paket;

// Get active wilayah only
$activeWilayah = Wilayah::where('is_active', true)->get();
```

---

## Asrama

**Location:** `app/Models/Asrama.php`

Represents a dormitory belonging to a specific region.

### Properties

| Property   | Type    | Description            |
|------------|---------|------------------------|
| id         | int     | Primary key            |
| wilayah_id | int     | Foreign key to wilayah |
| nama       | string  | Dormitory name         |
| kode       | string  | Dormitory code         |
| alamat     | string  | Address                |
| kapasitas  | int     | Capacity               |
| is_active  | boolean | Active status          |
| created_at | Carbon  | Creation timestamp     |
| updated_at | Carbon  | Update timestamp       |

### Fillable

```php
['wilayah_id', 'nama', 'kode', 'alamat', 'kapasitas', 'is_active']
```

### Casts

```php
['is_active' => 'boolean', 'kapasitas' => 'integer']
```

### Relationships

**belongsTo:**
- `wilayah()` - Returns the parent region

**hasMany:**
- `paket()` - Returns all packages for this dormitory

### Usage Examples

```php
use App\Models\Asrama;
use App\Models\Wilayah;

// Create new asrama
$asrama = Asrama::create([
    'wilayah_id' => 1,
    'nama' => 'Asrama Putra A',
    'kode' => 'APA-A',
    'alamat' => 'Jalan Kampus No. 1',
    'kapasitas' => 100,
    'is_active' => true,
]);

// Get wilayah
$wilayah = $asrama->wilayah;

// Get all packages for this asrama
$packages = $asrama->paket;

// Query with relationships
$asramaWithWilayah = Asrama::with('wilayah')->get();

// Find asrama by wilayah
$asramaUtara = Asrama::where('wilayah_id', 1)->get();
```

---

## User

**Location:** `app/Models/User.php`

Represents system users with role-based access control.

### Properties

| Property          | Type     | Description                |
|-------------------|----------|----------------------------|
| id                | int      | Primary key                |
| name              | string   | Full name                  |
| email             | string   | Email address              |
| username          | string   | Username                   |
| password          | string   | Hashed password            |
| role              | UserRole | User role enum             |
| wilayah_id        | int      | Foreign key to wilayah     |
| is_active         | boolean  | Active status              |
| email_verified_at | Carbon   | Email verification date    |
| remember_token    | string   | Remember token             |
| created_at        | Carbon   | Creation timestamp         |
| updated_at        | Carbon   | Update timestamp           |

### Fillable

```php
['name', 'email', 'username', 'password', 'role', 'wilayah_id', 'is_active', 'email_verified_at']
```

### Hidden

```php
['password', 'remember_token']
```

### Casts

```php
[
    'email_verified_at' => 'datetime',
    'password' => 'hashed',
    'role' => UserRole::class,
    'is_active' => 'boolean',
]
```

### Relationships

**belongsTo:**
- `wilayah()` - Returns assigned region (for keamanan)

**hasMany:**
- `paketDiterima()` - Returns packages received by this user
- `paketDiantar()` - Returns packages delivered by this user
- `statusLogs()` - Returns status changes made by this user
- `auditLogs()` - Returns audit logs for this user

### Methods

**Role Checking:**
```php
public function isAdmin(): bool
public function isPetugasPos(): bool
public function isKeamanan(): bool
```

### Usage Examples

```php
use App\Models\User;
use App\Enums\UserRole;

// Create admin user
$admin = User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'username' => 'admin',
    'password' => 'password', // Will be hashed automatically
    'role' => UserRole::ADMIN,
    'is_active' => true,
    'email_verified_at' => now(),
]);

// Create keamanan user with wilayah
$keamanan = User::create([
    'name' => 'Keamanan Utara',
    'email' => 'keamanan@example.com',
    'username' => 'keamanan_utara',
    'password' => 'password',
    'role' => UserRole::KEAMANAN,
    'wilayah_id' => 1,
    'is_active' => true,
    'email_verified_at' => now(),
]);

// Check role
if ($user->isAdmin()) {
    // Admin logic
}

if ($user->isKeamanan()) {
    $assignedWilayah = $user->wilayah;
}

// Get user's packages
$receivedPackages = $user->paketDiterima;
$deliveredPackages = $user->paketDiantar;

// Get user's audit trail
$auditLogs = $user->auditLogs()->latest()->limit(10)->get();
```

---

## Paket

**Location:** `app/Models/Paket.php`

Represents a package with soft delete support.

### Properties

| Property         | Type        | Description                  |
|------------------|-------------|------------------------------|
| id               | int         | Primary key                  |
| kode_resi        | string      | Tracking number (unique)     |
| nama_penerima    | string      | Recipient name               |
| telepon_penerima | string      | Recipient phone              |
| wilayah_id       | int         | Foreign key to wilayah       |
| asrama_id        | int         | Foreign key to asrama        |
| nomor_kamar      | string      | Room number                  |
| alamat_lengkap   | string      | Full address                 |
| tanpa_wilayah    | boolean     | Without region flag          |
| keluarga         | boolean     | Family package flag          |
| nama_pengirim    | string      | Sender name                  |
| keterangan       | string      | Notes                        |
| status           | PaketStatus | Current status               |
| tanggal_diterima | Carbon      | Received date                |
| tanggal_diambil  | Carbon      | Picked up date               |
| diterima_oleh    | int         | User who received            |
| diantar_oleh     | int         | User who delivered           |
| deleted_at       | Carbon      | Soft delete timestamp        |
| created_at       | Carbon      | Creation timestamp           |
| updated_at       | Carbon      | Update timestamp             |

### Fillable

```php
[
    'kode_resi', 'nama_penerima', 'telepon_penerima', 'wilayah_id', 
    'asrama_id', 'nomor_kamar', 'alamat_lengkap', 'tanpa_wilayah', 
    'keluarga', 'nama_pengirim', 'keterangan', 'status', 
    'tanggal_diterima', 'tanggal_diambil', 'diterima_oleh', 'diantar_oleh'
]
```

### Casts

```php
[
    'tanpa_wilayah' => 'boolean',
    'keluarga' => 'boolean',
    'status' => PaketStatus::class,
    'tanggal_diterima' => 'datetime',
    'tanggal_diambil' => 'datetime',
]
```

### Relationships

**belongsTo:**
- `wilayah()` - Returns assigned region
- `asrama()` - Returns assigned dormitory
- `diterimaOleh()` - Returns user who received the package
- `diantarOleh()` - Returns user who delivered the package

**hasMany:**
- `statusLogs()` - Returns all status change logs

### Methods

**Status Management:**
```php
public function updateStatus(
    PaketStatus $newStatus, 
    ?int $userId = null, 
    ?string $catatan = null
): void
```

**Status Checking:**
```php
public function isDiterima(): bool
public function isDiproses(): bool
public function isDiantar(): bool
public function isSelesai(): bool
public function isDikembalikan(): bool
```

### Usage Examples

```php
use App\Models\Paket;
use App\Enums\PaketStatus;

// Create new package
$paket = Paket::create([
    'kode_resi' => 'PKT-2024-001',
    'nama_penerima' => 'John Doe',
    'telepon_penerima' => '081234567890',
    'wilayah_id' => 1,
    'asrama_id' => 1,
    'nomor_kamar' => '101',
    'status' => PaketStatus::DITERIMA,
    'tanggal_diterima' => now(),
    'diterima_oleh' => 1,
]);

// Update status with logging
$paket->updateStatus(
    PaketStatus::DIPROSES, 
    $user->id, 
    'Package is being sorted'
);

// Check status
if ($paket->isDiterima()) {
    // Handle received package
}

// Get relationships
$wilayah = $paket->wilayah;
$asrama = $paket->asrama;
$receiver = $paket->diterimaOleh;

// Query packages
$activePackages = Paket::whereIn('status', [
    PaketStatus::DITERIMA,
    PaketStatus::DIPROSES,
])->get();

// Get family packages
$familyPackages = Paket::where('keluarga', true)->get();

// Get packages without region
$noRegionPackages = Paket::where('tanpa_wilayah', true)->get();

// Soft delete
$paket->delete(); // Soft delete

// Include soft deleted
$allPackages = Paket::withTrashed()->get();

// Restore soft deleted
$paket->restore();

// Permanent delete
$paket->forceDelete();
```

---

## PaketStatusLog

**Location:** `app/Models/PaketStatusLog.php`

Tracks status transitions for packages.

### Properties

| Property     | Type        | Description              |
|--------------|-------------|--------------------------|
| id           | int         | Primary key              |
| paket_id     | int         | Foreign key to paket     |
| status_dari  | PaketStatus | Previous status          |
| status_ke    | PaketStatus | New status               |
| diubah_oleh  | int         | User who changed status  |
| catatan      | string      | Notes                    |
| created_at   | Carbon      | Change timestamp         |

### Fillable

```php
['paket_id', 'status_dari', 'status_ke', 'diubah_oleh', 'catatan']
```

### Casts

```php
[
    'status_dari' => PaketStatus::class,
    'status_ke' => PaketStatus::class,
    'created_at' => 'datetime',
]
```

### Relationships

**belongsTo:**
- `paket()` - Returns the package
- `diubahOleh()` - Returns the user who made the change

### Usage Examples

```php
use App\Models\PaketStatusLog;
use App\Enums\PaketStatus;

// Create status log manually
$log = PaketStatusLog::create([
    'paket_id' => 1,
    'status_dari' => PaketStatus::DITERIMA,
    'status_ke' => PaketStatus::DIPROSES,
    'diubah_oleh' => $user->id,
    'catatan' => 'Package sorted and ready for delivery',
]);

// Get status history for a package
$history = PaketStatusLog::where('paket_id', $paket->id)
    ->orderBy('created_at', 'desc')
    ->get();

// Get recent status changes
$recentChanges = PaketStatusLog::with(['paket', 'diubahOleh'])
    ->latest('created_at')
    ->limit(10)
    ->get();
```

---

## AuditLog

**Location:** `app/Models/AuditLog.php`

Tracks user actions for audit purposes.

### Properties

| Property   | Type     | Description              |
|------------|----------|--------------------------|
| id         | int      | Primary key              |
| user_id    | int      | Foreign key to users     |
| user_name  | string   | User name (cached)       |
| role       | UserRole | User role                |
| action     | string   | Action performed         |
| model_type | string   | Model class name         |
| model_id   | int      | Model ID                 |
| old_values | array    | Previous values          |
| new_values | array    | New values               |
| ip_address | string   | IP address               |
| user_agent | string   | User agent string        |
| created_at | Carbon   | Action timestamp         |

### Fillable

```php
[
    'user_id', 'user_name', 'role', 'action', 'model_type', 
    'model_id', 'old_values', 'new_values', 'ip_address', 'user_agent'
]
```

### Casts

```php
[
    'role' => UserRole::class,
    'old_values' => 'array',
    'new_values' => 'array',
    'created_at' => 'datetime',
]
```

### Relationships

**belongsTo:**
- `user()` - Returns the user who performed the action

### Methods

**Static Helper:**
```php
public static function log(
    string $action,
    ?User $user = null,
    ?string $modelType = null,
    ?int $modelId = null,
    ?array $oldValues = null,
    ?array $newValues = null
): self
```

### Usage Examples

```php
use App\Models\AuditLog;

// Log an action using static helper
AuditLog::log(
    action: 'update',
    user: $user,
    modelType: 'Paket',
    modelId: $paket->id,
    oldValues: ['status' => 'diterima'],
    newValues: ['status' => 'diproses']
);

// Log without model reference
AuditLog::log(
    action: 'login',
    user: $user
);

// Query audit logs
$userLogs = AuditLog::where('user_id', $user->id)
    ->orderBy('created_at', 'desc')
    ->get();

// Get logs for specific model
$paketLogs = AuditLog::where('model_type', 'Paket')
    ->where('model_id', $paket->id)
    ->orderBy('created_at', 'desc')
    ->get();

// Get recent activity
$recentActivity = AuditLog::with('user')
    ->latest('created_at')
    ->limit(50)
    ->get();
```

---

## Enums

### UserRole

**Location:** `app/Enums/UserRole.php`

Represents user roles in the system.

**Cases:**
```php
case ADMIN = 'admin';
case PETUGAS_POS = 'petugas_pos';
case KEAMANAN = 'keamanan';
```

**Methods:**
```php
public function label(): string
public static function values(): array
```

**Usage:**
```php
use App\Enums\UserRole;

// Use in queries
$admins = User::where('role', UserRole::ADMIN)->get();

// Get all values
$roles = UserRole::values(); // ['admin', 'petugas_pos', 'keamanan']

// Get label
$label = UserRole::ADMIN->label(); // 'Administrator'

// Match on enum
$message = match($user->role) {
    UserRole::ADMIN => 'Full access',
    UserRole::PETUGAS_POS => 'Can manage packages',
    UserRole::KEAMANAN => 'Can view assigned region',
};
```

### PaketStatus

**Location:** `app/Enums/PaketStatus.php`

Represents package status.

**Cases:**
```php
case DITERIMA = 'diterima';
case DIPROSES = 'diproses';
case DIANTAR = 'diantar';
case SELESAI = 'selesai';
case DIKEMBALIKAN = 'dikembalikan';
```

**Methods:**
```php
public function label(): string
public static function values(): array
```

**Usage:**
```php
use App\Enums\PaketStatus;

// Use in queries
$received = Paket::where('status', PaketStatus::DITERIMA)->get();

// Get all values
$statuses = PaketStatus::values();

// Get label
$label = PaketStatus::DITERIMA->label(); // 'Diterima'

// Use in conditionals
if ($paket->status === PaketStatus::SELESAI) {
    // Package is completed
}
```

---

## Best Practices

### 1. Always Use Relationships

```php
// Good
$paket = Paket::with(['wilayah', 'asrama', 'diterimaOleh'])->find($id);

// Avoid N+1
$pakets = Paket::with('wilayah')->get();
```

### 2. Use Enums for Type Safety

```php
// Good
$user->role = UserRole::ADMIN;

// Avoid
$user->role = 'admin';
```

### 3. Log Status Changes

```php
// Always use the updateStatus method
$paket->updateStatus(PaketStatus::DIPROSES, $user->id, 'Processing package');

// Avoid direct updates
$paket->update(['status' => 'diproses']); // No logging
```

### 4. Use Audit Logging

```php
// Log important actions
AuditLog::log('delete', $user, 'Paket', $paket->id);
```

### 5. Scope Queries Appropriately

```php
// For keamanan users, filter by wilayah
if ($user->isKeamanan()) {
    $pakets = Paket::where('wilayah_id', $user->wilayah_id)->get();
}
```

### 6. Use Soft Deletes

```php
// Soft delete allows recovery
$paket->delete();

// Query without soft deleted
$active = Paket::all();

// Include soft deleted
$all = Paket::withTrashed()->get();
```

---

## Testing with Factories

All models include factories for easy testing:

```php
use App\Models\{Wilayah, Asrama, User, Paket};

// Create test data
$wilayah = Wilayah::factory()->create();
$asrama = Asrama::factory()->forWilayah($wilayah->id)->create();
$user = User::factory()->keamanan()->forWilayah($wilayah->id)->create();
$paket = Paket::factory()->forAsrama($asrama->id)->diterima()->create();

// Create multiple
$pakets = Paket::factory()->count(10)->create();
```

See `MIGRATIONS_AND_SEEDERS.md` for more factory examples.
