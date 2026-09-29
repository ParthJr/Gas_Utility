# StayFlow — PG & Property Management SaaS (InfinityFree Edition)

StayFlow is a modern, responsive, and multi-tenant PG and Property Management SaaS platform built with pure PHP 8.x, MySQL/MariaDB, Vanilla JavaScript, and Bootstrap 5.

---

## 📦 Package Contents

- **`htdocs/`**: All production-ready web application files to be uploaded to the server root.
- **`database/`**: 
  - `schema.sql`: Complete production database table structure (48 tables with foreign keys and indexes).
  - `seed.sql`: Safe default seed data including RBAC roles, permission matrix, default plans, and feature sets.
  - `backup-template.md`: Step-by-step instructions for backing up and restoring via phpMyAdmin.
- **`deployment/`**:
  - `INFINITYFREE.md`: Detailed step-by-step deployment guide for InfinityFree and cPanel shared hosting.
  - `health-check.php`: Web-accessible diagnostics tool for testing server environment and database connectivity.
  - `config.example.php`: Production configuration template with placeholders.
- **`DEPLOYMENT-AUDIT.md`**: Complete codebase audit, table inventory, dependency analysis, and security verification.

---

## 🚀 Quick Start Deployment

1. **Upload Files**: Upload the contents of the `htdocs/` folder into your InfinityFree `htdocs/` directory.
2. **Import Database**: In phpMyAdmin, import `database/schema.sql`, followed by `database/seed.sql`.
3. **Run Installer or Configure**:
   - Navigate to `https://YOUR-DOMAIN.com/install/` and follow the 2-step setup wizard, **OR**
   - Copy `config.example.php` to `config/config.php` and fill in your MySQL credentials and `APP_URL`.
4. **Sign In**:
   - Default Administrator: `admin@stayflow.test` / `admin123` (or the credentials you configured during installation).
5. **Verify Diagnostics**:
   - Check `https://YOUR-DOMAIN.com/deployment/health-check.php?key=YOUR_CRON_KEY` to confirm all systems are green.

---

## 🔒 Security Notes
- HTTPS is automatically enforced in `.htaccess`.
- Direct script execution in `/uploads/` is strictly disabled.
- `.env`, `.sql`, `database/`, and log files are blocked from direct web access.
- `install/installed.lock` locks the installation wizard after initial setup.
