# Inventory Management System

A comprehensive Laravel-based inventory management system with dynamic pricing, transaction processing, and audit logging capabilities.

## Features

- **Dynamic Pricing Engine**: Time-based and quantity-based pricing rules
- **Real-time Inventory Management**: Track stock levels across multiple locations
- **Transaction Processing**: Atomic sale transactions with inventory updates
- **Audit Logging**: Complete audit trail for all system operations
- **RESTful API**: Clean API endpoints for all operations
- **Concurrent Access Handling**: Database locks to prevent race conditions

## Installation & Setup

### Prerequisites

- PHP 8.1+
- Composer
- MySQL 5.7+ or PostgreSQL 10+
- Redis (for caching)
- XAMPP/WAMP (for local development)

### Installation Steps

1. **Clone the repository:**

```bash
git clone https://github.com/UsmanSaleem707827/inventory-managment-task.git
cd Inventory-Managment-Task
```

2. **Install dependencies:**

```bash
composer install
```

3. **Environment Configuration:**

```bash
cp .env.example .env
```

4. **Configure your database in `.env`:**

```env
APP_NAME="Inventory Management System"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventory_db
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

5. **Generate application key:**

```bash
php artisan key:generate
```

6. **Create database:**
   Create a MySQL database named `inventory_db` (or your preferred name as configured in .env)
7. **Run migrations:**

```bash
php artisan migrate
```

8. **Seed sample data:**

```bash
php artisan db:seed --class=ProductSeeder
```

9. **Start the development server:**

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## Database Schema

### Tables Overview

- **products**: Core product information (SKU, name, description, base price)
- **inventories**: Stock tracking (quantity, location, cost, lot numbers)
- **pricing_rules**: Dynamic pricing configurations (time/quantity-based)
- **transactions**: Sales and restock transactions
- **audit_logs**: Complete audit trail

### Key Relationships

- Product → hasOne → Inventory
- Product → hasMany → PricingRules
- Product → hasMany → Transactions

## API Endpoints

### Inventory Management

#### Get All Inventory Items

```http
GET /api/inventory?location=Warehouse%201&page=1
```

**Response:**

```json
{
    "message": "Inventory items retrieved successfully.",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "product_id": 1,
                "quantity": 100,
                "location": "Warehouse 1",
                "cost": "150.00",
                "lot_number": null,
                "product": {
                    "id": 1,
                    "sku": "SKU123",
                    "name": "Laptop",
                    "description": "Test product.",
                    "base_price": "200.00"
                }
            }
        ],
        "per_page": 10,
        "total": 1
    }
}
```

#### Get Single Inventory Item

```http
GET /api/inventory/{id}
```

#### Update Inventory Quantity

```http
PUT /api/inventory/{id}
Content-Type: application/json

{
    "quantity": 150
}
```

### Transaction Processing

#### Process a Sale Transaction

```http
POST /api/transaction
Content-Type: application/json

{
    "product_id": 1,
    "quantity": 5
}
```

**Response (Success):**

```json
{
    "message": "Transaction completed successfully."
}
```

**Response (Error):**

```json
{
    "message": "Transaction failed: Insufficient stock"
}
```

## Project Structure

```
app/
├── Http/Controllers/Api/
│   ├── InventoryController.php    # Inventory management endpoints
│   └── TransactionController.php  # Transaction processing
├── Models/
│   ├── Product.php               # Product model with relationships
│   ├── Inventory.php             # Inventory tracking model
│   ├── Transaction.php           # Transaction records
│   ├── PricingRule.php          # Dynamic pricing rules
│   └── AuditLog.php             # Audit trail
database/
├── migrations/                   # Database schema definitions
└── seeders/
    └── ProductSeeder.php        # Sample data creation
routes/
└── api.php                      # API route definitions
```
