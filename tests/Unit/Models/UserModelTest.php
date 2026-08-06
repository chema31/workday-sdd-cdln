<?php

namespace Tests\Unit\Models;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Panel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_role_enum_has_admin_case(): void
    {
        $this->assertSame('admin', UserRole::Admin->value);
    }

    public function test_user_role_enum_has_employee_case(): void
    {
        $this->assertSame('employee', UserRole::Employee->value);
    }

    public function test_admin_user_can_access_filament_panel(): void
    {
        $user = User::factory()->admin()->create();
        $panel = app(Panel::class);

        $this->assertTrue($user->canAccessPanel($panel));
    }

    public function test_employee_user_cannot_access_filament_panel(): void
    {
        $user = User::factory()->create(); // default = employee
        $panel = app(Panel::class);

        $this->assertFalse($user->canAccessPanel($panel));
    }

    public function test_active_scope_returns_only_active_users(): void
    {
        User::factory()->count(3)->create(['is_active' => true]);
        User::factory()->inactive()->create();

        $activeUsers = User::active()->get();

        $this->assertCount(3, $activeUsers);
        $activeUsers->each(fn (User $u) => $this->assertTrue($u->is_active));
    }

    public function test_employees_scope_excludes_admins(): void
    {
        User::factory()->count(2)->create(); // default = employee
        User::factory()->admin()->create();

        $employees = User::employees()->get();

        $this->assertCount(2, $employees);
        $employees->each(fn (User $u) => $this->assertSame(UserRole::Employee, $u->role));
    }

    public function test_factory_admin_state_sets_admin_role(): void
    {
        $user = User::factory()->admin()->make();

        $this->assertSame(UserRole::Admin, $user->role);
    }

    public function test_factory_inactive_state_sets_is_active_false(): void
    {
        $user = User::factory()->inactive()->make();

        $this->assertFalse($user->is_active);
    }
}