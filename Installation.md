Saya akan buatkan file INSTALLATION.md lengkap untuk Anda dalam format markdown yang mudah dibaca. Berikut isinya:

# INSTALLATION GUIDE - Sistem Pendataan & Distribusi Paket Pesantren

Panduan lengkap untuk menginstal dan menjalankan aplikasi Laravel di environment lokal.

---

## 📋 PREREQUISITES (Persiapan Awal)

Pastikan Anda sudah memiliki software berikut terinstall di komputer:

| Software | Versi | Cara Verifikasi |
|----------|-------|-----------------|
| PHP | 8.1+ | `php -v` |
| Composer | Latest | `composer --version` |
| MySQL Server | 5.7+ | `mysql --version` atau cek MySQL Services |
| Git | Latest | `git --version` |
| Node.js | 14+ | `node -v` |
| npm | 6+ | `npm -v` |

### **Instalasi Jika Belum Ada:**

**Windows:**
- PHP: https://windows.php.net/download/
- Composer: https://getcomposer.org/download/
- MySQL: https://dev.mysql.com/downloads/mysql/
- Git: https://git-scm.com/download/win
- Node.js: https://nodejs.org/

**macOS:**
```bash
# Menggunakan Homebrew
brew install php composer mysql node git
Linux (Ubuntu/Debian):

sudo apt-get update
sudo apt-get install php php-mysql php-xml php-mbstring composer mysql-server git nodejs npm
🚀 LANGKAH-LANGKAH INSTALASI
STEP 1: Clone Repository
git clone https://github.com/yourusername/trackingnj.git
cd trackingnj
STEP 2: Install Dependencies
Install PHP Dependencies via Composer:
composer install
Install JavaScript Dependencies:
npm install
Proses ini akan mengunduh semua package yang dibutuhkan ke folder vendor dan node_modules.

STEP 3: Setup Environment File
Copy file .env.example menjadi .env:

cp .env.example .env
Untuk Windows CMD:

copy .env.example .env
Kemudian generate application key:

php artisan key:generate
STEP 4: Configure .ENV File
Edit file .env di root directory dan sesuaikan dengan konfigurasi lokal Anda:

#=====================================
# APPLICATION
#=====================================
APP_NAME="Sistem Pendataan Paket Pesantren"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_TIMEZONE=Asia/Jakarta

#=====================================
# DATABASE
#=====================================
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=trackingnj_db
DB_USERNAME=root
DB_PASSWORD=

#=====================================
# JWT AUTHENTICATION
#=====================================
JWT_SECRET=your-jwt-secret-key-here
JWT_ALGORITHM=HS256
JWT_EXPIRES_IN=60480

#=====================================
# MAIL (Optional untuk local testing)
#=====================================
MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@pesantren.test"
MAIL_FROM_NAME="Sistem Paket"

#=====================================
# LOCALE
#=====================================
APP_LOCALE=id
APP_FALLBACK_LOCALE=en
Penjelasan field penting:

| Field | Keterangan |
|-------|-----------|
| APP_TIMEZONE | Harus Asia/Jakarta untuk format tanggal & waktu yang benar |
| DB_DATABASE | Nama database yang akan dibuat di MySQL |
| DB_USERNAME | Username MySQL (default root untuk local) |
| DB_PASSWORD | Password MySQL (default kosong untuk local XAMPP/WAMP) |
| JWT_SECRET | Akan di-generate otomatis di STEP 6 |

STEP 5: Create Database
Buka MySQL client pilihan Anda:

Option A: MySQL CLI

mysql -u root -p
Option B: phpMyAdmin (http://localhost/phpmyadmin)

Option C: MySQL Workbench

Kemudian jalankan query:

CREATE DATABASE trackingnj_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
Verifikasi database berhasil dibuat:

SHOW DATABASES;
STEP 6: Run Migrations & Seeders
Jalankan migrasi database untuk membuat tabel:

php artisan migrate
Kemudian jalankan seeder untuk menambahkan data default:

php artisan db:seed
ATAU dalam satu command (recommended untuk fresh install):

php artisan migrate:fresh --seed
⚠️ Warning: migrate:fresh akan menghapus semua data existing, gunakan hanya untuk setup awal.

STEP 7: Generate JWT Secret
Generate JWT secret key untuk authentication:

php artisan jwt:secret
Ini akan otomatis menambahkan JWT_SECRET ke file .env.

Verifikasi berhasil dengan melihat .env:

grep JWT_SECRET .env
STEP 8: Build Frontend Assets
Compile CSS dan JavaScript:

# Development build
npm run dev
Atau jika ingin auto-watch saat development:

npm run watch
STEP 9: Jalankan Laravel Development Server
Buka 2 terminal/command prompt terpisah:

Terminal 1: Jalankan Laravel Server
php artisan serve
Output akan menunjukkan:

Starting Laravel development server: http://127.0.0.1:8000
Terminal 2: Watch Assets (optional tapi recommended)
npm run watch
Ini akan auto-compile assets saat ada perubahan CSS/JS.

STEP 10: Verifikasi Instalasi
Buka browser ke: http://localhost:8000

✅ Checklist Verifikasi:

[ ] Halaman login muncul dengan benar
[ ] Tidak ada error 500 atau database connection error
[ ] Logo dan styling UI tampil dengan baik
🔐 Login Credentials (Default Setelah Seeding)
Database seeding akan membuat 7 user dengan password default password:

Admin Account (Full Access)
Email: admin@pesantren.test
Password: password
Role: Admin
Petugas Pos Account (Paket Masuk/Keluar)
Email: petugas@pesantren.test
Password: password
Role: Petugas Pos
Keamanan Wilayah Accounts (5 User)
| Wilayah | Email | Akses |
|---------|-------|-------|
| Utara | keamanan_utara@pesantren.test | Hanya paket wilayah Utara |
| Selatan | keamanan_selatan@pesantren.test | Hanya paket wilayah Selatan |
| Timur | keamanan_timur@pesantren.test | Hanya paket wilayah Timur |
| Barat | keamanan_barat@pesantren.test | Hanya paket wilayah Barat |
| Tengah | keamanan_tengah@pesantren.test | Hanya paket wilayah Tengah |

Password semua: password

🗂️ Data Default Setelah Seeding
Wilayah (5 Data)
| ID | Nama Wilayah |
|----|-------------|
| 1 | Utara |
| 2 | Selatan |
| 3 | Timur |
| 4 | Barat |
| 5 | Tengah |

Asrama (10 Data - 2 per Wilayah)
| Wilayah | Asrama |
|---------|--------|
| Utara | Asrama A1, Asrama A2 |
| Selatan | Asrama B1, Asrama B2 |
| Timur | Asrama C1, Asrama C2 |
| Barat | Asrama D1, Asrama D2 |
| Tengah | Asrama E1, Asrama E2 |

Database Tables yang Dibuat
| Tabel | Fungsi | Record Default |
|-------|--------|-----------------|
| users | User account | 7 (admin, petugas_pos, 5 keamanan) |
| wilayah | Daftar wilayah | 5 |
| asrama | Daftar asrama per wilayah | 10 |
| paket | Data paket | 0 (siap input) |
| paket_status_logs | Log perubahan status | 0 (otomatis terisi) |
| audit_logs | Log aktivitas user | 0 (otomatis terisi) |

📁 Storage & Permission (Jika Ada Error)
Jika terjadi error permission saat akses storage:

Linux/Mac:
chmod -R 775 storage
chmod -R 775 bootstrap/cache
Windows (Run Command Prompt as Administrator):
icacls "storage" /grant Everyone:F /T
icacls "bootstrap/cache" /grant Everyone:F /T
🛠️ Useful Commands (Development)
Clear Cache & Config
# Clear semua cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Atau sekaligus
php artisan optimize:clear
Database Operations
# Reset database (hapus semua data & buat ulang)
php artisan migrate:fresh --seed

# Rollback last migration
php artisan migrate:rollback

# Check migration status
php artisan migrate:status

# Seed data saja (tanpa migrate)
php artisan db:seed
Laravel Interactive Shell
php artisan tinker
Gunakan untuk testing query database secara interaktif:

>>> App\Models\User::all();
>>> App\Models\Paket::count();
>>> App\Models\Wilayah::with('asrama')->get();
Check Routes
php artisan route:list
Check Laravel Version
php artisan --version
🧪 Quick Testing Guide
Setelah instalasi selesai, lakukan testing dasar:

1️⃣ Test Login Admin
Buka http://localhost:8000
Login dengan email: admin@pesantren.test / password: password
Verifikasi dashboard muncul dengan statistik
2️⃣ Test Input Paket Masuk
Masuk ke menu Paket Masuk
Klik tombol + Input Paket Baru
Isi form:
Kode Resi: RES001 (atau scan barcode)
Nama Penerima: Ahmad Ridho
Jenis Kelamin: Laki-laki
Wilayah: Utara
Asrama: Asrama A1
Catatan: Test input
Klik Simpan
Verifikasi paket muncul di list Paket Masuk
3️⃣ Test Update Status
Klik paket yang baru dibuat
Ubah status menjadi Diambil
Klik Perbarui Status
Cek Log Aktivitas - seharusnya ada record perubahan status
4️⃣ Test Filter & Search
Masuk menu Seluruh Data Paket
Coba filter berdasarkan:
Kode Resi
Tanggal
Nama Penerima
Coba pagination (ubah per halaman: 10/25/50)
5️⃣ Test Export
Di menu Laporan
Pilih periode
Klik export Excel atau PDF
Verifikasi file download dan membuka dengan benar
6️⃣ Test Role-Based Access
Logout
Login dengan email: keamanan_utara@pesantren.test
Verifikasi hanya bisa lihat paket wilayah Utara
Verifikasi tidak bisa lihat menu Admin
❌ Troubleshooting
Error: SQLSTATE[HY000] [2002] Connection refused
Penyebab: MySQL Server tidak running

Solusi:

# macOS (dengan Homebrew)
brew services start mysql

# Linux (Ubuntu/Debian)
sudo service mysql start
# atau
sudo systemctl start mysql

# Windows
# Nyalakan MySQL Service via Services (services.msc)
Verifikasi MySQL sudah jalan:

mysql -u root -p
Error: Permission denied (storage)
Penyebab: Folder storage tidak punya akses write

Solusi:

# Linux/Mac
chmod -R 775 storage bootstrap/cache
sudo chown -R $USER:$USER storage bootstrap/cache

# Windows (Run as Administrator)
icacls "storage" /grant Everyone:F /T
icacls "bootstrap/cache" /grant Everyone:F /T
Error: Class 'App\Models...' not found
Penyebab: Autoload cache outdated

Solusi:

composer dump-autoload
php artisan cache:clear
Error: npm: command not found
Penyebab: Node.js tidak terinstall atau tidak di PATH

Solusi:

Install Node.js dari https://nodejs.org/
Restart terminal/command prompt
Verifikasi: npm -v
Error: Barcode scanner tidak bekerja
Penyebab: Browser keamanan (HTTPS required untuk camera access)

Solusi:

Gunakan HTTPS (setup SSL) atau
Test di localhost (browser trusted)
Check browser console untuk permission errors
Error: SQLSTATE[42S01]: Table or view already exists
Penyebab: Table sudah ada dari migrasi sebelumnya

Solusi:

# Jika ingin reset total (warning: semua data hilang)
php artisan migrate:fresh --seed

# Atau rollback semua migrasi
php artisan migrate:reset
php artisan migrate --seed
Error: Route [login] not defined
Penyebab: Routes tidak teregister dengan benar

Solusi:

php artisan route:clear
php artisan cache:clear
php artisan route:cache
📚 Database Schema Reference
Tabel: wilayah
id (Primary Key)
nama (string) - Utara, Selatan, Timur, Barat, Tengah
created_at, updated_at
Tabel: asrama
id (Primary Key)
wilayah_id (Foreign Key → wilayah.id)
nama (string) - Asrama A1, A2, B1, B2, ...
created_at, updated_at
Tabel: users
id (Primary Key)
name (string)
email (string, unique)
password (hashed)
role (enum: Admin, Petugas Pos, Keamanan Wilayah)
wilayah_id (Foreign Key, nullable - untuk Keamanan Wilayah)
created_at, updated_at
Tabel: paket
id (Primary Key)
kode_resi (string, unique)
nama_penerima (string)
jenis_kelamin (enum: Laki-laki, Perempuan)
wilayah_id (Foreign Key, nullable jika tanpa_wilayah)
asrama_id (Foreign Key, nullable jika tanpa_wilayah)
tanpa_wilayah (boolean)
keluarga (boolean)
catatan_khusus (text, nullable)
status (enum: belum_diambil, diambil, diterima, diterima_keamanan_wilayah, salah_wilayah)
nama_pengambil (string, nullable)
tanggal_diambil (date, nullable)
created_at, updated_at
deleted_at (soft delete)
Tabel: paket_status_logs
id (Primary Key)
paket_id (Foreign Key → paket.id)
status_lama (string)
status_baru (string)
diubah_oleh (Foreign Key → users.id)
catatan (text, nullable)
created_at, updated_at
Tabel: audit_logs
id (Primary Key)
user_id (Foreign Key → users.id)
aksi (string) - create, update, delete, status_change
tabel (string) - paket, wilayah, asrama
data_id (integer)
perubahan (json, nullable)
ip_address (string, nullable)
created_at
🎯 Next Steps
Setelah instalasi berhasil:

✅ Verify login dengan berbagai role
✅ Test input paket & scanning barcode
✅ Check log aktivitas
✅ Explore menu laporan & export
✅ Review database schema
✅ Custom seeding data jika diperlukan
📞 Support & Debugging
Jika masih ada error:

Check Laravel logs: storage/logs/laravel.log
Check Browser console: Press F12 untuk lihat error JavaScript
Check Network tab: Verifikasi API response
Run php artisan tinker untuk test database connection
Happy coding! 🚀
