# Spec 9: Employee Dashboard

## Metadata

- **User Story**: US-001
- **Area**: Backend (Laravel Admin Panel)
- **Effort**: M
- **Depends on**: Spec 4, Spec 8, Spec 10
- **Branch**: `feature/20260511_user_management_and_application_bootstrap`
- **PR**: Bitbucket
- **Date**: 2026-05-12

---

## Overview

Implement `DashboardController` and its Blade view. The dashboard shows a time-sensitive greeting with initials avatar (`TimeGreetingHelper`), a clock records table for the current calendar month (paginated 50/page, DD/MM/YYYY HH:mm format), an empty-state message when no records exist, and a conditional placeholder button ("Fichar entrada" / "Fichar salida") based on whether there is an open `clock_record` for today. Button clicks do nothing in this story (US-002 wires the action).

---

## Architecture Context

- `app/Http/Controllers/DashboardController.php` — thin controller, delegates to service/query
- `app/Helpers/TimeGreetingHelper.php` — resolves greeting string from hour (06-12 = "Buenos días", 12-20 = "Buenas tardes", 20-06 = "Buenas noches")
- `resources/views/dashboard.blade.php` — Blade view using `<x-app-layout>` (Breeze) or custom layout
- `App\Policies\ClockRecordPolicy` — enforced via `$this->authorize()` in controller (after Spec 10)

---

## Implementation Steps

### Step 0: Verify on feature branch

Confirm you are on `feature/20260511_user_management_and_application_bootstrap` before making any changes.

---

### Step 1: Create `App\Helpers\TimeGreetingHelper`

- **File**: `app/Helpers/TimeGreetingHelper.php`
- **Method**: `public static function getGreeting(string $name, ?int $hour = null): string`
- `$hour` defaults to `now()->hour` if null (injectable for testing)
- **Logic**:
  - 6 ≤ hour < 12 → `"¡Buenos días, {$name}!"`
  - 12 ≤ hour < 20 → `"¡Buenas tardes, {$name}!"`
  - otherwise → `"¡Buenas noches, {$name}!"`

---

### Step 2: Create `DashboardController`

- **File**: `app/Http/Controllers/DashboardController.php`
- **Method**: `public function index(): View`
- **Logic**:
  1. Get authenticated user: `$user = auth()->user()`
  2. Build greeting: `$greeting = TimeGreetingHelper::getGreeting($user->name)`
  3. Build initials: first letter of first word + first letter of last word in name (uppercase)
  4. Query current month records (scoped to user via policy/scope):
     ```php
     $records = ClockRecord::where('user_id', $user->id)
         ->whereMonth('clocked_in_at', now()->month)
         ->whereYear('clocked_in_at', now()->year)
         ->orderBy('clocked_in_at', 'desc')
         ->paginate(50);
     ```
  5. Check for open record today:
     ```php
     $hasOpenRecord = ClockRecord::where('user_id', $user->id)
         ->whereDate('clocked_in_at', today())
         ->whereNull('clocked_out_at')
         ->exists();
     ```
  6. Return view with: `$records`, `$greeting`, `$initials`, `$hasOpenRecord`

---

### Step 3: Create `resources/views/dashboard.blade.php`

- Use `<x-app-layout>` wrapper (from Breeze, customised in Spec 12)
- **Structure**:
  1. Greeting row: initials avatar circle (teal bg, white text, 40px) + greeting text
  2. Clock widget card placeholder: `<x-clock-widget :hasOpenRecord="$hasOpenRecord" />` (component built in Spec 12)
  3. "Registros del mes actual" section heading
  4. If `$records->isEmpty()`: show `<p class="text-gray-400">No records for this month yet.</p>`
  5. Else: table with columns Fecha, Entrada, Salida, Total horas
     - **Fecha**: `{{ $record->clocked_in_at->format('d/m/Y') }}`
     - **Entrada**: `{{ $record->clocked_in_at->format('H:i') }}`
     - **Salida**: `{{ $record->clocked_out_at?->format('H:i') ?? '—' }}`
     - **Total**: calculate only if `clocked_out_at` is not null: `{{ $record->clocked_out_at->diffInMinutes($record->clocked_in_at) / 60 }}h` formatted as `Xh Ym`; otherwise `—`
  6. Pagination links: `{{ $records->links() }}`

---

### Step 4: Register route in `routes/web.php`

```php
use App\Http\Controllers\DashboardController;

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
```

---

### Step 5: Run `php artisan test`

All tests must pass before marking this spec complete.

---

### Step LAST: No `rules/` changes needed.

---

## Unit Test Specifications (TDD)

Write tests FIRST before implementing any production code.

### File: `tests/Unit/Helpers/TimeGreetingHelperTest.php`

| Test | Description |
|------|-------------|
| `test_returns_buenos_dias_between_6_and_12` | Verify greeting string for hours 6–11 |
| `test_returns_buenas_tardes_between_12_and_20` | Verify greeting string for hours 12–19 |
| `test_returns_buenas_noches_between_20_and_6` | Verify greeting string for hours 20–5 |
| `test_includes_name_in_greeting` | Confirm the user's name appears in the returned string |

### File: `tests/Feature/Dashboard/DashboardTest.php`

| Test | Description |
|------|-------------|
| `test_unauthenticated_user_is_redirected_to_login` | GET `/dashboard`, assert redirect to login |
| `test_dashboard_shows_greeting_with_employee_name` | Authenticated employee sees their name in the greeting |
| `test_dashboard_shows_current_month_records_only` | Create records in current month and previous month; confirm only current month shown |
| `test_employee_sees_only_own_records` | Create records for two employees, login as one, assert only own records visible (AC-19) |
| `test_dashboard_shows_empty_message_when_no_records` | No records exist; assert "No records for this month yet." |
| `test_button_shows_fichar_entrada_when_no_open_record` | No open record today; assert "Fichar entrada" in response |
| `test_button_shows_fichar_salida_when_open_record_exists` | Open record today (`clocked_out_at` null); assert "Fichar salida" in response |

---

## Acceptance Criteria

- [ ] `TimeGreetingHelper::getGreeting()` resolves correctly for all three time periods.
- [ ] Dashboard query scoped to authenticated user only.
- [ ] Records filtered to current calendar month, paginated 50/page.
- [ ] Dates display as DD/MM/YYYY, times as HH:mm.
- [ ] Empty state shows "No records for this month yet."
- [ ] Conditional button text correct (AC-16, AC-17).
- [ ] All 11 tests (4 unit + 7 feature) pass.
- [ ] Branch follows naming convention.
- [ ] PR created in Bitbucket.
