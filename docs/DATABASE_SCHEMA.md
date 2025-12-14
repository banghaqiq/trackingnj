# Database Schema Documentation

## Overview

This document describes the database schema for the package management system. The system manages packages (paket) across different regions (wilayah) and dormitories (asrama), with role-based access control for users.

## Entity Relationship Diagram

```
┌─────────────┐       ┌─────────────┐       ┌─────────────┐
│   Wilayah   │◄──────│   Asrama    │       │    Users    │
│             │ 1   * │             │       │             │
│ - id        │       │ - id        │       │ - id        │
│ - nama      │       │ - wilayah_id│       │ - name      │
│ - kode      │       │ - nama      │       │ - email     │
│             │       │ - kode      │       │ - role      │
└─────────────┘       └─────────────┘       │ - wilayah_id│
      │                     │               └─────────────┘
      │ 1                   │ 1                    │
      │                     │                      │
      │ *                   │ *                    │ 1
      │                     │                      │
┌─────────────────────────────────────────────────┘
│                                                  │
│            ┌─────────────┐                       │
│            │    Paket    │                       │
│            │             │                       │
│            │ - id        │                       │
│            │ - kode_resi │                       │
│            │ - wilayah_id│                       │
│            │ - asrama_id │                       │
│            │ - status    │                       │
│            │ - ...       │                       │
│            └─────────────┘                       │
│                  │                               │
│                  │ 1                             │
│                  │                               │
│                  │ *                             │ *
│            ┌──────────────────┐          ┌─────────────┐
│            │PaketStatusLog    │          │  AuditLog   │
│            │                  │          │             │
└───────────►│ - paket_id       │          │ - user_id   │
             │ - status_dari    │          │ - action    │
             │ - status_ke      │          │ - ...       │
             │ - diubah_oleh    │          └─────────────┘
             └──────────────────┘
```

## Tables

### 1. wilayah (Regions)

Stores static list of regions/areas within the campus.

| Column      | Type         | Nullable | Description                    |
|-------------|--------------|----------|--------------------------------|
| id          | bigint       | No       | Primary key                    |
| nama        | varchar(255) | No       | Region name (unique)           |
| kode        | varchar(10)  | No       | Region code (unique)           |
| deskripsi   | text         | Yes      | Description                    |
| is_active   | boolean      | No       | Active status (default: true)  |
| created_at  | timestamp    | Yes      | Creation timestamp             |
| updated_at  | timestamp    | Yes      | Last update timestamp          |

**Indexes:**
- Primary key on `id`
- Unique index on `nama`
- Unique index on `kode`

---

### 2. asrama (Dormitories)

Stores dormitory information, each belonging to a region.

| Column      | Type         | Nullable | Description                    |
|-------------|--------------|----------|--------------------------------|
| id          | bigint       | No       | Primary key                    |
| wilayah_id  | bigint       | No       | Foreign key to wilayah         |
| nama        | varchar(255) | No       | Dormitory name (unique)        |
| kode        | varchar(10)  | No       | Dormitory code (unique)        |
| alamat      | text         | Yes      | Address                        |
| kapasitas   | integer      | Yes      | Capacity                       |
| is_active   | boolean      | No       | Active status (default: true)  |
| created_at  | timestamp    | Yes      | Creation timestamp             |
| updated_at  | timestamp    | Yes      | Last update timestamp          |

**Indexes:**
- Primary key on `id`
- Foreign key on `wilayah_id` (cascade on delete)
- Unique index on `nama`
- Unique index on `kode`

---

### 3. users

Stores user accounts with role-based access control.

| Column             | Type         | Nullable | Description                          |
|--------------------|--------------|----------|--------------------------------------|
| id                 | bigint       | No       | Primary key                          |
| name               | varchar(255) | No       | Full name                            |
| email              | varchar(255) | No       | Email address (unique)               |
| username           | varchar(255) | No       | Username (unique)                    |
| password           | varchar(255) | No       | Hashed password                      |
| role               | enum         | No       | admin/petugas_pos/keamanan           |
| wilayah_id         | bigint       | Yes      | Foreign key to wilayah (for keamanan)|
| is_active          | boolean      | No       | Active status (default: true)        |
| email_verified_at  | timestamp    | Yes      | Email verification timestamp         |
| remember_token     | varchar(100) | Yes      | Remember token                       |
| created_at         | timestamp    | Yes      | Creation timestamp                   |
| updated_at         | timestamp    | Yes      | Last update timestamp                |

**Indexes:**
- Primary key on `id`
- Unique index on `email`
- Unique index on `username`
- Foreign key on `wilayah_id` (set null on delete)
- Composite index on `(role, wilayah_id)`

**Roles:**
- `admin`: System administrator (no wilayah restriction)
- `petugas_pos`: Post office staff (handles incoming packages)
- `keamanan`: Security staff (assigned to specific wilayah)

---

### 4. paket (Packages)

Stores package information with soft deletes enabled.

| Column            | Type         | Nullable | Description                          |
|-------------------|--------------|----------|--------------------------------------|
| id                | bigint       | No       | Primary key                          |
| kode_resi         | varchar(255) | No       | Tracking number (unique)             |
| nama_penerima     | varchar(255) | No       | Recipient name                       |
| telepon_penerima  | varchar(255) | Yes      | Recipient phone                      |
| wilayah_id        | bigint       | Yes      | Foreign key to wilayah               |
| asrama_id         | bigint       | Yes      | Foreign key to asrama                |
| nomor_kamar       | varchar(255) | Yes      | Room number                          |
| alamat_lengkap    | text         | Yes      | Full address                         |
| tanpa_wilayah     | boolean      | No       | Package without region (default: false)|
| keluarga          | boolean      | No       | Family package flag (default: false) |
| nama_pengirim     | varchar(255) | Yes      | Sender name                          |
| keterangan        | text         | Yes      | Notes                                |
| status            | enum         | No       | Current status (default: diterima)   |
| tanggal_diterima  | timestamp    | No       | Received date (default: current)     |
| tanggal_diambil   | timestamp    | Yes      | Picked up date                       |
| diterima_oleh     | bigint       | Yes      | Foreign key to users (receiver)      |
| diantar_oleh      | bigint       | Yes      | Foreign key to users (deliverer)     |
| deleted_at        | timestamp    | Yes      | Soft delete timestamp                |
| created_at        | timestamp    | Yes      | Creation timestamp                   |
| updated_at        | timestamp    | Yes      | Last update timestamp                |

**Indexes:**
- Primary key on `id`
- Unique index on `kode_resi`
- Foreign key on `wilayah_id` (set null on delete)
- Foreign key on `asrama_id` (set null on delete)
- Foreign key on `diterima_oleh` (set null on delete)
- Foreign key on `diantar_oleh` (set null on delete)
- Composite index on `(status, wilayah_id)`
- Composite index on `(tanpa_wilayah, keluarga)`
- Index on `tanggal_diterima`

**Status Values:**
- `diterima`: Package received at post office
- `diproses`: Package being processed
- `diantar`: Package out for delivery
- `selesai`: Package delivered successfully
- `dikembalikan`: Package returned

**Flags:**
- `tanpa_wilayah`: Package not assigned to any region
- `keluarga`: Package for family members (not students)

---

### 5. paket_status_logs

Tracks all status transitions for packages.

| Column       | Type      | Nullable | Description                          |
|--------------|-----------|----------|--------------------------------------|
| id           | bigint    | No       | Primary key                          |
| paket_id     | bigint    | No       | Foreign key to paket                 |
| status_dari  | enum      | Yes      | Previous status                      |
| status_ke    | enum      | No       | New status                           |
| diubah_oleh  | bigint    | Yes      | Foreign key to users (who changed)   |
| catatan      | text      | Yes      | Notes about the change               |
| created_at   | timestamp | No       | Change timestamp (default: current)  |

**Indexes:**
- Primary key on `id`
- Foreign key on `paket_id` (cascade on delete)
- Foreign key on `diubah_oleh` (set null on delete)
- Composite index on `(paket_id, created_at)`

---

### 6. audit_logs

Tracks all user actions for audit purposes.

| Column      | Type             | Nullable | Description                    |
|-------------|------------------|----------|--------------------------------|
| id          | bigint           | No       | Primary key                    |
| user_id     | bigint           | Yes      | Foreign key to users           |
| user_name   | varchar(255)     | Yes      | User name (cached)             |
| role        | enum             | Yes      | User role at action time       |
| action      | varchar(255)     | No       | Action performed               |
| model_type  | varchar(255)     | Yes      | Model class name               |
| model_id    | bigint           | Yes      | Model ID                       |
| old_values  | json             | Yes      | Previous values (for updates)  |
| new_values  | json             | Yes      | New values                     |
| ip_address  | varchar(45)      | Yes      | IP address                     |
| user_agent  | text             | Yes      | User agent string              |
| created_at  | timestamp        | No       | Action timestamp               |

**Indexes:**
- Primary key on `id`
- Foreign key on `user_id` (set null on delete)
- Composite index on `(user_id, created_at)`
- Composite index on `(model_type, model_id)`
- Index on `action`

---

## Data Seeding

The system includes pre-configured seed data:

### Wilayah (5 regions)
- Wilayah Utara (WLY-UTR)
- Wilayah Selatan (WLY-SLT)
- Wilayah Timur (WLY-TMR)
- Wilayah Barat (WLY-BRT)
- Wilayah Tengah (WLY-TGH)

### Asrama (10 dormitories)
- 2 dormitories per region (one for male, one for female students)

### Users (7 users)
- 1 Administrator (admin)
- 1 Post Office Staff (petugas_pos)
- 5 Security Staff (keamanan), one per region

**Default credentials:** All users have password: `password`

---

## Relationships

1. **Wilayah → Asrama**: One-to-Many
2. **Wilayah → Users**: One-to-Many (for keamanan role)
3. **Wilayah → Paket**: One-to-Many
4. **Asrama → Paket**: One-to-Many
5. **Paket → PaketStatusLog**: One-to-Many
6. **Users → PaketStatusLog**: One-to-Many (via diubah_oleh)
7. **Users → AuditLog**: One-to-Many
8. **Users → Paket**: One-to-Many (via diterima_oleh, diantar_oleh)

---

## Business Rules

1. **Unique Tracking Numbers**: Each package must have a unique `kode_resi`
2. **Security Staff Assignment**: Users with role `keamanan` must be assigned to a specific `wilayah_id`
3. **Package Flags**:
   - If `tanpa_wilayah` is true, `wilayah_id` and `asrama_id` should be null
   - If `keluarga` is true, the package is for family members
4. **Status Transitions**: All status changes must be logged in `paket_status_logs`
5. **Soft Deletes**: Deleted packages are not permanently removed; they're marked with `deleted_at`
6. **Audit Trail**: All significant user actions should be logged in `audit_logs`

---

## Performance Considerations

1. Indexes are created on frequently queried columns
2. Composite indexes are used for common multi-column queries
3. Foreign keys use appropriate ON DELETE actions to maintain referential integrity
4. Soft deletes allow recovery of accidentally deleted packages
5. Status logs and audit logs use single timestamp column (created_at only)
