# ShopEasy — E-Commerce Platform

A web-based e-commerce platform built for Rwanda, connecting small businesses with customers.
Built with PHP 8.2, MySQL, and Apache. Supports product browsing, cart, checkout, order tracking,
and a full admin dashboard.

---

## Table of Contents

- [Features](#features)
- [Project Structure](#project-structure)
- [Requirements](#requirements)
- [Option A: Run with XAMPP (local development)](#option-a-run-with-xampp-local-development)
- [Option B: Run with Docker (recommended)](#option-b-run-with-docker-recommended)
- [Default Login Credentials](#default-login-credentials)
- [Environment Configuration](#environment-configuration)
- [Database Schema](#database-schema)
- [Version Control Workflow](#version-control-workflow)

---

## Features

- Customer registration, login, and session management
- Product browsing with category filter and search
- Shopping cart (add, view, checkout)
- Order placement with simulated payment (Cash on Delivery, Card, PayPal)
- Order tracking per customer
- Admin dashboard: manage products, orders, users, payments
- Sales reports: daily sales, low stock, top products, top customers
- Activity logging (login/logout audit trail)
- Contact form

---

## Project Structure

```
Ecommerce/
├── admin/              Admin pages (require admin role)
├── api/                JSON API endpoints (called by JavaScript)
├── assets/             CSS, JS, product images
├── customer/           Customer pages (require customer role)
├── docker/
│   └── mysql/
│       └── init.sql    Auto-runs on first Docker MySQL startup
├── includes/
│   ├── config.php          XAMPP database config (not in Git)
│   ├── config.docker.php   Docker database config (not in Git)
│   ├── database.php        Singleton PDO connection
│   ├── session.php         Session bootstrap
│   ├── auth.php            Login, register, logout, role guards
│   ├── functions.php       All reusable business logic
│   ├── header.php          Shared HTML header + nav
│   ├── footer.php          Shared HTML footer
│   └── partials/
│       └── product_grid.php  Reusable product filter + grid
├── .dockerignore
├── .gitignore
├── .htaccess
├── database.sql        Full schema + sample data
├── docker-compose.yml
├── Dockerfile
├── index.php
├── login.php
├── register.php
├── logout.php
├── products.php
├── about.php
└── contact.php
```

---

## Requirements

### For XAMPP
- XAMPP with PHP 8.0+ and MySQL 5.7+
- A web browser

### For Docker
- Docker Desktop (Windows/Mac) or Docker Engine (Linux)
- Docker Compose v2+

---

## Option A: Run with XAMPP (local development)

**Step 1 — Copy the project**
```
Place the Ecommerce/ folder inside:
C:\xampp\htdocs\Ecommerce\
```

**Step 2 — Create the config file**

Create `includes/config.php` with this content:
```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'ecommerce_platform');
define('DB_USER', 'root');
define('DB_PASS', '');
define('APP_NAME', 'E-Commerce Platform');
define('APP_URL', '/Ecommerce');
?>
```

**Step 3 — Import the database**
1. Start XAMPP (Apache + MySQL)
2. Open http://localhost/phpmyadmin
3. Click "Import" and select `database.sql`

**Step 4 — Open the app**
```
http://localhost/Ecommerce/
```

---

## Option B: Run with Docker (recommended)

Docker runs the entire app (PHP + Apache + MySQL) in isolated containers.
No XAMPP needed.

**Step 1 — Install Docker Desktop**

Download from: https://www.docker.com/products/docker-desktop

**Step 2 — Create the Docker config file**

Create `includes/config.php` with this content (Docker version):
```php
<?php
define('DB_HOST', 'db');
define('DB_NAME', 'ecommerce_platform');
define('DB_USER', 'shopeasy_user');
define('DB_PASS', 'shopeasy_pass');
define('APP_NAME', 'E-Commerce Platform');
define('APP_URL', '');
?>
```

> Note: `DB_HOST` is `db` — the name of the MySQL container on the Docker network.
> `APP_URL` is empty because Docker serves from root (`http://localhost:8080/`).

**Step 3 — Build and start the containers**

Open a terminal in the `Ecommerce/` folder and run:
```bash
docker-compose up -d --build
```

This will:
- Build the PHP+Apache image from the Dockerfile
- Pull the MySQL 8.0 image
- Start both containers
- Auto-create the database and all tables from `docker/mysql/init.sql`

**Step 4 — Open the app**
```
http://localhost:8080/
```

**Step 5 — Useful Docker commands**

```bash
# View running containers
docker-compose ps

# View live logs
docker-compose logs -f

# Stop all containers
docker-compose down

# Stop and delete all data (fresh start)
docker-compose down -v

# Rebuild after code changes
docker-compose up -d --build
```

---

## Default Login Credentials

| Role     | Email                 | Password  |
|----------|-----------------------|-----------|
| Admin    | admin@example.com     | Admin123  |
| Customer | alice@example.com     | Admin123  |
| Customer | bob@example.com       | Admin123  |

---

## Environment Configuration

| Setting    | XAMPP value       | Docker value      |
|------------|-------------------|-------------------|
| DB_HOST    | localhost         | db                |
| DB_USER    | root              | shopeasy_user     |
| DB_PASS    | (empty)           | shopeasy_pass     |
| DB_NAME    | ecommerce_platform| ecommerce_platform|
| APP_URL    | /Ecommerce        | (empty string)    |
| App port   | :80               | :8080             |
| MySQL port | :3306             | :3307             |

---

## Database Schema

The full schema is in `database.sql`. Key tables:

| Table           | Purpose                                      |
|-----------------|----------------------------------------------|
| Users           | All accounts (customers + admins)            |
| Customers       | Customer profile extension                   |
| Admins          | Admin profile extension                      |
| Categories      | Product categories                           |
| Products        | Product catalog                              |
| ShoppingCart    | Active cart items per customer               |
| Orders          | Placed orders                                |
| OrderItems      | Line items per order                         |
| Payments        | Payment records per order                    |
| ActivityLogs    | Login/logout audit trail                     |
| ArchivedUsers   | Soft-deleted user backups                    |
| ContactMessages | Contact form submissions                     |

---

## Version Control Workflow

This project uses **Git** for version control.

### Initial setup (first time only)
```bash
git init
git add .
git commit -m "Initial commit: ShopEasy e-commerce platform"
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/shopeasy.git
git push -u origin main
```

### Daily workflow
```bash
# Check what changed
git status

# Stage your changes
git add .

# Commit with a clear message
git commit -m "feat: add product search filter"

# Push to remote
git push
```

### Branching strategy
```bash
# Create a feature branch
git checkout -b feature/payment-gateway

# Work on your feature, then merge back
git checkout main
git merge feature/payment-gateway
```

### Recommended commit message format
```
feat: add new feature
fix: fix a bug
refactor: improve code without changing behavior
docs: update documentation
style: formatting changes only
```
