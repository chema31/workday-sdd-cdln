<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeAuthTest extends TestCase
{
    use RefreshDatabase;

    private const DEACTIVATED_MESSAGE = 'Your account has been deactivated. Contact the administrator.';

    public function test_employee_can_login_and_is_redirected_to_dashboard(): void
    {
        $employee = User::factory()->create(['is_active' => true]);

        $response = $this->post('/login', [
            'email' => $employee->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($employee);
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_admin_login_redirects_to_admin_panel(): void
    {
        $admin = User::factory()->admin()->create(['is_active' => true]);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect('/admin');
    }

    public function test_inactive_employee_is_rejected_at_login(): void
    {
        $employee = User::factory()->create(['is_active' => false]);

        $response = $this->post('/login', [
            'email' => $employee->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors(['email' => self::DEACTIVATED_MESSAGE]);
        $this->assertGuest();
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_logout_destroys_session_and_redirects_to_login(): void
    {
        $employee = User::factory()->create(['is_active' => true]);

        $response = $this->actingAs($employee)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }
}
