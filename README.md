# Blackwell Membership Application

A membership management system built with Laravel 11, featuring member CRUD, a referral system, promotion rewards, and daily reward processing.

---

## Tech Stack

- **Backend:** PHP 8.2+, Laravel 11
- **Architecture:** Laravel Modules (nwidart/laravel-modules), Repository Design Pattern
- **Database:** SQLite (zero-config local dev) — swappable to MySQL/PostgreSQL
- **Frontend:** Bootstrap 5, Bootstrap Icons, DataTables
- **Other:** Laravel Scheduler, Polymorphic Relationships

---

## Features

- Member CRUD — create, view, edit, delete members
- Profile image & proof-of-address document uploads (polymorphic)
- Referral system — unique referral code per member, referral tree display
- Address management — multiple addresses with configurable types
- Promotion & reward system — 4 tiers (fixed + recurring)
- Promotion management — create and update promotion periods and tier settings
- Daily reward processing via Artisan scheduled command
- CSV export for member list and reward reports
- Admin dashboard with summary stats

---

## Requirements

- PHP >= 8.2
- Composer
- Node.js (optional, for asset compilation)

> Recommended: [Laravel Herd](https://herd.laravel.com/) for macOS — provides PHP and Composer out of the box.

---

## Setup

### 1. Clone the repository

```bash
git clone https://github.com/yuhang0627/blackwell-membership-application.git
cd blackwell-membership-application
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

Open `.env` and confirm the database settings. The default is SQLite (no extra setup needed):

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/your/project/database/database.sqlite
```

> Replace `/absolute/path/to/your/project` with the actual full path to the project folder.
>
> **macOS example:** `DB_DATABASE=/Users/yourname/blackwell-membership-application/database/database.sqlite`
>
> **Windows example:** `DB_DATABASE=C:/Projects/blackwell-membership-application/database/database.sqlite`

### 4. Create the SQLite database file

```bash
touch database/database.sqlite
```

### 5. Run migrations and seed sample data

```bash
php artisan migrate --seed
```

This will create all tables and seed:
- Address types (Home, Work, Billing, etc.)
- A sample promotion with 4 reward tiers
- Sample members with referral relationships

### 6. Create storage symlink

```bash
php artisan storage:link
```

### 7. Start the development server

```bash
php artisan serve
```

Visit [http://localhost:8000](http://localhost:8000)

---

## Key URLs

| Page | URL |
|------|-----|
| Dashboard | `/admin/dashboard` |
| Members List | `/members` |
| Add Member | `/members/create` |
| Promotion Setup | `/promotions` |
| Reward Report | `/rewards` |
| Export Members CSV | `/members/export/csv` |
| Export Rewards CSV | `/rewards/export` |

---

## Reward Processing

Rewards are processed automatically via Laravel Scheduler (runs daily at midnight):

```bash
# Run manually
php artisan rewards:process-daily

# Dry run (preview without saving)
php artisan rewards:process-daily --dry-run
```

To enable the scheduler, add this to your server's crontab:

```
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## Project Structure

```
Modules/
├── Admin/       — Dashboard controller & views
├── Member/      — Member CRUD, routes, requests, repository
└── Promotion/   — Reward report controller & views

app/
├── Models/      — Eloquent models (Member, Promotion, RewardTier, etc.)
├── Repositories/
│   └── Interfaces/   — Repository contracts
└── Providers/
    └── RepositoryServiceProvider.php   — Binds interfaces to implementations
```

---

## Module Overview

| Module | Responsibility |
|--------|---------------|
| `Admin` | Dashboard with member stats, recent members, recent rewards |
| `Member` | Full member CRUD, document uploads, referral tree, CSV export |
| `Promotion` | Reward report with filters, CSV export |
