# SneakerStock

SneakerStock is a Laravel + Livewire inventory management system built for sneaker stores and retail teams. It helps you organize categories, add stock variants, manage prices, upload product pictures, and monitor low-stock items from a clean admin dashboard.

## Features

- Inventory dashboard with product summary metrics
- Category management with image support
- Product variant management (brand, model, size, color, SKU, price, stock)
- Stock movement tracking
- Image uploads for products and categories
- Low-stock alerts and summary views
- Admin role and authentication flow
- SQLite-based setup for fast local development

## Tech stack

- Laravel 12
- Livewire 4
- Fortify authentication
- Spatie Laravel Permission
- Vite + Tailwind CSS
- SQLite

## Requirements

Before installing, make sure you have:

- PHP 8.2+
- Composer
- Node.js 18+
- npm
- SQLite enabled in your PHP installation

## Installation

### 1) Clone the project

```bash
git clone https://github.com/Esmayli/sneakerstock.git
cd sneakerstock
```

### 2) Install PHP dependencies

```bash
composer install
```

### 3) Install frontend dependencies

```bash
npm install
```

### 4) Configure environment

Copy the example environment file:

```bash
cp .env.example .env
```

Generate the app key:

```bash
php artisan key:generate
```

### 5) Create the SQLite database file

```bash
touch database/database.sqlite
```

### 6) Run migrations and seed sample data

```bash
php artisan migrate --seed
```

### 7) Build the frontend assets

```bash
npm run build
```

### 8) Start the application

```bash
php artisan serve
```

Open:

```text
http://localhost:8000
```

## Admin account setup

If you want to create an admin user manually, add these variables to your `.env` file before running the seed command:

```env
ADMIN_NAME=Administrator
ADMIN_EMAIL=admin@example.com
ADMIN_PASSWORD=StrongPassword123
```

Then run:

```bash
php artisan db:seed --class=AdminUserSeeder
```

## Development mode

If you want Vite in watch mode while working locally:

```bash
npm run dev
```

Then in another terminal:

```bash
php artisan serve
```

## Useful commands

```bash
php artisan migrate
php artisan db:seed
php artisan test
php artisan storage:link
php artisan optimize
```

## Project structure

```text
app/            Laravel application code
config/         Configuration files
database/       Migrations and seeders
public/         Public assets and entry points
resources/      Blade templates, CSS, JS
routes/          Web routes
storage/        Logs and generated file storage
tests/          Test suite
```

## License

This project is licensed under the MIT License.
