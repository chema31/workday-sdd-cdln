<?php

namespace Tests\Unit\Services;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    private UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userService = new UserService();
    }

    public function test_create_employee_creates_user_with_employee_role(): void
    {
        $user = $this->userService->createEmployee([
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'password' => 'Secret123',
        ]);

        $this->assertSame(UserRole::Employee, $user->role);
    }

    public function test_create_employee_hashes_password(): void
    {
        $plainPassword = 'Secret123';

        $user = $this->userService->createEmployee([
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'password' => $plainPassword,
        ]);

        $this->assertNotSame($plainPassword, $user->password);
        $this->assertTrue(Hash::check($plainPassword, $user->password));
    }

    public function test_create_employee_throws_when_password_too_short(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->userService->createEmployee([
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'password' => 'Ab1',
        ]);
    }

    public function test_create_employee_throws_when_password_has_no_number(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->userService->createEmployee([
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'password' => 'NoDigitsHere',
        ]);
    }

    public function test_update_employee_changes_password_when_provided(): void
    {
        $user = User::factory()->create();
        $newPassword = 'Brand4New';

        $this->userService->updateEmployee($user, [
            'name' => 'Grace Hopper',
            'email' => 'grace@example.com',
            'is_active' => true,
            'password' => $newPassword,
        ]);

        $this->assertTrue(Hash::check($newPassword, $user->fresh()->password));
    }

    public function test_update_employee_preserves_password_when_not_provided(): void
    {
        $user = User::factory()->create();
        $originalHash = $user->password;

        $this->userService->updateEmployee($user, [
            'name' => 'Grace Hopper',
            'email' => 'grace@example.com',
            'is_active' => false,
            'password' => null,
        ]);

        $this->assertSame($originalHash, $user->fresh()->password);
    }

    public function test_is_protected_admin_returns_true_for_admin_email(): void
    {
        config(['app.admin_email' => 'boss@example.com']);
        $admin = User::factory()->create(['email' => 'boss@example.com']);

        $this->assertTrue($this->userService->isProtectedAdmin($admin));
    }

    public function test_is_protected_admin_returns_false_for_other_emails(): void
    {
        config(['app.admin_email' => 'boss@example.com']);
        $employee = User::factory()->create(['email' => 'someone@example.com']);

        $this->assertFalse($this->userService->isProtectedAdmin($employee));
    }
}
