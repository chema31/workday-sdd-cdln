# Spec 14: Manual Test Plan (US-001)

## Metadata
- **User Story**: [US-001](../features/20260511-user-management-and-application-bootstrap.md)
- **Type**: Backend (Laravel Admin Panel)
- **Estimated effort**: S (to write) / ~30 min (to execute)
- **Branch**: feature/20260807_us001_sail_and_manual_tests
- **Depends on**: Spec 13 (the environment must be up)

## Overview

Step-by-step script for a human to verify US-001 end to end in a browser. The automated suites prove behaviour; this plan covers what they cannot: visual layout, exact spacing and colour, and the AC-24 / AC-23 styling criteria that no assertion checks.

Every scenario maps to acceptance criteria of US-001. Execute them in order — later scenarios rely on data created by earlier ones.

## How to execute this plan

1. Bring the environment up (Spec 13): `sail up -d`, then `sail npm run build`.
2. Reset to a known state:
   ```bash
   sail artisan migrate:fresh --seed --seeder=E2ESeeder
   ```
   This creates three accounts:

   | Role | Email | Password | State |
   |---|---|---|---|
   | Admin | value of `ADMIN_EMAIL` (`desarrollo@we-accom.com`) | value of `ADMIN_PASSWORD` (`Admin1234`) | active |
   | Employee | `employee@test.com` | `Employee1` | active |
   | Employee | `inactive@test.com` | `Employee1` | **inactive** |

3. Open `http://localhost:8000` in a **private/incognito window** — a stale session invalidates the redirect scenarios.
4. Record the result of each numbered step in the results table at the end.
5. Any deviation is a defect: write down what you saw, do not "interpret" it as acceptable.

> **Clock records.** The seeder creates no attendance rows, so scenarios 5 and 6 need data. Create it with:
> ```bash
> # An open shift for today (drives AC-17)
> sail artisan tinker --execute="App\Models\ClockRecord::create(['user_id' => App\Models\User::where('email','employee@test.com')->value('id'), 'clocked_in_at' => now()->setTime(9,0), 'clocked_out_at' => null]);"
> ```

---

## Scenario 1 — Public access and route protection (AC-08, AC-12, AC-01)

| # | Action | Expected result |
|---|---|---|
| 1.1 | Visit `http://localhost:8000/dashboard` while logged out | Redirected to `/login`. The dashboard is never rendered, not even briefly. |
| 1.2 | Visit `http://localhost:8000/admin` while logged out | Redirected to the Filament login page. |
| 1.3 | Visit `http://localhost:8000/login` | Page renders with status 200. |

---

## Scenario 2 — Login page appearance (AC-24, AC-21)

Stay on `/login`.

| # | Check | Expected result |
|---|---|---|
| 2.1 | Logo | The wordmark renders in teal `#2BBFB3` with the pink `#E8195A` dot, **centred at the top of the card**. |
| 2.2 | Card | White, rounded corners, drop shadow, roughly 448 px wide (`max-w-md`), clearly separated from the top of the viewport. |
| 2.3 | Field labels | In Spanish: "Correo electrónico", "Contraseña", "Recordarme". |
| 2.4 | Submit button | Reads "Iniciar sesión", spans the **full width** of the card, teal background, white text. |
| 2.5 | No user info | No employee name and no "Cerrar sesión" anywhere on this page. |
| 2.6 | Error colour | Submit the form empty. Validation messages appear in **pink** (`#E8195A`), not the browser default red. |

> **Known deviations to confirm as defects.** AC-24 asks for the logo **inside** the card and a `mt-24` top margin; the build places the logo **above** the card with `mt-6`. Record both in the results table.

---

## Scenario 3 — Employee login and dashboard (AC-09, AC-14, AC-15, AC-20, AC-21, AC-22, AC-23)

| # | Action | Expected result |
|---|---|---|
| 3.1 | Log in as `employee@test.com` / `Employee1` | Landed on `/dashboard`. |
| 3.2 | Greeting | Matches the current server hour: "¡Buenos días, Test Employee!" (06–12), "¡Buenas tardes, …!" (12–20), "¡Buenas noches, …!" (20–06). Verify the wording is right **for the hour you are testing**. |
| 3.3 | Avatar | 40×40 px circle, teal background, white initials "TE", vertically centred against the greeting. |
| 3.4 | Navbar | Logo on the left; on the right the name "Test Employee" and a "Cerrar sesión" link. White background, bottom border, 64 px tall, full width. |
| 3.5 | Page background | Light grey (`bg-gray-50`), not white. |
| 3.6 | Clock widget | Card titled "Fichaje" with a `›` on the right. Since no shift is open: grey dot and the label "Sin fichar". |
| 3.7 | Action button | Reads **"Fichar entrada"** on a teal background. |
| 3.8 | Click the button | **Nothing happens.** No navigation, no error, no console exception. It is a placeholder until US-002 (AC-18). |
| 3.9 | Empty state | Under "Registros del mes actual", the message "No records for this month yet." appears — not an empty table with headers. |

> **Known deviations to confirm.** AC-23 asks for a `max-w-5xl` content area and table headers in `text-gray-500 text-sm uppercase`; the build uses `max-w-7xl` and dark headers on a teal-tinted background. AC-22 labels the buttons "Entrada"/"Salida" while AC-16/AC-17 demand "Fichar entrada"/"Fichar salida" — the build follows AC-16/AC-17. Record all three.

---

## Scenario 4 — Open shift changes the button (AC-16, AC-17, AC-22)

Run the tinker command from *How to execute this plan* to open a shift for today, then reload `/dashboard`.

| # | Check | Expected result |
|---|---|---|
| 4.1 | Status row | Teal dot, pulsing, with the label "Fichado". |
| 4.2 | Action button | Now reads **"Fichar salida"** on a **pink** background. |
| 4.3 | Table | Today's row is listed. "Salida" shows `—` and "Total horas" shows `—`, because the shift is still open. |

---

## Scenario 5 — Records table formatting (AC-15)

Create one closed shift in the current month and one in the previous month:

```bash
sail artisan tinker --execute="\$id = App\Models\User::where('email','employee@test.com')->value('id'); App\Models\ClockRecord::create(['user_id'=>\$id,'clocked_in_at'=>now()->startOfMonth()->addDay()->setTime(8,30),'clocked_out_at'=>now()->startOfMonth()->addDay()->setTime(17,15)]); App\Models\ClockRecord::create(['user_id'=>\$id,'clocked_in_at'=>now()->subMonthNoOverflow()->startOfMonth()->setTime(8,0),'clocked_out_at'=>now()->subMonthNoOverflow()->startOfMonth()->setTime(16,0)]);"
```

| # | Check | Expected result |
|---|---|---|
| 5.1 | Columns | Fecha, Entrada, Salida, Total horas. |
| 5.2 | Date format | `DD/MM/YYYY`, e.g. `02/08/2026`. Not ISO, not US order. |
| 5.3 | Time format | `HH:mm` in 24-hour form, e.g. `08:30`. No AM/PM, no seconds. |
| 5.4 | Total hours | The 08:30→17:15 shift shows `8h 45m`. |
| 5.5 | Current month only | The **previous month's** row is **not** listed. |

---

## Scenario 6 — Cross-employee isolation (AC-19)

| # | Action | Expected result |
|---|---|---|
| 6.1 | Note the times visible for `employee@test.com` | Written down for comparison. |
| 6.2 | Log out, log in as `inactive@test.com` | Blocked — see Scenario 7. Reactivate it from the admin panel first if you want to compare, then repeat. |
| 6.3 | With a second active employee, log in as them | Their dashboard shows **none** of the first employee's records. The table is empty or shows only their own. |

---

## Scenario 7 — Inactive account is rejected (AC-11)

| # | Action | Expected result |
|---|---|---|
| 7.1 | Log in as `inactive@test.com` / `Employee1` | Stays on `/login`. |
| 7.2 | Message | Exactly: `Your account has been deactivated. Contact the administrator.` |
| 7.3 | Session | No session is created. Visiting `/dashboard` still redirects to `/login`. |

---

## Scenario 8 — Logout (AC-13)

| # | Action | Expected result |
|---|---|---|
| 8.1 | Logged in as an employee, click "Cerrar sesión" | Redirected to `/login`. |
| 8.2 | Press the browser Back button | The dashboard does **not** come back as an authenticated page; you are redirected to `/login`. |

---

## Scenario 9 — Admin login and role routing (AC-10, AC-02)

| # | Action | Expected result |
|---|---|---|
| 9.1 | At `/login`, log in with the admin credentials | Redirected to `/admin`, **not** `/dashboard`. |
| 9.2 | Filament dashboard | Renders with a "Dashboard" heading. |
| 9.3 | Log out, log in as `employee@test.com`, then visit `/admin` manually | Access is denied. |

> **Known deviation to confirm as a defect.** AC-02 requires a **redirect to `/login`**; the build returns a **403 Forbidden** page, which is Filament's default. Record what you actually see.

---

## Scenario 10 — Employee list (AC-04)

Logged in as admin, go to `/admin/employees`.

| # | Check | Expected result |
|---|---|---|
| 10.1 | Columns | Name, Email, Status, Created at. |
| 10.2 | Admin excluded | The admin's own email does **not** appear in the list. |
| 10.3 | Search | Typing part of a name filters the rows. Repeat searching by email. |
| 10.4 | Status filter | The filter narrows to active only and to inactive only. |
| 10.5 | Status column | Active rows show a green check, inactive rows a red cross. |

---

## Scenario 11 — Create an employee (AC-05)

| # | Action | Expected result |
|---|---|---|
| 11.1 | Click "New Employee" | The create form opens. |
| 11.2 | Submit it empty | Name, Email and Password are all reported as required. |
| 11.3 | Password `short1` | Rejected: fewer than 8 characters. |
| 11.4 | Password `password` | Rejected: contains no number. |
| 11.5 | Reuse `employee@test.com` as the email | Rejected as already taken. |
| 11.6 | Valid data: "Manual Tester" / `manual@test.com` / `Manual123`, Active on | Saved. A "Created" notification appears and the row shows in the list. |
| 11.7 | **Log out and log in as `manual@test.com` / `Manual123`** | Login succeeds. This is the check that the password was stored correctly hashed exactly once. |

> **Known deviation to confirm as a defect.** AC-05 requires the password field to have a **confirmation field**. The build has no confirmation input. Record it.

---

## Scenario 12 — Edit an employee (AC-06)

| # | Action | Expected result |
|---|---|---|
| 12.1 | Edit "Manual Tester", change the name, leave **Password blank**, save | "Saved" notification; the new name shows in the list. |
| 12.2 | Log in as `manual@test.com` with the **original** password `Manual123` | Still works — a blank password field must preserve the existing password. |
| 12.3 | Edit again, set password `Changed456`, save | Saved. |
| 12.4 | Log in with `Changed456` | Works. The old password no longer does. |
| 12.5 | Edit and turn **Active** off, save | The list shows the row as inactive, and that account is now rejected at login with the AC-11 message. |

---

## Scenario 13 — Delete and the protected admin (AC-07)

| # | Action | Expected result |
|---|---|---|
| 13.1 | Delete "Manual Tester" | A confirmation modal appears first. |
| 13.2 | Cancel it | The row is still there. |
| 13.3 | Confirm the deletion | The row disappears and does not come back after a reload. |
| 13.4 | Try to delete the admin account | Not possible: it is not listed at all (AC-04), so there is no delete control for it. |

---

## Results table

Fill this in as you go. `AC` is the criterion each scenario proves.

| Scenario | AC covered | Pass / Fail | Notes |
|---|---|---|---|
| 1 — Route protection | AC-01, AC-08, AC-12 | | |
| 2 — Login appearance | AC-21, AC-24 | | |
| 3 — Employee dashboard | AC-09, AC-14, AC-15, AC-20, AC-21, AC-22, AC-23 | | |
| 4 — Open shift button | AC-16, AC-17, AC-22 | | |
| 5 — Table formatting | AC-15 | | |
| 6 — Cross-employee isolation | AC-19 | | |
| 7 — Inactive rejected | AC-11 | | |
| 8 — Logout | AC-13 | | |
| 9 — Admin routing | AC-02, AC-10 | | |
| 10 — Employee list | AC-04 | | |
| 11 — Create employee | AC-05 | | |
| 12 — Edit employee | AC-06 | | |
| 13 — Delete and guard | AC-07 | | |

## Acceptance Criteria

- [ ] Every scenario has been executed against the Sail environment and its result recorded.
- [ ] Each of AC-01 to AC-24 is covered by at least one scenario.
- [ ] The four known deviations (AC-02 redirect vs 403, AC-05 missing password confirmation, AC-23 layout metrics, AC-24 logo placement) have each been explicitly confirmed or refuted.
- [ ] Every failure is written up with what was observed, not interpreted away.
- [ ] Branch follows naming convention.
- [ ] PR created.
