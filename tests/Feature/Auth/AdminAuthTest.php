<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * End-to-end checks that the admin panel is actually gated by the middleware.
 * UserModelTest covers canAccessPanel() in isolation; these hit the route.
 */
class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_to_admin_panel(): void
    {
        $admin = User::factory()->admin()->create(['is_active' => true]);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect('/admin');

        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    public function test_employee_cannot_access_admin_panel(): void
    {
        $employee = User::factory()->create(['is_active' => true]);

        $this->actingAs($employee)
            ->get('/admin')
            ->assertForbidden();
    }
}
