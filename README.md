# CRM System - Laravel verze

Komplexní CRM systém postavený na Laravel frameworku.

## 🚀 Rychlý start

```bash
# 1. Instalace závislostí
composer install
npm install

# 2. Konfigurace prostředí
cp .env.example .env
php artisan key:generate

# 3. Nastavení databáze v .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crm
DB_USERNAME=root
DB_PASSWORD=

# 4. Spuštění migrací
php artisan migrate
php artisan db:seed

# 5. Storage link
php artisan storage:link

# 6. Build assets
npm run build

# 7. Spuštění serveru
php artisan serve
npm run dev
```

## 📝 Přihlašovací údaje

Po seed:
- **Email:** admin@example.com
- **Heslo:** password
- **Role:** ADMIN

## ✨ Funkce

- ✅ Správa klientů (CRUD)
- ✅ Správa projektů (CRUD)
- ✅ Správa úkolů (CRUD + List/Kanban view)
- ✅ Komentáře s @mentions a přílohami
- ✅ Time tracking (aktivní trackování + záznamy)
- ✅ Notifikace (Laravel Notifications)
- ✅ Správa hesel (šifrované)
- ✅ Dashboard se statistikami
- ✅ Tmavý/světlý režim
- ✅ Autentizace (Laravel Breeze)

## 📚 Dokumentace

Viz `QUICK_START.md` nebo `INSTALLATION.md` pro detailní instrukce.

## 🛠️ Technologie

- **Laravel 12** - PHP framework
- **Livewire 3** - Interaktivní komponenty
- **Tailwind CSS** - Styling
- **Alpine.js** - Frontend interaktivita
- **MySQL/MariaDB** - Databáze
- **PHP 8.2+** - Backend jazyk
