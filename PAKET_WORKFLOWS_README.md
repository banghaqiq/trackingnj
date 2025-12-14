# Paket Workflows Implementation

This document describes the paket workflows implementation for the package management system.

## Overview

The paket workflows provide complete functionality for managing packages through three main views:
1. **Paket Masuk** (Incoming Packages) - Packages that haven't been picked up or are in transit
2. **Paket Keluar** (Outgoing Packages) - Packages that have been delivered or returned
3. **Seluruh Data Paket** (All Packages) - Complete package listing with all statuses

## Features Implemented

### 1. Barcode Scanning
- WebRTC-based barcode scanning using QuaggaJS library
- Supports multiple barcode formats (Code 128, EAN, UPC, Code 39, etc.)
- Fallback to manual entry if camera is unavailable
- Real-time resi code validation

### 2. Form Validations
- Required field validation for: kode_resi, nama_penerima, telepon_penerima
- Unique constraint validation for kode_resi
- Dynamic wilayah/asrama filtering
- Client-side and server-side validation

### 3. Status Workflow Rules

#### Status Flow
```
BELUM_DIAMBIL (default)
    ↓
DIAMBIL (only petugas_pos can set)
    ↓
DITERIMA (keamanan/petugas_pos can set)
    ↓
SELESAI
    
Alternative paths:
- SALAH_WILAYAH (keamanan/petugas_pos can set from any status)
- DIKEMBALIKAN (any authorized user can set)
```

#### Role-Based Permissions
- **Admin**: Can perform all operations
- **Petugas Pos**: 
  - Can mark packages as DIAMBIL
  - Can mark packages as DITERIMA or SALAH_WILAYAH
  - Can soft delete and force delete packages
- **Keamanan**: 
  - Can mark packages as DITERIMA or SALAH_WILAYAH
  - Can only view packages in their assigned wilayah
  - Cannot force delete packages

#### Status Rollback
- All status changes are logged
- Users can change status back to previous states
- History is preserved in paket_status_logs table

### 4. Pagination and Filtering

#### Pagination
- Configurable items per page: 10, 25, or 50
- Maintains filter parameters across pages
- Shows total results and current page range

#### Filtering Options
- Search by: kode_resi, nama_penerima, telepon_penerima, nama_pengirim
- Filter by status (all statuses available)
- Filter by date range (tanggal_mulai, tanggal_akhir)
- Combined filters work together

### 5. Soft Delete and Hard Delete

#### Soft Delete
- Available to all authorized users
- Marks package as deleted but keeps data
- Deleted packages visible in main listing with indicator
- Can be restored by any authorized user

#### Hard Delete (Force Delete)
- Only available to Admin and Petugas Pos
- Permanently removes package from database
- Audit log entry preserved even after hard delete
- Confirmation required before deletion

### 6. Audit Logging

Every operation logs to both tables:

#### paket_status_logs
- Records: status_dari, status_ke, diubah_oleh, catatan
- Automatically created on status changes
- Displayed in package detail view as timeline

#### audit_logs
- Records all CRUD operations:
  - create_paket
  - update_paket
  - update_status
  - soft_delete_paket
  - force_delete_paket
  - restore_paket
- Includes: user_id, user_name, role, old_values, new_values, timestamp

## File Structure

```
app/
├── Enums/
│   ├── PaketStatus.php          # Updated with new statuses
│   └── UserRole.php
├── Http/
│   └── Controllers/
│       ├── PaketController.php       # Main CRUD controller
│       ├── PaketMasukController.php  # Incoming packages
│       └── PaketKeluarController.php # Outgoing packages
├── Models/
│   ├── Paket.php                # Updated with new helper methods
│   ├── PaketStatusLog.php
│   └── AuditLog.php
└── Services/
    └── PaketService.php         # Business logic layer

resources/
├── lang/
│   └── id/
│       └── paket.php            # Indonesian translations
└── views/
    ├── layouts/
    │   └── app.blade.php        # Main layout
    ├── components/
    │   └── barcode-scanner.blade.php  # Barcode scanner modal
    └── paket/
        ├── index.blade.php      # All packages view
        ├── masuk.blade.php      # Incoming packages view
        ├── keluar.blade.php     # Outgoing packages view
        ├── create.blade.php     # Create form
        ├── edit.blade.php       # Edit form
        └── show.blade.php       # Detail view

routes/
└── web.php                      # Route definitions

public/
├── css/
│   └── app.css                  # Custom styles
└── js/
    └── (QuaggaJS loaded via CDN)
```

## Routes

```php
// Paket Masuk
GET  /paket/masuk

// Paket Keluar
GET  /paket/keluar

// Main Paket CRUD
GET    /paket              # List all packages
GET    /paket/create       # Show create form
POST   /paket              # Store new package
GET    /paket/{paket}      # Show package details
GET    /paket/{paket}/edit # Show edit form
PUT    /paket/{paket}      # Update package
DELETE /paket/{paket}      # Soft delete package

// Status Update
POST /paket/{paket}/status # Update package status

// Force Delete and Restore
DELETE /paket/{id}/force   # Force delete (admin/petugas_pos only)
POST   /paket/{id}/restore # Restore soft-deleted package

// AJAX
POST /paket/check-resi     # Check if resi code exists
```

## Usage Examples

### Creating a Package

1. Navigate to "Tambah Paket Baru"
2. Either:
   - Click "Scan Barcode" and scan the package barcode
   - Manually enter the kode_resi
3. Fill in required fields: nama_penerima, telepon_penerima
4. Optionally select wilayah and asrama
5. Check boxes for "Tanpa Wilayah" or "Paket Keluarga" if applicable
6. Click "Simpan"

### Updating Status

**From Package Detail Page:**
1. Use the status update form in the right sidebar
2. Select new status
3. Optionally add notes
4. Click "Update Status"

**From Paket Masuk Page (Petugas Pos only):**
1. Click the check icon on a "Belum Diambil" package
2. Status automatically changes to "Diambil"

**From Paket Keluar Page:**
1. Click the dropdown menu on a package
2. Select desired status change
3. Enter notes when prompted

### Filtering Packages

1. Use the filter form at the top of any listing page
2. Enter search terms (searches across multiple fields)
3. Select status filter (if on "Seluruh Data Paket" page)
4. Set date range if needed
5. Click "Filter"
6. Click "Reset" to clear all filters

### Deleting Packages

**Soft Delete:**
1. From listing page, click trash icon
2. Confirm deletion
3. Package marked as deleted but still visible

**Restore:**
1. Find deleted package in listing (marked with "Paket Terhapus" badge)
2. Click restore icon
3. Confirm restoration

**Force Delete (Admin/Petugas Pos only):**
1. Find soft-deleted package
2. Click force delete icon (filled trash icon)
3. Confirm permanent deletion
4. Package removed from database (audit log remains)

## Translation Keys

All UI text uses translation keys from `resources/lang/id/paket.php`:
- Page titles
- Form labels
- Status labels
- Action buttons
- Messages
- Validation errors

Example usage in Blade:
```blade
{{ __('paket.create_package') }}
{{ __('paket.tracking_code') }}
{{ __('paket.status_belum_diambil') }}
```

## JavaScript Features

### Barcode Scanner
- Initializes QuaggaJS when modal opens
- Stops scanner when modal closes
- Automatically checks if scanned resi already exists
- Fills kode_resi input with scanned value

### Dynamic Filtering
- Asrama dropdown filters based on selected wilayah
- Per-page selector updates URL and reloads
- Status update functions with AJAX

### Form Validation
- Real-time resi code checking
- Client-side required field validation
- Dynamic asrama filtering based on wilayah

## Security Considerations

1. **Authorization**: All routes require authentication
2. **Role-based access**: Controllers check user role before allowing operations
3. **CSRF Protection**: All forms include CSRF token
4. **Input Validation**: Server-side validation for all inputs
5. **SQL Injection Prevention**: Using Eloquent ORM
6. **Audit Trail**: All changes logged with user information

## Database Impact

### New Status Values
The PaketStatus enum has been updated:
- Added: BELUM_DIAMBIL, DIAMBIL, SALAH_WILAYAH
- Removed: DIPROSES, DIANTAR

### Logging Tables
Both tables are automatically populated:
- `paket_status_logs`: Every status change
- `audit_logs`: Every CRUD operation

### Soft Deletes
The `paket` table includes `deleted_at` column for soft deletes.

## Performance Considerations

1. **Eager Loading**: Relationships are eager loaded to prevent N+1 queries
2. **Pagination**: Large datasets are paginated
3. **Indexes**: Database indexes on frequently queried columns
4. **Caching**: QuaggaJS library loaded from CDN (cached by browser)

## Browser Compatibility

- Modern browsers (Chrome, Firefox, Safari, Edge)
- Camera API for barcode scanning (requires HTTPS in production)
- Bootstrap 5 for responsive design
- Progressive enhancement (works without JavaScript for basic features)

## Future Enhancements

Potential improvements:
1. Export to Excel/PDF
2. Bulk status updates
3. Email/SMS notifications
4. Advanced reporting and analytics
5. Mobile app integration
6. Print barcode labels
7. Package photo upload
8. Digital signature capture
