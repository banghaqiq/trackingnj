# Quick Reference Guide

## Common Commands

### Database Operations

```bash
# Fresh start (development)
php artisan migrate:fresh --seed

# Run migrations only
php artisan migrate

# Run seeders only
php artisan db:seed

# Rollback last migration
php artisan migrate:rollback

# Check migration status
php artisan migrate:status
```

### Interactive Shell

```bash
# Open Laravel Tinker
php artisan tinker
```

## Common Queries

### Package Operations

```php
use App\Models\Paket;
use App\Enums\PaketStatus;

// Create package
$paket = Paket::create([
    'kode_resi' => 'PKT-' . now()->format('YmdHis'),
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
$paket->updateStatus(PaketStatus::DIPROSES, $userId, 'Processing');

// Find by tracking number
$paket = Paket::where('kode_resi', 'PKT-001')->first();

// Get packages by status
$received = Paket::where('status', PaketStatus::DITERIMA)->get();

// Get packages by region
$packages = Paket::where('wilayah_id', 1)->get();

// Get packages with relationships
$pakets = Paket::with(['wilayah', 'asrama', 'diterimaOleh'])->get();

// Get family packages
$familyPackages = Paket::where('keluarga', true)->get();

// Get packages without region
$noRegion = Paket::where('tanpa_wilayah', true)->get();

// Soft delete
$paket->delete();

// Include soft deleted
$all = Paket::withTrashed()->get();

// Restore
$paket->restore();
```

### User Operations

```php
use App\Models\User;
use App\Enums\UserRole;

// Create admin
$admin = User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'username' => 'admin',
    'password' => 'password',
    'role' => UserRole::ADMIN,
    'is_active' => true,
    'email_verified_at' => now(),
]);

// Create keamanan with wilayah
$keamanan = User::create([
    'name' => 'Keamanan Utara',
    'email' => 'keamanan@example.com',
    'username' => 'keamanan_utara',
    'password' => 'password',
    'role' => UserRole::KEAMANAN,
    'wilayah_id' => 1,
    'is_active' => true,
]);

// Check role
if ($user->isAdmin()) {
    // Admin logic
}

// Get user by role
$admins = User::where('role', UserRole::ADMIN)->get();
$keamananUsers = User::where('role', UserRole::KEAMANAN)->get();

// Get user with wilayah
$user = User::with('wilayah')->find($id);
```

### Wilayah & Asrama Operations

```php
use App\Models\{Wilayah, Asrama};

// Get wilayah with asrama
$wilayah = Wilayah::with('asrama')->find(1);

// Get all asrama in wilayah
$asrama = $wilayah->asrama;

// Get active wilayah
$active = Wilayah::where('is_active', true)->get();

// Create asrama
$asrama = Asrama::create([
    'wilayah_id' => 1,
    'nama' => 'Asrama Putra A',
    'kode' => 'APA-A',
    'alamat' => 'Jalan Kampus No. 1',
    'kapasitas' => 100,
]);
```

### Status Log Operations

```php
use App\Models\PaketStatusLog;

// Get status history for package
$history = PaketStatusLog::where('paket_id', $paket->id)
    ->with('diubahOleh')
    ->orderBy('created_at', 'desc')
    ->get();

// Get recent status changes
$recent = PaketStatusLog::with(['paket', 'diubahOleh'])
    ->latest()
    ->limit(10)
    ->get();
```

### Audit Log Operations

```php
use App\Models\AuditLog;

// Create audit log
AuditLog::log(
    action: 'update',
    user: $user,
    modelType: 'Paket',
    modelId: $paket->id,
    oldValues: ['status' => 'diterima'],
    newValues: ['status' => 'diproses']
);

// Get user's audit logs
$logs = AuditLog::where('user_id', $user->id)
    ->orderBy('created_at', 'desc')
    ->get();

// Get logs for specific action
$deletions = AuditLog::where('action', 'delete')
    ->with('user')
    ->latest()
    ->get();
```

## Factory Usage

### Creating Test Data

```php
use App\Models\{Wilayah, Asrama, User, Paket};

// Single records
Wilayah::factory()->create();
Asrama::factory()->create();
User::factory()->admin()->create();
Paket::factory()->diterima()->create();

// Multiple records
Wilayah::factory()->count(5)->create();
Paket::factory()->count(50)->create();

// With relationships
$wilayah = Wilayah::factory()->create();
Asrama::factory()->forWilayah($wilayah->id)->count(2)->create();

// Different states
User::factory()->admin()->create();
User::factory()->petugasPos()->create();
User::factory()->keamanan()->create();

Paket::factory()->diterima()->create();
Paket::factory()->diproses()->create();
Paket::factory()->selesai()->create();
Paket::factory()->keluarga()->create();
Paket::factory()->tanpaWilayah()->create();
```

## Common Relationships

```php
// Package relationships
$paket->wilayah;           // BelongsTo Wilayah
$paket->asrama;            // BelongsTo Asrama
$paket->diterimaOleh;      // BelongsTo User
$paket->diantarOleh;       // BelongsTo User
$paket->statusLogs;        // HasMany PaketStatusLog

// User relationships
$user->wilayah;            // BelongsTo Wilayah
$user->paketDiterima;      // HasMany Paket
$user->paketDiantar;       // HasMany Paket
$user->statusLogs;         // HasMany PaketStatusLog
$user->auditLogs;          // HasMany AuditLog

// Wilayah relationships
$wilayah->asrama;          // HasMany Asrama
$wilayah->users;           // HasMany User
$wilayah->paket;           // HasMany Paket

// Asrama relationships
$asrama->wilayah;          // BelongsTo Wilayah
$asrama->paket;            // HasMany Paket
```

## Enums

### UserRole

```php
use App\Enums\UserRole;

UserRole::ADMIN            // 'admin'
UserRole::PETUGAS_POS      // 'petugas_pos'
UserRole::KEAMANAN         // 'keamanan'

// Get all values
UserRole::values();        // ['admin', 'petugas_pos', 'keamanan']

// Get label
UserRole::ADMIN->label();  // 'Administrator'
```

### PaketStatus

```php
use App\Enums\PaketStatus;

PaketStatus::DITERIMA      // 'diterima'
PaketStatus::DIPROSES      // 'diproses'
PaketStatus::DIANTAR       // 'diantar'
PaketStatus::SELESAI       // 'selesai'
PaketStatus::DIKEMBALIKAN  // 'dikembalikan'

// Get all values
PaketStatus::values();     // ['diterima', 'diproses', ...]

// Get label
PaketStatus::DITERIMA->label();  // 'Diterima'
```

## Filtering by User Role

```php
// Admin - full access
if ($user->isAdmin()) {
    $pakets = Paket::all();
}

// Petugas Pos - all packages
if ($user->isPetugasPos()) {
    $pakets = Paket::all();
}

// Keamanan - only their region
if ($user->isKeamanan()) {
    $pakets = Paket::where('wilayah_id', $user->wilayah_id)->get();
}
```

## Statistics & Reporting

```php
// Count by status
$stats = [
    'diterima' => Paket::where('status', PaketStatus::DITERIMA)->count(),
    'diproses' => Paket::where('status', PaketStatus::DIPROSES)->count(),
    'diantar' => Paket::where('status', PaketStatus::DIANTAR)->count(),
    'selesai' => Paket::where('status', PaketStatus::SELESAI)->count(),
];

// Count by region
$regionStats = Wilayah::withCount('paket')->get();

// Packages received today
$today = Paket::whereDate('tanggal_diterima', today())->count();

// Packages completed this month
$thisMonth = Paket::where('status', PaketStatus::SELESAI)
    ->whereMonth('tanggal_diambil', now()->month)
    ->count();

// User activity
$userActivity = User::withCount([
    'paketDiterima',
    'paketDiantar',
    'statusLogs',
])->get();
```

## Search Operations

```php
// Search by tracking number
$paket = Paket::where('kode_resi', 'like', "%$search%")->get();

// Search by recipient name
$pakets = Paket::where('nama_penerima', 'like', "%$search%")->get();

// Search by room number
$pakets = Paket::where('nomor_kamar', $roomNumber)->get();

// Combined search
$pakets = Paket::where(function($query) use ($search) {
    $query->where('kode_resi', 'like', "%$search%")
          ->orWhere('nama_penerima', 'like', "%$search%")
          ->orWhere('nomor_kamar', 'like', "%$search%");
})->get();
```

## Date Filtering

```php
// Packages received today
$today = Paket::whereDate('tanggal_diterima', today())->get();

// Packages in date range
$pakets = Paket::whereBetween('tanggal_diterima', [$startDate, $endDate])->get();

// Packages not picked up (pending)
$pending = Paket::whereNull('tanggal_diambil')
    ->whereNotIn('status', [PaketStatus::SELESAI, PaketStatus::DIKEMBALIKAN])
    ->get();

// Old packages (> 30 days)
$old = Paket::where('tanggal_diterima', '<', now()->subDays(30))
    ->where('status', '!=', PaketStatus::SELESAI)
    ->get();
```

## Validation Rules

Common validation rules for package creation:

```php
$rules = [
    'kode_resi' => 'required|string|max:255|unique:paket,kode_resi',
    'nama_penerima' => 'required|string|max:255',
    'telepon_penerima' => 'nullable|string|max:255',
    'wilayah_id' => 'nullable|exists:wilayah,id',
    'asrama_id' => 'nullable|exists:asrama,id',
    'nomor_kamar' => 'nullable|string|max:255',
    'tanpa_wilayah' => 'boolean',
    'keluarga' => 'boolean',
    'status' => 'required|in:' . implode(',', PaketStatus::values()),
];
```

## Performance Tips

```php
// Use eager loading to avoid N+1
$pakets = Paket::with(['wilayah', 'asrama', 'diterimaOleh'])->get();

// Use select to load only needed columns
$pakets = Paket::select('id', 'kode_resi', 'nama_penerima', 'status')->get();

// Use chunk for large datasets
Paket::chunk(100, function($pakets) {
    foreach ($pakets as $paket) {
        // Process each paket
    }
});

// Use indexes for frequent queries
// Indexes are already created in migrations for:
// - status, wilayah_id
// - tanpa_wilayah, keluarga
// - tanggal_diterima
```

## Helpful Tinker Commands

```bash
php artisan tinker
```

```php
// Count records
Wilayah::count();
Asrama::count();
User::count();
Paket::count();

// Latest records
Paket::latest()->first();
AuditLog::latest()->first();

// Clear test data
Paket::truncate();
PaketStatusLog::truncate();
AuditLog::truncate();

// Refresh seeders
Artisan::call('migrate:fresh --seed');
```
