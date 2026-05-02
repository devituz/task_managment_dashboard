# Task Management Dashboard

A modern task management system built with Laravel 13 and PHP 8.4.

## 🚀 Quick Start

### Prerequisites
- Docker & Docker Compose

### Installation

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd task-management-dashboard
   ```

2. **Setup using scripts:**
   
   **For macOS:**
   ```bash
   chmod +x devit-macos.sh
   ./devit-macos.sh
   ```

   **For Linux:**
   ```bash
   chmod +x devit-linux.sh
   ./devit-linux.sh
   ```

3. **Access the application:**
   The application will be available at [http://localhost:9635](http://localhost:9635) (unless you changed `APP_PORT`).

---

## 🔐 Default Credentials

The database is pre-seeded with the following test accounts:

### 1. Super Admin (Full Access)
- **Email:** `admin@example.com`
- **Password:** `password`

### 2. Test Employee (Worker)
- **Email:** `employee@example.com`
- **Password:** `password`

---

## 🛠 Tech Stack
- **Framework:** Laravel 13
- **PHP:** 8.4
- **Database:** PostgreSQL 16
- **Cache/Queue:** Redis 7.2
- **Infrastructure:** Docker (Nginx, PHP-FPM)

## 📁 Common Commands

| Task | Command |
| :--- | :--- |
| Start Containers | `docker-compose up -d` |
| Rebuild Containers | `docker-compose up -d --build` |
| Stop Containers | `docker-compose down` |
| View Logs | `docker-compose logs -f app` |
| Run Migrations | `docker-compose exec app php artisan migrate` |
| Seed Database | `docker-compose exec app php artisan db:seed` |
| Access Container | `docker-compose exec app bash` |

---
🚀 Powered by [devit.uz](https://devit.uz)
