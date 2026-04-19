# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**mustafaInvoices** is a Laravel 8.x Point of Sale (POS) system packaged as an Electron desktop app. The web app runs on XAMPP (Apache + MySQL), and Electron loads it from `http://localhost/pos_opencodee/public`.

- **Database:** MySQL, database name `MustafaPos`
- **Auth:** Laravel built-in auth + `spatie/laravel-permission` for roles
- **UI:** Blade templates, Bootstrap 5, jQuery (minimal Vue.js)
- **Desktop wrapper:** Electron 41.2.0

## Commands

### PHP / Laravel
```bash
php artisan migrate              # Run pending migrations
php artisan migrate:fresh --seed # Drop all tables and re-seed
php artisan cache:clear          # Clear app cache
php artisan config:clear         # Clear config cache
php artisan route:list           # List all routes
php artisan db:seed              # Seed the database
php artisan make:controller Name # Scaffold a controller
php artisan make:migration Name  # Scaffold a migration
php artisan make:model Name -m   # Scaffold model + migration
php artisan test                 # Run PHPUnit tests
php artisan test --filter=FooTest # Run a single test class
```

### Assets (Laravel Mix)
```bash
npm run dev          # One-time development build
npm run watch        # Watch and rebuild on changes
npm run production   # Minified production build
```

### Electron
```bash
npm run electron:start   # Launch Electron window (XAMPP must be running)
npm run electron:build   # Build Windows x64 distributable (output: /dist)
node launcher.js         # Auto-start XAMPP services then launch Electron
```

## Architecture

### Request Flow
Browser/Electron → `public/index.php` → Laravel Router (`routes/web.php`) → Controller → Model (Eloquent) → MySQL → Blade view

### Key Directories
- `app/Http/Controllers/` — All business logic; one controller per domain (Sales, Products, Customers, Suppliers, Expenses, StockAdjustments, etc.)
- `app/Models/` — Eloquent models; model names often differ from table names (e.g., `Products` model → `products` table)
- `resources/views/` — Blade templates organized by feature; `layouts/master.blade.php` is the main layout
- `database/migrations/` — Schema source of truth; 25+ migration files
- `electron/main.js` — Electron window, tray icon, and application menu logic
- `electron/preload.js` — Exposes `window.electronAPI` (minimize/maximize/close/isMaximized) to renderer via context bridge
- `routes/web.php` — ~80 routes; all protected by `auth` middleware except login

### POS Screen (`resources/views/sales/pos.blade.php`)
The most complex view. Key behaviors:
- Tax rate is a PHP constant `TAX_RATE = 0.15` (15%) set in the controller
- Payment modes: `cash`, `card`, `split` — split payment requires both `cash_amount` and `card_amount`
- Suspended sales are stored in the `suspended_sales` table and restored via AJAX
- Barcode scanning triggers product lookup via a dedicated AJAX endpoint
- Keyboard shortcuts: `F2` = open payment modal, `F4` = suspend sale, `Esc` = clear cart

### Roles & Permissions
Managed via `spatie/laravel-permission`. Roles and permissions are seeded and checked using `@can` directives in Blade and `$this->authorize()` in controllers.

### Excel Exports
`app/Exports/` contains Maatwebsite Excel export classes used for sales reports and inventory lists.

### Electron ↔ Laravel Integration
Electron does not bundle the PHP app — it loads the existing XAMPP-served Laravel app over HTTP. `launcher.js` uses `child_process.spawn` to start Apache and MySQL via the XAMPP binary before creating the Electron window.

## Environment
Copy `.env.example` to `.env` and set:
```
DB_DATABASE=MustafaPos
DB_USERNAME=root
DB_PASSWORD=         # empty by default for XAMPP
APP_URL=http://localhost
```
Run `php artisan key:generate` after first setup.
