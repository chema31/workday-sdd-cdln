<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSeederTest extends TestCase
{
    use RefreshDatabase;

    private const ADMIN_EMAIL = 'seeded-admin@example.com';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.admin_email' => self::ADMIN_EMAIL,
            'app.admin_name' => 'Seeded Administrator',
            'app.admin_password' => 'SeedPass123',
        ]);
    }

    public function test_admin_seeder_creates_user_from_env_values(): void
    {
        $this->seed(AdminSeeder::class);

        $this->assertDatabaseHas('users', [
            'email' => config('app.admin_email'),
            'name' => config('app.admin_name'),
        ]);
    }

    public function test_admin_seeder_is_idempotent(): void
    {
        $this->seed(AdminSeeder::class);
        $this->seed(AdminSeeder::class);

        $this->assertSame(
            1,
            User::where('email', self::ADMIN_EMAIL)->count(),
            'Seeding twice must not duplicate the admin user.'
        );
    }

    public function test_seeded_admin_has_admin_role(): void
    {
        $this->seed(AdminSeeder::class);

        $admin = User::where('email', self::ADMIN_EMAIL)->sole();

        $this->assertSame(UserRole::Admin, $admin->role);
    }

    public function test_seeded_admin_is_active(): void
    {
        $this->seed(AdminSeeder::class);

        $admin = User::where('email', self::ADMIN_EMAIL)->sole();

        $this->assertTrue($admin->is_active);
    }
}
