---
id: US-001
title: User Management & Application Bootstrap
type: feature
status: draft
created: 2026-05-11
branch: feature/20260511_user_management_and_application_bootstrap
---

# US-001 — User Management & Application Bootstrap

## Description

As the **system administrator**, I need a complete user management foundation
so that employees can be registered, managed, and given access to clock in
and out of their work shifts.

As an **employee**, I need a secure login portal where I can see my attendance
records for the current month and a button to clock in or out of my shift.

This is the foundational story of `fichaccom`, a monolithic Laravel application
for employee time tracking. The actual clocking logic is NOT implemented here —
this story establishes the data model, admin panel, and employee portal scaffolding
on which US-002 will build.

## Business Value

- Enables management to maintain a full employee roster from a single admin interface.
- Provides each employee with a secure, role-gated login that prevents cross-user
  data access.
- Creates the `users` and `clock_records` schema that all future stories depend on.

---

## Acceptance Criteria

### Admin Panel (Filament — /admin)

- **AC-01**: `/admin` requires authentication. Unauthenticated users are redirected
  to the Filament login page.
- **AC-02**: Only users with `role = admin` can access `/admin`. Employees who
  attempt to access it are rejected with a redirect to `/login`.
- **AC-03**: A seeded admin account exists, created from `.env` values
  (`ADMIN_EMAIL`, `ADMIN_PASSWORD`, `ADMIN_NAME`). The seeder is idempotent
  (`firstOrCreate`). No credentials are hardcoded in migrations or seeders.
- **AC-04**: The **Employee List** displays: Name, Email, Status (Active/Inactive),
  Created At. It is searchable by name and email. It can be filtered by `is_active`.
  The admin account (`ADMIN_EMAIL`) is **excluded** from this list.
- **AC-05**: The admin can **create** an employee with: Full Name, Email, Password
  (with confirmation), Is Active toggle. Email must be unique. The password must
  be at least 8 characters long and contain at least one number.
- **AC-06**: The admin can **edit** an employee. The password field is optional —
  leaving it blank preserves the current password.
- **AC-07**: The admin can **delete** an employee (hard delete) after a confirmation
  modal. Attempting to delete the account identified by `ADMIN_EMAIL` is blocked
  with an error notification. No soft-delete is required in this version.

### Employee Authentication (Frontend — /login)

- **AC-08**: `/login` is the only public route. All other routes require authentication.
- **AC-09**: Successful login for an employee redirects to `/dashboard`.
- **AC-10**: An admin logging in through the frontend portal is redirected to `/admin`.
- **AC-11**: An inactive employee (`is_active = false`) is rejected at login with
  the message: `"Your account has been deactivated. Contact the administrator."`
  No session is created.
- **AC-12**: Unauthenticated users accessing `/dashboard` are redirected to `/login`.
- **AC-13**: `/logout` destroys the session and redirects to `/login`.

### Employee Dashboard (/dashboard)

- **AC-14**: The dashboard displays a **time-sensitive greeting** with an **initials avatar**:
  - Greeting text changes based on the server's current hour: `"¡Buenos días, [Name]!"` (06:00–12:00), `"¡Buenas tardes, [Name]!"` (12:00–20:00), `"¡Buenas noches, [Name]!"` (20:00–06:00).
  - Avatar: a `40×40px` circle filled with `accom-teal` (`#2BBFB3`), containing the employee's initials (first letter of first name + first letter of last name) in white, `text-sm font-semibold`.
  - Avatar and greeting are horizontally aligned and vertically centred.
  - Greeting resolution logic lives in `App\Helpers\TimeGreetingHelper` — no logic in the controller or Blade view.
- **AC-15**: A table lists all `clock_records` for the **current calendar month**
  (from the 1st of the current month to today, inclusive), with columns: Date,
  Clock-in Time, Clock-out Time, Total Hours Worked. Dates are displayed as
  `DD/MM/YYYY`; times as `HH:mm`. If `clocked_out_at` is null, Total Hours is
  displayed as `—`. Records are paginated at **50 per page**.
- **AC-16**: If there is no open record for today (no row with today's date and
  `clocked_out_at = null`), the action button reads **"Fichar entrada"**.
- **AC-17**: If there is an open record for today (a row with today's date and
  `clocked_out_at = null`), the action button reads **"Fichar salida"**.
- **AC-18**: The button is a placeholder — clicking it does nothing in this story.
  The POST handler will be wired in US-002.
- **AC-19**: Employees can only see their own `clock_records`. No cross-user data
  leakage is possible (enforced by both an Eloquent query scope AND a Laravel Policy).
- **AC-20**: When the employee has no `clock_records` for the current month, the
  table area displays the message: `"No records for this month yet."` instead of
  an empty table.

### Navigation & Branding

- **AC-21**: Every authenticated frontend page includes a persistent **top navigation bar**
  (`<x-navbar>` Blade component) with:
  - Left: Accom logo as an inline SVG using brand teal (`#2BBFB3`). No external image file.
  - Right: employee's full name (`text-gray-700`) + `"Cerrar sesión"` ghost-button link to `/logout`.
  - Style: white background, `border-b border-gray-200`, height `h-16`, full-width.
  - The login page (`/login`) reuses the same layout but shows the logo centred only — no user info.

### Clock Widget Card

- **AC-22**: The clock action area is rendered as a **card widget** (`<x-clock-widget>` Blade component)
  visually inspired by Factorial's "Fichaje" card:
  - Card wrapper: `bg-white rounded-xl shadow-sm border border-gray-100 p-6`.
  - Header row: `"Fichaje"` label in `text-sm font-medium text-gray-500` (left) + `›` arrow icon (right).
  - Status row:
    - No active shift: status dot `●` in `text-gray-300`, label `"Sin fichar"` in `text-gray-400`.
    - Active shift: status dot `●` in `text-accom-teal animate-pulse`, label `"Fichado"` in `text-accom-teal font-semibold`.
  - Action button:
    - Clock-in state: `"Entrada"` — `bg-accom-teal text-white rounded-lg px-5 py-2.5 font-medium hover:bg-teal-600`.
    - Clock-out state: `"Salida"` (with `■` prefix icon) — `bg-accom-pink text-white rounded-lg px-5 py-2.5 font-medium hover:bg-pink-700`.
  - The circular arc timer and elapsed-time counter are **explicitly out of scope for US-001** and will be implemented in US-002.

### Dashboard Layout

- **AC-23**: The dashboard content area follows this explicit layout structure:
  ```
  ┌──────────────────────────────────────────────────────────┐
  │  NAVBAR: [Accom Logo SVG]      [Employee Name] [Cerrar]  │
  ├──────────────────────────────────────────────────────────┤
  │  CONTENT (max-w-5xl mx-auto px-6 py-8)                   │
  │                                                           │
  │  [●] ¡Buenos tardes, [Initials Avatar] [Name]!           │
  │                                                           │
  │  ┌───────────────────────────────┐                       │
  │  │  Fichaje widget card (AC-22) │                       │
  │  └───────────────────────────────┘                       │
  │                                                           │
  │  Registros del mes actual                                 │
  │  ┌──────────────────────────────────────────────────┐   │
  │  │  Fecha │ Entrada │ Salida │ Total horas           │   │
  │  └──────────────────────────────────────────────────┘   │
  └──────────────────────────────────────────────────────────┘
  ```
  Page background: `bg-gray-50`. Table headers in `text-gray-500 text-sm uppercase`.

### Login Page

- **AC-24**: The login page (`/login`) is styled as follows:
  - Accom logo SVG centred at the top of the card.
  - Card: `max-w-md mx-auto mt-24 bg-white rounded-xl shadow-md p-8`.
  - `"Iniciar sesión"` submit button: `bg-accom-teal text-white w-full rounded-lg py-2.5 font-medium`.
  - Validation and inactive-user error messages: `text-accom-pink text-sm`.

---

## Technical Considerations

### Design System (Accom Corporate Branding)

| Token | Hex | Usage |
|---|---|---|
| `accom-teal` | `#2BBFB3` | Primary buttons, active states, navbar accent, avatar background |
| `accom-teal-light` | `#E8F9F8` | Card backgrounds, hover fills, tag backgrounds |
| `accom-pink` | `#E8195A` | Clock-out button, error messages, destructive actions |
| `gray-50` | `#F9FAFB` | Page background |
| `gray-900` | `#111827` | Primary body text |

Register as custom Tailwind tokens in `tailwind.config.js`:
```js
theme: {
  extend: {
    colors: {
      accom: {
        teal:       '#2BBFB3',
        'teal-light': '#E8F9F8',
        pink:       '#E8195A',
      },
    },
  },
},
```

UI-facing text (labels, buttons, messages) is in **Spanish**. All code identifiers remain in **English**.

### Stack

| Concern | Choice |
|---|---|
| Framework | Laravel 11.x, PHP 8.2+ |
| Admin Panel | Filament 3.x |
| Frontend Auth Scaffold | Laravel Breeze (Blade stack, unused routes disabled) |
| Frontend Views | Blade + Tailwind CSS + Alpine.js |
| Auth | Laravel session auth (built-in), separate from Filament's panel auth |
| Database | MySQL (tests: SQLite in-memory via `RefreshDatabase`) |
| Testing | PHPUnit via `php artisan test` |

### Architecture

- Follows **MVC + Service Layer** as defined in `rules/backend-standards.mdc`.
- Business logic for user creation and updates lives in `App\Services\User\UserService`.
  Filament Resources and controllers delegate to the service — no business logic
  inside them.
- `role` is implemented as a PHP 8.1 enum: `App\Enums\UserRole` (`admin`, `employee`),
  cast on the `User` model via `$casts`.
- `User::canAccessPanel(Panel $panel)` (Filament contract) enforces `role = admin`
  for `/admin`.
- The Filament `EmployeeResource` query excludes the admin account by filtering out
  `ADMIN_EMAIL` via a modified `getEloquentQuery()`.
- `App\Policies\ClockRecordPolicy` enforces that employees can only query and view
  their own records.
- Laravel Breeze register route and profile routes are disabled — only `login`,
  `logout`, and `dashboard` are kept.

### Database Schema

**`users`** (extends the default Laravel migration — add two columns):

| Column | Type | Default | Notes |
|---|---|---|---|
| `role` | enum('admin','employee') | 'employee' | Cast to `UserRole` enum |
| `is_active` | boolean | true | False blocks login |

**`clock_records`** (stub — schema only, no service logic this story):

| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | Auto-increment |
| `user_id` | bigint FK → users.id | CASCADE on delete |
| `clocked_in_at` | timestamp | Required |
| `clocked_out_at` | timestamp nullable | null = open shift |
| `created_at` / `updated_at` | timestamps | |

### Environment Variables

```dotenv
# Required — document in .env.example with placeholder values
ADMIN_EMAIL=desarrollo@we-accom.com
ADMIN_PASSWORD=
ADMIN_NAME=Administrador
```

---

## Non-Functional Requirements

- `php artisan test` must pass with all 11 feature tests green before story close.
- No CSRF exemptions on any POST route.
- No debug code (`dd()`, `dump()`, `var_dump()`) committed.
- `.env.example` updated with all new environment variables.
- No credentials hardcoded anywhere in source code.
- All code, comments, test names, and DB identifiers in English.
- After implementing Blade components, run `npm run dev` and visually verify the login page and dashboard render correctly before marking UI specs complete.
- No custom CSS files — Tailwind utility classes only (exception: SVG arc animation in US-002).
- No external image assets for the logo — use an inline SVG built from brand hex values.

---

## Out of Scope (US-002)

- Clock-in / clock-out write logic (POST handler, `ClockRecord` creation/update).
- Password reset / forgot-password flow.
- Email notifications on account creation.
- Per-employee timezone configuration.

---

## Tests

| # | Description | Type |
|---|---|---|
| 1 | Admin seeder creates user from `.env` values | Feature |
| 2 | Admin can log in to `/admin` | Feature |
| 3 | Employee cannot access `/admin` | Feature |
| 4 | Inactive employee is rejected at login | Feature |
| 5 | Admin can create an employee via Filament | Feature |
| 6 | Admin can edit an employee via Filament | Feature |
| 7 | Admin cannot delete the seeded admin account | Feature |
| 8 | Employee sees only their own clock records on the dashboard | Feature |
| 9 | Dashboard button shows "Fichar entrada" when no open record exists today | Feature |
| 10 | Dashboard button shows "Fichar salida" when an open record exists today | Feature |
| 11 | Admin cannot create employee with password shorter than 8 chars or without a number | Feature |

---

## Implementation Checklist

- [x] **Spec 0**: Accom brand Tailwind config — register `accom-teal`, `accom-teal-light`,
  `accom-pink` tokens in `tailwind.config.js`; confirm `npm run dev` compiles without errors.
- [x] **Spec 1**: Laravel 11 bootstrap — fresh install, Filament 3, Laravel Breeze
  (Blade stack, unused routes disabled), `.env.example` with admin vars.
- [ ] **Spec 2**: Database migrations — `add_role_and_is_active_to_users_table` +
  `create_clock_records_table`.
- [ ] **Spec 3**: `UserRole` enum + `User` model update — casts, query scopes,
  `canAccessPanel()`, factory states (`admin`, `employee`, `inactive`).
- [ ] **Spec 4**: `ClockRecord` model + factory — relationships, fillable, casts.
- [ ] **Spec 5**: `AdminSeeder` — idempotent seeder reading from `.env`.
- [ ] **Spec 6**: `UserService` — `createEmployee()`, `updateEmployee()` (optional
  password change), `isProtectedAdmin()` guard method.
- [ ] **Spec 7**: Filament `EmployeeResource` — list (admin excluded), create, edit,
  delete (with admin-deletion guard).
- [ ] **Spec 8**: Employee authentication — login controller with inactive-user guard
  and role-based post-login redirect; logout; remove unused Breeze routes.
- [ ] **Spec 9**: Employee dashboard — `DashboardController`, Blade view, current-month
  records table, conditional clock-in/out button placeholder.
- [ ] **Spec 10**: `ClockRecordPolicy` — ensures employees can only view their own records.
- [ ] **Spec 11**: Feature tests — all 11 tests green.
- [ ] **Spec 12**: Blade UI components — `<x-navbar>` (logo + logout), `<x-clock-widget>`
  (card with status dot and action button), `TimeGreetingHelper`, login page layout with
  Accom branding. Visual check: `npm run dev` confirms login and dashboard render correctly.
- [ ] Functional Tests (Playwright)