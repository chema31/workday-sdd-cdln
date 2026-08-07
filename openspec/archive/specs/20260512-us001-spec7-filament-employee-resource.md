# Spec 7: Filament EmployeeResource (Admin CRUD)

## Metadata
- **User Story**: [US-001](../features/20260511-user-management-and-application-bootstrap.md)
- **Type**: Backend (Laravel Admin Panel)
- **Branch**: feature/20260511_user_management_and_application_bootstrap
- **Estimated effort**: M
- **Depends on**: Spec 6 (UserService)

## Overview
Implement the Filament v3 `EmployeeResource` for the `/admin` panel. Covers the employee list (with search and filters), create/edit forms, and a guarded delete action. The admin account (identified by `ADMIN_EMAIL`) must be excluded from the list and cannot be deleted.

## Architecture Context
- `app/Filament/Resources/EmployeeResource.php` — main resource class
- `app/Filament/Resources/EmployeeResource/Pages/` — List, Create, Edit page classes
- `App\Services\User\UserService` — injected; all writes go through the service
- `User` model scoped via `getEloquentQuery()` to exclude the admin account

## Implementation Steps

### Step 0: Verify on feature branch and dependencies
- Confirm `UserService` exists with `createEmployee()`, `updateEmployee()`, `isProtectedAdmin()`.

### Step 1: Scaffold the resource
```bash
php artisan make:filament-resource Employee --generate
```
This creates `EmployeeResource.php` and the three page classes.

### Step 2: Configure `getEloquentQuery()`
Add to `EmployeeResource`:
```php
public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()
        ->where('email', '!=', config('app.admin_email'));
}
```

### Step 3: Configure the Table (list view — AC-04)
```php
public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('email')->searchable(),
            IconColumn::make('is_active')
                ->label('Status')
                ->boolean()
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-x-circle')
                ->trueColor('success')
                ->falseColor('danger'),
            TextColumn::make('created_at')->dateTime('d/m/Y')->sortable(),
        ])
        ->filters([
            TernaryFilter::make('is_active')->label('Status'),
        ])
        ->actions([
            EditAction::make(),
            DeleteAction::make()
                ->before(function (DeleteAction $action, User $record) {
                    if (app(UserService::class)->isProtectedAdmin($record)) {
                        Notification::make()
                            ->title('Cannot delete the administrator account.')
                            ->danger()
                            ->send();
                        $action->cancel();
                    }
                }),
        ]);
}
```

### Step 4: Configure the Form (create/edit — AC-05, AC-06)
```php
public static function form(Form $form): Form
{
    $isEdit = $form->getOperation() === 'edit';

    return $form->schema([
        TextInput::make('name')->required()->maxLength(255),
        TextInput::make('email')->email()->required()->unique(
            table: 'users',
            column: 'email',
            ignoreRecord: true
        ),
        TextInput::make('password')
            ->password()
            ->required(!$isEdit)
            ->minLength(8)
            ->rules(['regex:/[0-9]/'])
            ->helperText('Minimum 8 characters, must contain at least one number.')
            ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
            ->dehydrated(fn ($state) => filled($state)),
        Toggle::make('is_active')->label('Active')->default(true),
    ]);
}
```

### Step 5: Wire form to UserService in CreateEmployee and EditEmployee pages
- **File**: `app/Filament/Resources/EmployeeResource/Pages/CreateEmployee.php`
  - Override `handleRecordCreation(array $data): Model`:
    ```php
    protected function handleRecordCreation(array $data): Model
    {
        return app(UserService::class)->createEmployee($data);
    }
    ```
- **File**: `app/Filament/Resources/EmployeeResource/Pages/EditEmployee.php`
  - Override `handleRecordUpdate(Model $record, array $data): Model`:
    ```php
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(UserService::class)->updateEmployee($record, $data);
    }
    ```

### Step 6: Register resource in AdminPanelProvider
Filament auto-discovers resources. Verify `app/Providers/Filament/AdminPanelProvider.php` has `->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')`.

### Step 7: Run tests
```bash
php artisan test
```

### Step LAST: Update Documentation
- No `rules/` changes needed.

## Unit Test Specifications (TDD)

Write tests FIRST in `tests/Feature/Admin/EmployeeResourceTest.php`:

```php
use RefreshDatabase;

// Setup: create admin user and authenticate as admin before each test
```

- `test_admin_can_view_employee_list` — acting as admin, GET `/admin/employees`, assert 200.
- `test_employee_list_excludes_admin_account` — create employee + admin, list page does not contain admin email.
- `test_admin_can_create_employee` — POST create form with valid data, assertDatabaseHas.
- `test_admin_cannot_create_employee_with_weak_password` — POST with password '12345678' (no letters/numbers? — actually the rule is min 8 + 1 number, so 'password' without a number should fail), assert validation error.
- `test_admin_can_edit_employee` — PUT/PATCH update form, assertDatabaseHas.
- `test_admin_cannot_delete_seeded_admin` — attempt delete of admin account, assert it still exists in DB.

**Note on Filament testing**: Use `Livewire::test(ListEmployees::class)` or use `actingAs($admin)->get('/admin/employees')` HTTP approach. Follow Filament v3 testing docs.

## Acceptance Criteria
- [ ] `getEloquentQuery()` excludes the admin email from the employee list.
- [ ] List has Name, Email, Status, Created At columns; searchable by name and email; filterable by `is_active`.
- [ ] Create form requires password (min 8 + 1 number); edit form makes password optional.
- [ ] Delete action is blocked for the admin account with an error notification.
- [ ] Create/edit delegate to `UserService`.
- [ ] All 6 feature tests pass.
- [ ] Branch follows naming convention.
- [ ] PR created in Bitbucket.
