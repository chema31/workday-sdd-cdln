# Spec 4: ClockRecord Model and Factory

## Metadata

| Field       | Value                                                             |
|-------------|-------------------------------------------------------------------|
| US          | US-001                                                            |
| Area        | Backend (Laravel Admin Panel)                                     |
| Effort      | S                                                                 |
| Depends on  | Spec 3 (User model, migrations)                                   |
| Branch      | `feature/20260511_user_management_and_application_bootstrap`      |
| PR          | Bitbucket                                                         |
| Date        | 2026-05-12                                                        |

---

## Overview

Create the `App\Models\ClockRecord` Eloquent model with `$fillable`, `$casts`, and a `BelongsTo` relationship to `User`. Add the inverse `HasMany` relationship on the `User` model. Generate and configure a `ClockRecordFactory` with a `withClockOut` state.

---

## Implementation Steps

> **Write the tests first (TDD).** The steps below follow after the tests are in place and failing.

### Step 1 — Generate model and factory

```bash
php artisan make:model ClockRecord -f
```

This creates:
- `app/Models/ClockRecord.php`
- `database/factories/ClockRecordFactory.php`

---

### Step 2 — Configure `ClockRecord` model

File: `app/Models/ClockRecord.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClockRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'clocked_in_at',
        'clocked_out_at',
    ];

    protected $casts = [
        'clocked_in_at'  => 'datetime',
        'clocked_out_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

---

### Step 3 — Add `clockRecords` relationship to `User` model

File: `app/Models/User.php`

Add the following method:

```php
use Illuminate\Database\Eloquent\Relations\HasMany;

public function clockRecords(): HasMany
{
    return $this->hasMany(ClockRecord::class);
}
```

---

### Step 4 — Configure `ClockRecordFactory`

File: `database/factories/ClockRecordFactory.php`

```php
<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClockRecordFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'        => User::factory(),
            'clocked_in_at'  => now()->subHours(rand(1, 8)),
            'clocked_out_at' => null,
        ];
    }

    public function withClockOut(): static
    {
        return $this->state(function (array $attributes) {
            $clockedIn = $attributes['clocked_in_at'] ?? now()->subHours(rand(1, 8));

            return [
                'clocked_out_at' => $clockedIn->copy()->addHours(rand(1, 4)),
            ];
        });
    }
}
```

---

### Step 5 — Run tests

```bash
php artisan test
```

All 5 unit tests in `tests/Unit/Models/ClockRecordModelTest.php` must pass.

---

## Tests

Write these tests **before** implementing. File: `tests/Unit/Models/ClockRecordModelTest.php`

| # | Test name                                          | What it asserts                                                                 |
|---|----------------------------------------------------|---------------------------------------------------------------------------------|
| 1 | `test_clock_record_belongs_to_user`                | A `ClockRecord` instance returns a `User` when accessing `->user`               |
| 2 | `test_user_has_many_clock_records`                 | A `User` instance returns a collection of `ClockRecord` via `->clockRecords`   |
| 3 | `test_clocked_in_at_is_cast_to_datetime`           | `clocked_in_at` attribute is an instance of `Carbon\Carbon`                    |
| 4 | `test_clocked_out_at_is_nullable`                  | A `ClockRecord` can be saved with `clocked_out_at = null`                       |
| 5 | `test_factory_with_clock_out_state_sets_clocked_out_at` | `ClockRecord::factory()->withClockOut()->create()` has a non-null `clocked_out_at` |

Use `RefreshDatabase` trait. Use `ClockRecord::factory()` and `User::factory()` to create fixtures.

---

## Acceptance Criteria

- [ ] `App\Models\ClockRecord` exists with correct `$fillable` and `$casts`.
- [ ] `ClockRecord::user()` returns a `BelongsTo` relationship.
- [ ] `User::clockRecords()` returns a `HasMany` relationship.
- [ ] `ClockRecordFactory` default state has `clocked_out_at = null`.
- [ ] `ClockRecordFactory::withClockOut()` state sets a non-null `clocked_out_at`.
- [ ] All 5 unit tests pass.
