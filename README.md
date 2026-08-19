# AgroDrop 🌱

AgroDrop is a complete PHP and MySQL agricultural marketplace. It gives customers a simple, mobile-friendly way to buy fresh products directly from farmers, while farmers can operate their own product inventory and fulfil orders.

## What is included

### Customer marketplace

- Professional home page and product catalogue
- Search and category browsing
- Product details with farm information and live stock
- Session-based shopping cart
- Checkout with Cash on Delivery or bKash selection
- Order history and delivery-status tracking
- Registration and secure password login

### Farmer centre

- Farmer registration and role-based login
- Farmer dashboard: products, inventory, sales, received orders
- Add, edit and delete only the farmer's own products
- Customer order list and order-status updates

### Built-in safeguards

- Passwords are hashed using `password_hash()`
- Customer/farmer pages require login and role checks
- Product stock is checked and reduced inside a database transaction at checkout
- SQL writes use prepared statements

## Installation

1. Start Apache and MySQL in XAMPP, Laragon, or another PHP stack.
2. Create/import the database by importing [agro_drop.sql](agro_drop.sql) in phpMyAdmin.

   > The SQL file resets the AgroDrop tables, so use it only for a new local installation.

3. Set the correct MySQL details in [config/database.php](config/database.php). The supplied configuration uses `root` / `root`.
4. Place the project in your web-server root as `agro_drop` and visit:

   ```text
   http://localhost/agro_drop/
   ```

## Demo accounts

| Role | Email | Password |
|---|---|---|
| Customer | `customer@agrodrop.test` | `password123` |
| Farmer | `farmer@agrodrop.test` | `password123` |

## Project pages

| Page | Purpose |
|---|---|
| `index.php` | Customer-facing landing page |
| `shop.php` | Searchable product marketplace |
| `cart.php` / `checkout.php` | Purchase flow |
| `my-orders.php` | Customer orders |
| `farmer/dashboard.php` | Farmer dashboard |
| `farmer/products.php` | Product and stock management |
| `farmer/orders.php` | Farmer order processing |

## Technology

- PHP 8+
- MySQL 8+ / MariaDB
- HTML5 and responsive CSS
- No framework or external setup required
