# SneakerStock

SneakerStock is a Laravel + Livewire inventory management app for sneaker stores. It allows you to manage categories, add inventory variants, track stock levels, upload product images, and monitor low-stock alerts from a single dashboard.

## Features

- Inventory dashboard with stock summary
- CRUD for product categories
- Product variant management with brand, model, size, color, SKU, prices, and stock
- Image upload for each variant
- Low-stock alerting
- Role-based admin access
- SQLite-ready setup for quick local development

## Requirements

- PHP 8.2+
- Composer
- Node.js 18+
- npm
- SQLite extension enabled in PHP (default local setup)

## Quick start

1. Clone the repository
   ```bash
   git clone https://github.com/Esmayli/sneakerstock.git
   cd sneakerstock
   ```

2. Install PHP dependencies
   ```bash
   composer install
   ```

3. Install frontend dependencies
   ```bash
   npm install
   ```

4. Create your environment file
   ```bash
   cp .env.example .env
   ```

5. Generate the application key
   ```bash
   php artisan key:generate
   ```

6. Initialize the SQLite database
   ```bash
   touch database/database.sqlite
   ```

7. Run migrations and seed sample data
   ```bash
   php artisan migrate --seed
   ```

8. Build the frontend assets
   ```bash
   npm run build
   ```

9. Start the app
   ```bash
   php artisan serve
   ```

Then open:

```text
http://localhost:8000
```

## Optional admin account

If you want an admin login, set the following variables in `.env` before running the seed command:

```env
ADMIN_NAME=Administrator
ADMIN_EMAIL=admin@example.com
ADMIN_PASSWORD=SecretPassword123
```

Then run:

```bash
php artisan db:seed --class=AdminUserSeeder
```

## Development mode

For live frontend rebuilds and local development:

```bash
npm run dev
```

In a separate terminal, run:

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
public/         Public assets and entry point
resources/      Blade templates, CSS, JS
routes/          Web and admin routes
storage/        Logs and generated files
tests/          Automated tests
```

## License

This project is licensed under the MIT License.
