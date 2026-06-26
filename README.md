# OMS — Order Management System (Laravel)

A Laravel 13 PHP application for managing customers, products, inventory, and orders through a web dashboard and REST API. This is the Laravel counterpart to the Symfony OMS in `/symfony`.

## Features

- **Customers** — create and manage customer records with shipping addresses
- **Products** — SKU-based catalog with pricing and stock tracking
- **Orders** — draft → pending → confirmed → processing → shipped → delivered lifecycle
- **Inventory** — stock is reserved on order confirmation and released on cancellation
- **Dashboard** — overview of order counts, recent activity, and low-stock alerts
- **REST API** — JSON endpoints under `/api` for integration

## Requirements

- PHP 8.2+
- Composer
- SQLite (default), or MySQL/PostgreSQL

## Quick Start

```bash
cd /Users/mpc/dev/laravel

# Install dependencies (already done if cloned)
composer install

# Run migrations
php artisan migrate

# Load sample data
php artisan app:seed

# Start the dev server
php artisan serve
```

Open [http://localhost:8000](http://localhost:8000) for the dashboard.

## Order Status Flow

```
draft → pending → confirmed → processing → shipped → delivered
  ↓         ↓          ↓            ↓          ↓
 cancelled (from any non-terminal status except delivered)
```

Stock is decremented when an order moves to **confirmed** and restored if cancelled afterward.

## REST API

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/dashboard` | Dashboard stats |
| GET/POST | `/api/customers` | List / create customers |
| GET/PUT/PATCH | `/api/customers/{id}` | Show / update customer |
| GET/POST | `/api/products` | List / create products |
| GET/PUT/PATCH | `/api/products/{id}` | Show / update product |
| GET/POST | `/api/orders` | List / create orders |
| GET | `/api/orders/{id}` | Order details |
| POST | `/api/orders/{id}/items` | Add line item |
| DELETE | `/api/orders/{id}/items/{itemId}` | Remove line item |
| POST | `/api/orders/{id}/submit` | Submit draft order |
| PATCH | `/api/orders/{id}/status` | Update status |
| PATCH | `/api/orders/{id}/pricing` | Set tax & shipping |

### Example: Create an order via API

```bash
curl -X POST http://localhost:8000/api/orders \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -d '{"customerId": 1, "notes": "API order"}'

curl -X POST http://localhost:8000/api/orders/1/items \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -d '{"productId": 1, "quantity": 2}'

curl -X POST http://localhost:8000/api/orders/1/submit \
  -H 'Accept: application/json'

curl -X PATCH http://localhost:8000/api/orders/1/status \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -d '{"status": "confirmed"}'
```

## Database Configuration

SQLite is configured by default in `.env`. For PostgreSQL or MySQL, update `DATABASE_URL` or DB_* vars in `.env`, then run:

```bash
php artisan migrate
php artisan app:seed
```

## Project Structure

```
app/
├── Enums/            OrderStatus
├── Http/Controllers/ Web + Api controllers
├── Models/           Eloquent models
├── Services/         OrderService, OrderNumberGenerator
└── Support/          EntityNormalizer (API responses)
database/
├── migrations/       OMS schema
└── seeders/          OmsSeeder
resources/views/      Blade templates
routes/
├── web.php           Web UI routes
└── api.php           REST API routes
```

## License

MIT
