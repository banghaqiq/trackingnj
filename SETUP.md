# Paket Masuk/Keluar System - Setup Instructions

This is a Laravel 12 application for managing incoming and outgoing parcels with support for Indonesian and English languages.

## Prerequisites

- PHP 8.3 or higher
- Composer
- Node.js 18+ and npm
- MySQL 5.7+ or MariaDB

## Installation

### 1. Clone and Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 2. Environment Configuration

```bash
# Copy the example environment file
cp .env.example .env

# Generate the application key
php artisan key:generate
```

### 3. Database Setup

Update your `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=paket_system
DB_USERNAME=root
DB_PASSWORD=
```

Then run migrations:

```bash
php artisan migrate
```

### 4. Build Frontend Assets

```bash
# Development build with hot reload
npm run dev

# Production build
npm run build
```

## Running the Application

### Start the Development Server

In one terminal, run:

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

In another terminal, run the frontend development server:

```bash
npm run dev
```

## Language Configuration

The application supports **Indonesian (id)** and **English (en)** languages.

### Current Settings

- **Default Locale**: Indonesian (id)
- **Fallback Locale**: English (en)
- **Timezone**: Asia/Jakarta
- **Date Format**: yyyy-mm-dd

### Switching Languages

#### Option 1: Via Language Switcher (Recommended)

Click the language button in the top-right corner of the navigation bar and select your preferred language.

#### Option 2: Programmatically

```php
// In your controller or anywhere in your code
session(['locale' => 'en']);
app()->setLocale('en');
```

The language preference is stored in the session and persists during your browsing session.

### Adding Translation Strings

Translation files are located in:
- English: `resources/lang/en/messages.php`
- Indonesian: `resources/lang/id/messages.php`

To add a new string:

```php
// In resources/lang/en/messages.php
return [
    'example_key' => 'Example value in English',
];

// In resources/lang/id/messages.php
return [
    'example_key' => 'Nilai contoh dalam Bahasa Indonesia',
];
```

Use in Blade templates:

```blade
{{ __('messages.example_key') }}
```

## Features

- **Navigation Menu** with links to:
  - Dashboard
  - Paket Masuk/Keluar (Incoming/Outgoing Parcels)
  - Data Paket (Parcel Data)
  - Laporan (Reports)
  - Pengaturan (Settings)

- **Multi-Language Support**: Seamlessly switch between Indonesian and English
- **Responsive Design**: Built with Tailwind CSS for mobile-friendly interface
- **Dark Mode**: Automatic dark mode support
- **Session-Based Language Switching**: Language preference is saved to user session

## Development Tools

### Run Tests

```bash
php artisan test
```

### Code Quality

```bash
# Run PHP lint with Pint
./vendor/bin/pint
```

### Database Migrations

```bash
# Create a new migration
php artisan make:migration create_table_name

# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Reset database
php artisan migrate:fresh
```

## Project Structure

```
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── DashboardController.php
│   │   │   └── LanguageController.php
│   │   └── Middleware/
│   │       └── SetLocale.php
├── resources/
│   ├── css/
│   │   └── app.css
│   ├── js/
│   │   └── app.js
│   ├── lang/
│   │   ├── en/
│   │   │   └── messages.php
│   │   └── id/
│   │       └── messages.php
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php
│       └── dashboard.blade.php
├── routes/
│   └── web.php
├── config/
│   ├── app.php (timezone & locale settings)
│   └── database.php (database configuration)
└── public/
    └── index.php
```

## Environment Variables

Important environment variables in `.env`:

```env
# Application
APP_NAME=Paket Masuk/Keluar System
APP_ENV=local
APP_DEBUG=true
APP_TIMEZONE=Asia/Jakarta

# Locale
APP_LOCALE=id
APP_FALLBACK_LOCALE=en

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=paket_system
DB_USERNAME=root
DB_PASSWORD=
```

## Troubleshooting

### Sessions Not Persisting Language

Ensure `SESSION_DRIVER=database` is set in your `.env` file and sessions table is created:

```bash
php artisan session:table
php artisan migrate
```

### CSS/JS Not Loading

Make sure to build frontend assets:

```bash
npm run dev  # For development
npm run build  # For production
```

### Database Connection Issues

Verify your MySQL credentials in `.env` and ensure the database exists:

```bash
mysql -u root -p
CREATE DATABASE paket_system;
```

## License

This project is open source and available under the MIT license.

## Support

For issues or questions, please check the Laravel documentation at https://laravel.com/docs
