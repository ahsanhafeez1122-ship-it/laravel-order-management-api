# Laravel Order Management REST API

A backend REST API for managing products, customers, and orders — built with Laravel and MySQL.

## Features

- **Product management** — CRUD API with validation (unique SKU, price, stock)
- **Order creation** — accepts a customer and a list of items, automatically calculates the order total, and wraps the whole operation in a database transaction so a failure never leaves a partial order behind
- **Eloquent relationships** — `Order belongsTo Customer`, `Order hasMany OrderItems`, `OrderItem belongsTo Product`
- **JSON API** — consistent REST endpoints for all resources

## Tech Stack

- PHP 8.3 / Laravel 12
- MySQL (run via Docker for local development)
- Laravel Sanctum (token-based API authentication)

## API Endpoints

### Authentication (public)

| Method | Endpoint | Description |
|---|---|---|
| POST | `/api/register` | Create an account, returns a Sanctum token |
| POST | `/api/login` | Log in, returns a Sanctum token |
| POST | `/api/logout` | Revoke the current token (requires auth) |

All routes below require an `Authorization: Bearer <token>` header.

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/products` | List all products |
| POST | `/api/products` | Create a product |
| GET | `/api/products/{id}` | Get a single product |
| PUT/PATCH | `/api/products/{id}` | Update a product |
| DELETE | `/api/products/{id}` | Delete a product |
| GET | `/api/orders` | List all orders (with customer and items) |
| POST | `/api/orders` | Create an order with items |
| GET | `/api/orders/{id}` | Get a single order |
| PUT/PATCH | `/api/orders/{id}` | Update order status |
| DELETE | `/api/orders/{id}` | Delete an order |

### Example: creating an order

```bash
curl -X POST http://127.0.0.1:8000/api/orders \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "customer_id": 1,
    "items": [
      { "product_id": 1, "quantity": 3 }
    ]
  }'
```

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
# configure DB_* in .env, then:
php artisan migrate
php artisan serve
```
