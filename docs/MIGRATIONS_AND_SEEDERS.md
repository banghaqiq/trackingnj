# Migrations and Seeders Guide

## Overview

This guide explains how to run database migrations and seeders for the package management system.

## Prerequisites

Before running migrations and seeders, ensure you have:

1. PHP 8.1 or higher installed
2. Composer installed
3. Laravel framework installed
4. Database server (MySQL/PostgreSQL/SQLite) configured
5. Database connection configured in `.env` file

## Database Configuration

Configure your database connection in the `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

For SQLite (development):
```env
DB_CONNECTION=sqlite
DB_DATABASE=/path/to/database.sqlite
```

## Running Migrations

### 1. Run All Migrations

To create all database tables:

```bash
php artisan migrate
```

This will create the following tables in order:
1. `wilayah` - Regions table
2. `asrama` - Dormitories table
3. `users` - Users table
4. `paket` - Packages table
5. `paket_status_logs` - Package status logs table
6. `audit_logs` - Audit logs table

### 2. Migration Options

**Fresh migration (drop all tables and recreate):**
```bash
php artisan migrate:fresh
```

**Fresh migration with seeding:**
```bash
php artisan migrate:fresh --seed
```

**Rollback last migration:**
```bash
php artisan migrate:rollback
```

**Rollback all migrations:**
```bash
php artisan migrate:reset
```

**Check migration status:**
```bash
php artisan migrate:status
```

## Running Seeders

### 1. Run All Seeders

To populate the database with initial data:

```bash
php artisan db:seed
```

This will seed:
- 5 regions (wilayah)
- 10 dormitories (asrama)
- 7 users (1 admin, 1 petugas_pos, 5 keamanan)

### 2. Run Specific Seeder

**Seed only wilayah:**
```bash
php artisan db:seed --class=WilayahSeeder
```

**Seed only asrama:**
```bash
php artisan db:seed --class=AsramaSeeder
```

**Seed only users:**
```bash
php artisan db:seed --class=UserSeeder
```

### 3. Fresh Migration with Seeding

The most common command for development:

```bash
php artisan migrate:fresh --seed
```

This will:
1. Drop all existing tables
2. Run all migrations
3. Run all seeders

## Seeded Data

### Default Users

After seeding, you can login with these credentials:

| Role         | Username        | Email                      | Password  | Wilayah   |
|--------------|-----------------|----------------------------|-----------|-----------|
| Admin        | admin           | admin@example.com          | password  | -         |
| Petugas Pos  | petugas_pos     | pos@example.com            | password  | -         |
| Keamanan     | keamanan_utara  | keamanan.utara@example.com | password  | Utara     |
| Keamanan     | keamanan_selatan| keamanan.selatan@example.com| password | Selatan   |
| Keamanan     | keamanan_timur  | keamanan.timur@example.com | password  | Timur     |
| Keamanan     | keamanan_barat  | keamanan.barat@example.com | password  | Barat     |
| Keamanan     | keamanan_tengah | keamanan.tengah@example.com| password  | Tengah    |

### Default Wilayah (Regions)

| ID | Nama             | Kode      |
|----|------------------|-----------|
| 1  | Wilayah Utara    | WLY-UTR   |
| 2  | Wilayah Selatan  | WLY-SLT   |
| 3  | Wilayah Timur    | WLY-TMR   |
| 4  | Wilayah Barat    | WLY-BRT   |
| 5  | Wilayah Tengah   | WLY-TGH   |

### Default Asrama (Dormitories)

| Wilayah  | Nama                  | Kode   | Kapasitas |
|----------|-----------------------|--------|-----------|
| Utara    | Asrama Putra Utara A  | APU-A  | 100       |
| Utara    | Asrama Putri Utara B  | APU-B  | 80        |
| Selatan  | Asrama Putra Selatan A| APS-A  | 120       |
| Selatan  | Asrama Putri Selatan B| APS-B  | 90        |
| Timur    | Asrama Putra Timur A  | APT-A  | 110       |
| Timur    | Asrama Putri Timur B  | APT-B  | 85        |
| Barat    | Asrama Putra Barat A  | APB-A  | 95        |
| Barat    | Asrama Putri Barat B  | APB-B  | 75        |
| Tengah   | Asrama Putra Tengah   | APT-C  | 150       |
| Tengah   | Asrama Putri Tengah   | APT-D  | 130       |

## Using Factories for Testing

The system includes factories for generating test data. You can use them in tests or tinker:

### Using Tinker

```bash
php artisan tinker
```

**Create test wilayah:**
```php
\App\Models\Wilayah::factory()->count(3)->create();
```

**Create test asrama:**
```php
\App\Models\Asrama::factory()->count(5)->create();
```

**Create test users:**
```php
// Create admin
\App\Models\User::factory()->admin()->create();

// Create petugas pos
\App\Models\User::factory()->petugasPos()->create();

// Create keamanan
\App\Models\User::factory()->keamanan()->create();
```

**Create test packages:**
```php
// Create 10 packages
\App\Models\Paket::factory()->count(10)->create();

// Create packages with specific status
\App\Models\Paket::factory()->diterima()->count(5)->create();
\App\Models\Paket::factory()->selesai()->count(3)->create();

// Create packages without wilayah
\App\Models\Paket::factory()->tanpaWilayah()->count(2)->create();

// Create family packages
\App\Models\Paket::factory()->keluarga()->count(3)->create();
```

**Create status logs:**
```php
\App\Models\PaketStatusLog::factory()->count(20)->create();
```

**Create audit logs:**
```php
\App\Models\AuditLog::factory()->count(50)->create();
```

### Factory States

**Wilayah Factory:**
- `active()` - Create active wilayah
- `inactive()` - Create inactive wilayah

**Asrama Factory:**
- `active()` - Create active asrama
- `inactive()` - Create inactive asrama
- `forWilayah($id)` - Create asrama for specific wilayah

**User Factory:**
- `admin()` - Create admin user
- `petugasPos()` - Create petugas pos user
- `keamanan()` - Create keamanan user
- `active()` - Create active user
- `inactive()` - Create inactive user
- `unverified()` - Create unverified user
- `forWilayah($id)` - Assign to specific wilayah

**Paket Factory:**
- `diterima()` - Create package with diterima status
- `diproses()` - Create package with diproses status
- `diantar()` - Create package with diantar status
- `selesai()` - Create package with selesai status
- `dikembalikan()` - Create package with dikembalikan status
- `tanpaWilayah()` - Create package without wilayah
- `keluarga()` - Create family package
- `forWilayah($id)` - Assign to specific wilayah
- `forAsrama($id)` - Assign to specific asrama

## Common Scenarios

### 1. Fresh Start (Development)

```bash
# Drop all tables, migrate, and seed
php artisan migrate:fresh --seed
```

### 2. Add New Migration

```bash
# Create new migration
php artisan make:migration add_column_to_table

# Run new migration
php artisan migrate
```

### 3. Reset and Reseed Database

```bash
# Reset database
php artisan migrate:fresh

# Seed database
php artisan db:seed
```

### 4. Create Test Data

```bash
php artisan tinker
```

Then run:
```php
// Create 50 test packages with related data
\App\Models\Paket::factory()
    ->count(50)
    ->create();

// Create packages for specific wilayah
$wilayah = \App\Models\Wilayah::first();
\App\Models\Paket::factory()
    ->forWilayah($wilayah->id)
    ->count(10)
    ->create();
```

## Troubleshooting

### Error: "Database not found"

**Solution:** Create the database first:
```bash
mysql -u root -p
CREATE DATABASE your_database_name;
exit;
```

### Error: "SQLSTATE[HY000] [2002] Connection refused"

**Solution:** Ensure your database server is running:
```bash
# For MySQL
sudo systemctl start mysql

# For PostgreSQL
sudo systemctl start postgresql
```

### Error: "Class 'Database\Seeders\...' not found"

**Solution:** Run composer autoload:
```bash
composer dump-autoload
```

### Error: "Integrity constraint violation"

**Solution:** Run fresh migration:
```bash
php artisan migrate:fresh --seed
```

## Best Practices

1. **Always backup** before running migrations in production
2. **Test migrations** in development environment first
3. **Use transactions** in seeders for data consistency
4. **Version control** all migration files
5. **Document changes** in migration comments
6. **Use factories** for test data instead of hardcoding
7. **Run seeders separately** in production for better control

## Production Deployment

For production environments:

```bash
# Run migrations only (no seeding)
php artisan migrate --force

# Seed only necessary data
php artisan db:seed --class=WilayahSeeder --force
php artisan db:seed --class=AsramaSeeder --force

# Create admin user manually with strong password
php artisan tinker
```

Then create admin:
```php
\App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@yourdomain.com',
    'username' => 'admin',
    'password' => bcrypt('strong_password_here'),
    'role' => 'admin',
    'is_active' => true,
    'email_verified_at' => now(),
]);
```

## Additional Resources

- [Laravel Migrations Documentation](https://laravel.com/docs/migrations)
- [Laravel Seeding Documentation](https://laravel.com/docs/seeding)
- [Laravel Factories Documentation](https://laravel.com/docs/eloquent-factories)
- Database Schema Documentation (see DATABASE_SCHEMA.md)
- Models Documentation (see MODELS.md)
