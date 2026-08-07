<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Filament\Resources\EmployeeResource\Pages\CreateEmployee;
use App\Filament\Resources\EmployeeResource\Pages\EditEmployee;
use App\Filament\Resources\EmployeeResource\Pages\ListEmployees;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class EmployeeResourceTest extends TestCase
{
    use RefreshDatabase;

    private const ADMIN_EMAIL = 'admin@example.com';

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.admin_email' => self::ADMIN_EMAIL]);

        $this->admin = User::factory()->create([
            'email' => self::ADMIN_EMAIL,
            'role' => UserRole::Admin,
            'is_active' => true,
        ]);

        $this->actingAs($this->admin);
    }

    public function test_admin_can_view_employee_list(): void
    {
        $this->get('/admin/employees')->assertOk();
    }

    public function test_employee_list_excludes_admin_account(): void
    {
        $employee = User::factory()->create([
            'email' => 'employee@example.com',
            'role' => UserRole::Employee,
        ]);

        Livewire::test(ListEmployees::class)
            ->assertCanSeeTableRecords([$employee])
            ->assertCanNotSeeTableRecords([$this->admin]);
    }

    public function test_admin_can_create_employee(): void
    {
        Livewire::test(CreateEmployee::class)
            ->fillForm([
                'name' => 'Ada Lovelace',
                'email' => 'ada@example.com',
                'password' => 'Secret123',
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', [
            'email' => 'ada@example.com',
            'name' => 'Ada Lovelace',
            'role' => UserRole::Employee->value,
        ]);
    }

    /**
     * Guards against hashing the password twice: the form must hand the plain
     * password to UserService, which is the only place that hashes it.
     */
    public function test_created_employee_can_authenticate_with_the_chosen_password(): void
    {
        $plainPassword = 'Secret123';

        Livewire::test(CreateEmployee::class)
            ->fillForm([
                'name' => 'Ada Lovelace',
                'email' => 'ada@example.com',
                'password' => $plainPassword,
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $employee = User::where('email', 'ada@example.com')->sole();

        $this->assertTrue(
            Hash::check($plainPassword, $employee->password),
            'The stored password must be a single hash of the plain password.'
        );
    }

    public static function weakPasswords(): array
    {
        return [
            'no number' => ['password'],
            'too short' => ['12345'],
        ];
    }

    #[DataProvider('weakPasswords')]
    public function test_admin_cannot_create_employee_with_weak_password(string $weakPassword): void
    {
        Livewire::test(CreateEmployee::class)
            ->fillForm([
                'name' => 'Ada Lovelace',
                'email' => 'ada@example.com',
                'password' => $weakPassword,
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasFormErrors(['password']);

        $this->assertDatabaseMissing('users', ['email' => 'ada@example.com']);
    }

    public function test_admin_can_edit_employee(): void
    {
        $employee = User::factory()->create([
            'email' => 'old@example.com',
            'role' => UserRole::Employee,
            'is_active' => true,
        ]);

        Livewire::test(EditEmployee::class, ['record' => $employee->getKey()])
            ->fillForm([
                'name' => 'Grace Hopper',
                'email' => 'grace@example.com',
                'is_active' => false,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', [
            'id' => $employee->id,
            'name' => 'Grace Hopper',
            'email' => 'grace@example.com',
            'is_active' => false,
        ]);
    }

    public function test_editing_without_a_new_password_preserves_the_existing_one(): void
    {
        $employee = User::factory()->create([
            'email' => 'keep@example.com',
            'role' => UserRole::Employee,
            'is_active' => true,
        ]);
        $originalHash = $employee->password;

        Livewire::test(EditEmployee::class, ['record' => $employee->getKey()])
            ->fillForm([
                'name' => 'Same Person',
                'email' => 'keep@example.com',
                'is_active' => true,
                'password' => null,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame($originalHash, $employee->fresh()->password);
    }

    public function test_admin_cannot_delete_seeded_admin(): void
    {
        Livewire::test(ListEmployees::class)
            ->assertCanNotSeeTableRecords([$this->admin]);

        $this->assertDatabaseHas('users', ['email' => self::ADMIN_EMAIL]);
    }
}
