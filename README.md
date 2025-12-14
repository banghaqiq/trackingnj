# Paket Masuk/Keluar System

A modern web application for managing incoming and outgoing parcels, built with Laravel 12, Tailwind CSS, Vite, and multi-language support (Indonesian & English).

## Features

✨ **Key Features**

- 📦 **Parcel Management System** - Track incoming and outgoing parcels
- 🌍 **Multi-Language Support** - Indonesian (id) and English (en) with session-based switching
- 🎨 **Modern UI** - Built with Tailwind CSS and responsive design
- 🌙 **Dark Mode** - Automatic dark mode support via system preferences
- ⚡ **Fast Development** - Vite for rapid development with HMR
- 📅 **Smart Date Handling** - Timezone set to Asia/Jakarta with yyyy-mm-dd format
- 🔐 **Secure** - CSRF protection and modern security practices

## Quick Start

### Prerequisites

- PHP 8.3+
- Node.js 18+ and npm
- MySQL 5.7+ or MariaDB
- Composer

### Installation & Setup

```bash
# 1. Install PHP dependencies
composer install

# 2. Install frontend dependencies
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Configure database (edit .env file)
# DB_DATABASE=paket_system
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Run migrations
php artisan migrate

# 6. Build frontend assets
npm run build
```

### Running the Application

**Terminal 1 - Start Laravel Development Server:**
```bash
php artisan serve
```

**Terminal 2 - Start Frontend Development Server (with hot reload):**
```bash
npm run dev
```

Then open your browser and navigate to `http://localhost:8000`

## Configuration

### Language & Timezone Settings

The application is pre-configured with:

| Setting | Value |
|---------|-------|
| **Timezone** | Asia/Jakarta |
| **Default Locale** | Indonesian (id) |
| **Fallback Locale** | English (en) |
| **Date Format** | yyyy-mm-dd |

### Environment Variables

Key configuration variables in `.env`:

```env
APP_NAME="Paket Masuk/Keluar System"
APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id
APP_FALLBACK_LOCALE=en
DB_CONNECTION=mysql
DB_DATABASE=paket_system
DB_USERNAME=root
DB_PASSWORD=
```

## Language Switching

### User Interface

Click the language selector button (top-right corner) to switch between:
- 🇮🇩 **Bahasa Indonesia** (id)
- 🇺🇸 **English** (en)

The language preference is stored in the session and persists during browsing.

### In Code

```php
// Set language
session(['locale' => 'id']);
app()->setLocale('id');

// Use translations
echo __('messages.dashboard'); // Will show translated string
```

### Adding Translations

Edit translation files:
- English: `resources/lang/en/messages.php`
- Indonesian: `resources/lang/id/messages.php`

```php
// Example in messages.php
return [
    'welcome' => 'Welcome',
    'logout' => 'Logout',
];
```

Use in Blade templates:
```blade
<h1>{{ __('messages.welcome') }}</h1>
```

## Project Structure

```
project/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── DashboardController.php
│   │   │   └── LanguageController.php
│   │   └── Middleware/
│   │       └── SetLocale.php
├── resources/
│   ├── css/
│   │   └── app.css (Tailwind)
│   ├── js/
│   │   └── app.js
│   ├── lang/
│   │   ├── en/messages.php
│   │   └── id/messages.php
│   └── views/
│       ├── layouts/app.blade.php
│       └── dashboard.blade.php
├── routes/
│   └── web.php
├── config/
│   ├── app.php
│   └── database.php
├── public/
│   ├── index.php
│   └── build/ (compiled assets)
├── storage/
├── tests/
├── vite.config.js
├── tailwind.config.js
├── package.json
└── composer.json
```

## Available Routes

| Method | Route | Purpose |
|--------|-------|---------|
| GET | `/` | Redirects to dashboard |
| GET | `/dashboard` | Main dashboard page |
| GET | `/language/{locale}` | Switch language (id or en) |

## Development Commands

### Laravel Artisan

```bash
# Generate new controller
php artisan make:controller MyController

# Generate new model
php artisan make:model MyModel

# Generate migration
php artisan make:migration create_table_name

# Run migrations
php artisan migrate

# Clear all caches
php artisan cache:clear
```

### Frontend Development

```bash
# Development with hot reload
npm run dev

# Production build
npm run build

# Build and watch
npm run build -- --watch
```

### Database

```bash
# Create migration
php artisan make:migration table_name

# Run migrations
php artisan migrate

# Rollback last batch
php artisan migrate:rollback

# Reset database
php artisan migrate:fresh
php artisan migrate:fresh --seed
```

## Navigation Menu

The application includes navigation links for:

- **Dashboard** - Main dashboard with statistics
- **Paket Masuk/Keluar** - Incoming/Outgoing parcels
- **Data Paket** - Parcel data management
- **Laporan** - Reports and analytics
- **Pengaturan** - Settings

(Placeholder links - ready for development)

## File Locations

| File | Purpose |
|------|---------|
| `.env` | Local environment configuration |
| `.env.example` | Template for environment variables |
| `SETUP.md` | Detailed setup and configuration guide |
| `vite.config.js` | Vite build configuration |
| `tailwind.config.js` | Tailwind CSS configuration |
| `routes/web.php` | Web route definitions |

## Troubleshooting

### "Session driver not configured"
```bash
php artisan session:table
php artisan migrate
```

### "Views not compiling"
Ensure you're running the Vite dev server:
```bash
npm run dev
```

### "Database connection error"
Check MySQL is running and update `.env` with correct credentials:
```bash
mysql -u root -p
CREATE DATABASE paket_system;
```

### "Missing dependencies"
Reinstall dependencies:
```bash
composer install
npm install
```

## Performance Tips

1. **Cache Configuration** (production):
   ```bash
   php artisan config:cache
   php artisan route:cache
   ```

2. **Optimize Autoloader**:
   ```bash
   composer install --optimize-autoloader --no-dev
   ```

3. **Build Assets** for production:
   ```bash
   npm run build
   ```

## Documentation

- **Setup Guide**: See `SETUP.md` for comprehensive setup instructions
- **Laravel Docs**: https://laravel.com/docs
- **Tailwind CSS**: https://tailwindcss.com/docs
- **Vite**: https://vitejs.dev/guide

## Security

- CSRF tokens enabled by default
- Environment variables protected
- Secure password hashing (Bcrypt)
- XSS protection via Blade escaping
- SQL injection prevention via Eloquent

## License

This project is open source and available under the MIT license.

## Support

For detailed setup and troubleshooting, refer to `SETUP.md`.

---

Built with ❤️ using Laravel 12, Tailwind CSS, and Vite
