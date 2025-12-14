# Package Management System - Domain Schema

A comprehensive Laravel-based package management system for tracking packages across campus regions (wilayah) and dormitories (asrama) with role-based access control.

## Features

- 📦 **Package Tracking**: Complete package lifecycle management with unique tracking numbers
- 🏢 **Region Management**: Organize packages by campus regions and dormitories
- 👥 **Role-Based Access**: Three user roles (Admin, Petugas Pos, Keamanan)
- 📊 **Status Tracking**: Comprehensive status logs for package transitions
- 🔍 **Audit Trail**: Complete audit logging for all user actions
- ♻️ **Soft Deletes**: Safe deletion with recovery options
- 🏭 **Factory Support**: Built-in factories for easy testing

## Domain Entities

### 1. Wilayah (Regions)
Static list of campus regions where packages are delivered.

**Key Features:**
- Unique region codes
- Active/inactive status
- Manages multiple dormitories

### 2. Asrama (Dormitories)
Dormitories belonging to specific regions.

**Key Features:**
- Linked to parent region
- Capacity tracking
- Unique dormitory codes

### 3. Users
System users with role-based permissions.

**Roles:**
- **Admin**: Full system access
- **Petugas Pos**: Post office staff who receive and process packages
- **Keamanan**: Security staff assigned to specific regions

### 4. Paket (Packages)
Package records with comprehensive tracking.

**Key Features:**
- Unique tracking number (`kode_resi`)
- Status tracking (diterima → diproses → diantar → selesai)
- Soft delete support
- Special flags:
  - `tanpa_wilayah`: Packages without region assignment
  - `keluarga`: Family packages

### 5. Paket Status Logs
Complete history of all package status changes.

**Tracks:**
- Previous and new status
- Who made the change
- When the change occurred
- Optional notes

### 6. Audit Logs
System-wide audit trail for compliance and security.

**Logs:**
- User actions
- Model changes (before/after values)
- IP address and user agent
- Timestamp

## Database Schema

### Tables Overview

| Table              | Purpose                          | Key Features              |
|--------------------|----------------------------------|---------------------------|
| wilayah            | Campus regions                   | Static list, 5 regions    |
| asrama             | Dormitories                      | 10 dorms, 2 per region    |
| users              | System users                     | 3 roles, wilayah mapping  |
| paket              | Package records                  | Soft deletes, unique resi |
| paket_status_logs  | Status change history            | Immutable audit trail     |
| audit_logs         | User action logs                 | Full audit trail          |

### Status Flow

```
DITERIMA → DIPROSES → DIANTAR → SELESAI
              ↓
         DIKEMBALIKAN
```

## Installation

### Prerequisites

- PHP 8.1 or higher
- Composer
- Laravel 10.x or higher
- Database (MySQL/PostgreSQL/SQLite)

### Setup

1. **Configure Database**

   Edit `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=package_management
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

2. **Run Migrations**

   ```bash
   php artisan migrate
   ```

3. **Seed Database**

   ```bash
   php artisan db:seed
   ```

   Or combine both steps:
   ```bash
   php artisan migrate:fresh --seed
   ```

### Default Credentials

After seeding, you can login with:

| Username        | Password | Role         | Access       |
|-----------------|----------|--------------|--------------|
| admin           | password | Admin        | Full access  |
| petugas_pos     | password | Petugas Pos  | All packages |
| keamanan_utara  | password | Keamanan     | Utara region |
| keamanan_selatan| password | Keamanan     | Selatan region |
| keamanan_timur  | password | Keamanan     | Timur region |
| keamanan_barat  | password | Keamanan     | Barat region |
| keamanan_tengah | password | Keamanan     | Tengah region |

**⚠️ Important:** Change these passwords in production!

## Quick Start

### Creating a Package

```php
use App\Models\Paket;
use App\Enums\PaketStatus;

$paket = Paket::create([
    'kode_resi' => 'PKT-2024-001',
    'nama_penerima' => 'John Doe',
    'telepon_penerima' => '081234567890',
    'wilayah_id' => 1,
    'asrama_id' => 1,
    'nomor_kamar' => '101',
    'status' => PaketStatus::DITERIMA,
    'tanggal_diterima' => now(),
    'diterima_oleh' => $user->id,
]);
```

### Updating Package Status

```php
// This automatically creates a status log entry
$paket->updateStatus(
    PaketStatus::DIPROSES,
    $user->id,
    'Package sorted and ready for delivery'
);
```

### Creating Audit Log

```php
use App\Models\AuditLog;

AuditLog::log(
    action: 'update',
    user: $user,
    modelType: 'Paket',
    modelId: $paket->id,
    oldValues: ['status' => 'diterima'],
    newValues: ['status' => 'diproses']
);
```

### Querying with Relationships

```php
// Get packages with related data (avoid N+1)
$pakets = Paket::with(['wilayah', 'asrama', 'diterimaOleh'])
    ->where('status', PaketStatus::DITERIMA)
    ->get();

// Get packages for specific region
$regionPackages = Paket::where('wilayah_id', $wilayahId)->get();

// Get status history
$history = $paket->statusLogs()
    ->with('diubahOleh')
    ->orderBy('created_at', 'desc')
    ->get();
```

## Testing

### Using Factories

The system includes comprehensive factories for all models:

```php
use App\Models\{Wilayah, Asrama, User, Paket};

// Create test wilayah
$wilayah = Wilayah::factory()->create();

// Create test asrama
$asrama = Asrama::factory()->forWilayah($wilayah->id)->create();

// Create test users
$admin = User::factory()->admin()->create();
$keamanan = User::factory()->keamanan()->forWilayah($wilayah->id)->create();

// Create test packages
$paket = Paket::factory()->diterima()->create();
$familyPaket = Paket::factory()->keluarga()->create();
$noRegionPaket = Paket::factory()->tanpaWilayah()->create();

// Create multiple packages
$pakets = Paket::factory()->count(50)->create();
```

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=PackageTest
```

## Documentation

Comprehensive documentation is available in the `docs/` directory:

- **[DATABASE_SCHEMA.md](docs/DATABASE_SCHEMA.md)** - Complete database schema documentation with ERD
- **[MIGRATIONS_AND_SEEDERS.md](docs/MIGRATIONS_AND_SEEDERS.md)** - Guide for running migrations and seeders
- **[MODELS.md](docs/MODELS.md)** - Detailed Eloquent models documentation with examples

## Project Structure

```
.
├── app/
│   ├── Enums/
│   │   ├── UserRole.php          # User role enum
│   │   └── PaketStatus.php       # Package status enum
│   └── Models/
│       ├── Wilayah.php           # Region model
│       ├── Asrama.php            # Dormitory model
│       ├── User.php              # User model
│       ├── Paket.php             # Package model
│       ├── PaketStatusLog.php    # Status log model
│       └── AuditLog.php          # Audit log model
├── database/
│   ├── factories/
│   │   ├── WilayahFactory.php
│   │   ├── AsramaFactory.php
│   │   ├── UserFactory.php
│   │   ├── PaketFactory.php
│   │   ├── PaketStatusLogFactory.php
│   │   └── AuditLogFactory.php
│   ├── migrations/
│   │   ├── 2024_01_01_000001_create_wilayah_table.php
│   │   ├── 2024_01_01_000002_create_asrama_table.php
│   │   ├── 2024_01_01_000003_create_users_table.php
│   │   ├── 2024_01_01_000004_create_paket_table.php
│   │   ├── 2024_01_01_000005_create_paket_status_logs_table.php
│   │   └── 2024_01_01_000006_create_audit_logs_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── WilayahSeeder.php
│       ├── AsramaSeeder.php
│       └── UserSeeder.php
└── docs/
    ├── DATABASE_SCHEMA.md
    ├── MIGRATIONS_AND_SEEDERS.md
    └── MODELS.md
```

## API Usage Examples

### Package Lifecycle

```php
// 1. Package received at post office
$paket = Paket::create([
    'kode_resi' => 'PKT-' . now()->format('YmdHis'),
    'nama_penerima' => 'John Doe',
    'wilayah_id' => 1,
    'asrama_id' => 1,
    'status' => PaketStatus::DITERIMA,
    'diterima_oleh' => $petugasPos->id,
]);

// 2. Package being processed
$paket->updateStatus(PaketStatus::DIPROSES, $petugasPos->id);

// 3. Package out for delivery
$paket->updateStatus(
    PaketStatus::DIANTAR,
    $keamanan->id,
    'Delivered by Keamanan Utara'
);
$paket->update(['diantar_oleh' => $keamanan->id]);

// 4. Package delivered
$paket->updateStatus(PaketStatus::SELESAI, $keamanan->id);
$paket->update(['tanggal_diambil' => now()]);
```

### Security Implementation

```php
// Check user permissions
if ($user->isKeamanan()) {
    // Keamanan can only see packages in their region
    $pakets = Paket::where('wilayah_id', $user->wilayah_id)->get();
}

if ($user->isPetugasPos() || $user->isAdmin()) {
    // Petugas pos and admin can see all packages
    $pakets = Paket::all();
}
```

### Reporting

```php
// Get package statistics by region
$stats = Wilayah::withCount([
    'paket',
    'paket as paket_diterima' => fn($q) => $q->where('status', PaketStatus::DITERIMA),
    'paket as paket_selesai' => fn($q) => $q->where('status', PaketStatus::SELESAI),
])->get();

// Get recent activity
$recentLogs = AuditLog::with('user')
    ->latest()
    ->limit(50)
    ->get();

// Get user performance
$userStats = User::withCount([
    'paketDiterima',
    'paketDiantar',
    'statusLogs',
])->where('is_active', true)->get();
```

## Features Implementation

### Unique Tracking Numbers

The `kode_resi` field has a unique index enforced at the database level:

```php
$table->string('kode_resi')->unique();
```

### Checkbox Flags

Two boolean flags for special package types:

- `tanpa_wilayah`: For packages without region assignment
- `keluarga`: For family packages (not for students)

```php
$familyPackage = Paket::create([
    'kode_resi' => 'FAM-001',
    'keluarga' => true,
    // ...
]);

$noRegionPackage = Paket::create([
    'kode_resi' => 'NOR-001',
    'tanpa_wilayah' => true,
    'wilayah_id' => null,
    'asrama_id' => null,
    // ...
]);
```

### Automatic Status Logging

Use the `updateStatus` method to automatically create status logs:

```php
$paket->updateStatus(
    PaketStatus::DIPROSES,
    auth()->id(),
    'Package sorted'
);
```

### Soft Deletes

Packages use soft deletes for data safety:

```php
// Soft delete
$paket->delete();

// Query without soft deleted
$active = Paket::all();

// Include soft deleted
$all = Paket::withTrashed()->get();

// Restore
$paket->restore();

// Permanent delete
$paket->forceDelete();
```

## Best Practices

1. **Always use relationships** to avoid N+1 queries
2. **Use enums** for type safety (UserRole, PaketStatus)
3. **Log status changes** using the `updateStatus` method
4. **Create audit logs** for important actions
5. **Scope queries** based on user role
6. **Use soft deletes** for packages
7. **Validate unique** `kode_resi` before creation
8. **Use factories** for testing

## Production Deployment

For production:

1. **Change default passwords**
2. **Configure proper database** (not SQLite)
3. **Set up backups**
4. **Enable logging**
5. **Configure queue** for async operations
6. **Set up monitoring**

```bash
# Run migrations only (no seeding)
php artisan migrate --force

# Seed necessary data
php artisan db:seed --class=WilayahSeeder --force
php artisan db:seed --class=AsramaSeeder --force

# Create admin manually with strong password
php artisan tinker
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For issues, questions, or contributions, please refer to the documentation in the `docs/` directory or contact the development team.
