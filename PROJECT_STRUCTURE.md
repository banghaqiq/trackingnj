# Project Structure

```
package-management-system/
│
├── app/
│   ├── Enums/
│   │   ├── UserRole.php              # User role enum (admin, petugas_pos, keamanan)
│   │   └── PaketStatus.php           # Package status enum (5 states)
│   │
│   └── Models/
│       ├── Wilayah.php               # Region model
│       ├── Asrama.php                # Dormitory model
│       ├── User.php                  # User model with authentication
│       ├── Paket.php                 # Package model with soft deletes
│       ├── PaketStatusLog.php        # Status transition log model
│       └── AuditLog.php              # Audit trail model
│
├── database/
│   ├── factories/
│   │   ├── WilayahFactory.php        # Region factory for testing
│   │   ├── AsramaFactory.php         # Dormitory factory for testing
│   │   ├── UserFactory.php           # User factory with role states
│   │   ├── PaketFactory.php          # Package factory with status states
│   │   ├── PaketStatusLogFactory.php # Status log factory
│   │   └── AuditLogFactory.php       # Audit log factory
│   │
│   ├── migrations/
│   │   ├── 2024_01_01_000001_create_wilayah_table.php
│   │   ├── 2024_01_01_000002_create_asrama_table.php
│   │   ├── 2024_01_01_000003_create_users_table.php
│   │   ├── 2024_01_01_000004_create_paket_table.php
│   │   ├── 2024_01_01_000005_create_paket_status_logs_table.php
│   │   └── 2024_01_01_000006_create_audit_logs_table.php
│   │
│   └── seeders/
│       ├── DatabaseSeeder.php        # Main seeder orchestrator
│       ├── WilayahSeeder.php         # Seeds 5 regions
│       ├── AsramaSeeder.php          # Seeds 10 dormitories (2 per region)
│       └── UserSeeder.php            # Seeds 7 users (1 admin, 1 pos, 5 keamanan)
│
├── docs/
│   ├── DATABASE_SCHEMA.md            # Complete schema with ERD
│   ├── MIGRATIONS_AND_SEEDERS.md     # Migration/seeder guide
│   ├── MODELS.md                     # Model documentation
│   └── QUICK_REFERENCE.md            # Quick reference guide
│
├── .gitignore                        # Git ignore file
├── README.md                         # Project overview & quick start
├── IMPLEMENTATION_SUMMARY.md         # Implementation summary
└── PROJECT_STRUCTURE.md              # This file
```

## File Count Summary

| Category      | Count | Description                              |
|---------------|-------|------------------------------------------|
| Enums         | 2     | Type-safe enumerations                   |
| Models        | 6     | Eloquent models with relationships       |
| Migrations    | 6     | Database schema migrations               |
| Seeders       | 4     | Data seeders (1 main + 3 entity seeders) |
| Factories     | 6     | Model factories for testing              |
| Documentation | 5     | Markdown documentation files             |
| Configuration | 3     | README, Summary, Structure, .gitignore   |
| **Total**     | **32**| **All project files**                    |

## Directory Purposes

### `/app/Enums/`
Contains PHP 8.1 enumerations for type-safe value objects:
- User roles (3 roles)
- Package statuses (5 states)

### `/app/Models/`
Eloquent models representing database entities:
- Domain models (Wilayah, Asrama, Paket)
- User model with authentication
- Log models (PaketStatusLog, AuditLog)

### `/database/migrations/`
Database schema definitions in chronological order:
1. Base tables (wilayah, users)
2. Dependent tables (asrama, paket)
3. Log tables (paket_status_logs, audit_logs)

### `/database/seeders/`
Data seeders for initial database population:
- Wilayah: 5 campus regions
- Asrama: 10 dormitories
- Users: 7 default users with different roles

### `/database/factories/`
Model factories for testing and development:
- All models have factory support
- Multiple states per factory
- Relationship helpers

### `/docs/`
Comprehensive project documentation:
- Database schema documentation
- Migration and seeder guides
- Model API documentation
- Quick reference for common tasks

## Key Files

### Most Important Models

1. **Paket.php** - Core package model
   - Soft deletes
   - Status management
   - Multiple relationships

2. **User.php** - User authentication
   - Role-based access
   - Wilayah assignment
   - Helper methods

3. **AuditLog.php** - Audit trail
   - Static logging helper
   - JSON value storage

### Critical Migrations

1. **create_paket_table.php** - Main package table
   - Unique kode_resi
   - Multiple foreign keys
   - Boolean flags
   - Indexes for performance

2. **create_users_table.php** - User management
   - Role enum
   - Wilayah assignment
   - Authentication fields

### Essential Documentation

1. **README.md** - Start here
2. **MODELS.md** - Model usage
3. **QUICK_REFERENCE.md** - Common commands

## Relationships Overview

```
Wilayah (1) ──────> (*) Asrama
   │
   ├──────> (*) Users (keamanan only)
   │
   └──────> (*) Paket
                 │
                 ├──────> (*) PaketStatusLog
                 │
                 └── (belongs to) ──> Users (diterima_oleh, diantar_oleh)

Users (1) ──────> (*) AuditLog
      (1) ──────> (*) PaketStatusLog (diubah_oleh)
```

## Data Flow

```
1. Package Received
   ↓
   Paket created (status: DITERIMA)
   ↓
   PaketStatusLog entry (status: null → DITERIMA)
   ↓
   AuditLog entry (action: create)

2. Status Update
   ↓
   Paket::updateStatus() called
   ↓
   Paket updated (status: DITERIMA → DIPROSES)
   ↓
   PaketStatusLog entry (status: DITERIMA → DIPROSES)
   ↓
   AuditLog entry (action: update)

3. Package Delivered
   ↓
   Paket updated (status: SELESAI, tanggal_diambil)
   ↓
   PaketStatusLog entry (status: DIANTAR → SELESAI)
   ↓
   AuditLog entry (action: update)
```

## Naming Conventions

### Models
- Singular, PascalCase: `Paket`, `Wilayah`, `User`
- Match Indonesian table names exactly

### Tables
- Lowercase, singular: `paket`, `wilayah`, `users`
- Log tables: `*_logs` suffix

### Foreign Keys
- `{model}_id`: `wilayah_id`, `asrama_id`, `user_id`
- Descriptive for multiple relations: `diterima_oleh`, `diantar_oleh`

### Enums
- PascalCase: `UserRole`, `PaketStatus`
- Values in lowercase: `admin`, `diterima`

### Factory States
- camelCase methods: `active()`, `forWilayah()`
- Descriptive: `tanpaWilayah()`, `keamanan()`

## Testing Structure

Each model has factory support for testing:

```php
// Simple creation
Wilayah::factory()->create();

// With state
User::factory()->keamanan()->create();

// With relationship
Asrama::factory()->forWilayah(1)->create();

// Multiple
Paket::factory()->count(50)->create();
```

## Quick Navigation

- **Getting Started**: See `README.md`
- **Database Schema**: See `docs/DATABASE_SCHEMA.md`
- **Running Migrations**: See `docs/MIGRATIONS_AND_SEEDERS.md`
- **Using Models**: See `docs/MODELS.md`
- **Common Commands**: See `docs/QUICK_REFERENCE.md`
- **Implementation Details**: See `IMPLEMENTATION_SUMMARY.md`
