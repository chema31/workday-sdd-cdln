# fichaccom

**Application type**: Laravel Admin Panel (monolithic)
**Framework**: Laravel 11.x | PHP 8.2+
**Admin panel**: Filament 3.x (`/admin`)
**Employee frontend**: Blade + Tailwind CSS + Alpine.js (`/login`, `/dashboard`)
**Database**: MySQL (local) / SQLite in-memory (tests)
**Testing**: PHPUnit (`php artisan test`)

## Setup
1. `composer install`
2. `npm install && npm run build`
3. `cp .env.example .env && php artisan key:generate`
4. Configure `.env` (DB credentials + ADMIN_* variables)
5. `php artisan migrate --seed`

## Environment variables
| Variable | Description |
|---|---|
| `ADMIN_EMAIL` | Email of the seeded administrator account |
| `ADMIN_PASSWORD` | Password for the administrator account |
| `ADMIN_NAME` | Display name for the administrator |

## Development
- `php artisan serve` — start the dev server
- `npm run dev` — start Vite asset watcher
- `php artisan test` — run the test suite

## Design System
Brand colour tokens registered in `tailwind.config.js`:
| Token | Hex | Usage |
|---|---|---|
| `accom-teal` | `#2BBFB3` | Primary actions, links |
| `accom-teal-light` | `#E8F9F8` | Backgrounds, hover states |
| `accom-pink` | `#E8195A` | Destructive actions, alerts |
