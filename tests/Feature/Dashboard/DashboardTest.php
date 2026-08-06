<?php

namespace Tests\Feature\Dashboard;

use App\Models\ClockRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_dashboard_shows_greeting_with_employee_name(): void
    {
        $employee = User::factory()->create(['name' => 'Ada Lovelace']);

        $this->actingAs($employee)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Ada Lovelace');
    }

    public function test_dashboard_shows_current_month_records_only(): void
    {
        $employee = User::factory()->create();

        $thisMonth = ClockRecord::factory()->create([
            'user_id' => $employee->id,
            'clocked_in_at' => now()->startOfMonth()->addDays(2)->setTime(9, 0),
            'clocked_out_at' => now()->startOfMonth()->addDays(2)->setTime(17, 30),
        ]);

        $lastMonth = ClockRecord::factory()->create([
            'user_id' => $employee->id,
            'clocked_in_at' => now()->subMonthNoOverflow()->startOfMonth()->setTime(8, 0),
            'clocked_out_at' => now()->subMonthNoOverflow()->startOfMonth()->setTime(16, 0),
        ]);

        $response = $this->actingAs($employee)->get('/dashboard')->assertOk();

        $response->assertSee($thisMonth->clocked_in_at->format('d/m/Y'));
        $response->assertDontSee($lastMonth->clocked_in_at->format('d/m/Y'));
    }

    public function test_employee_sees_only_own_records(): void
    {
        $employee = User::factory()->create();
        $colleague = User::factory()->create();

        $own = ClockRecord::factory()->create([
            'user_id' => $employee->id,
            'clocked_in_at' => now()->startOfMonth()->addDays(1)->setTime(9, 15),
            'clocked_out_at' => now()->startOfMonth()->addDays(1)->setTime(18, 0),
        ]);

        $foreign = ClockRecord::factory()->create([
            'user_id' => $colleague->id,
            'clocked_in_at' => now()->startOfMonth()->addDays(3)->setTime(7, 45),
            'clocked_out_at' => now()->startOfMonth()->addDays(3)->setTime(15, 30),
        ]);

        $response = $this->actingAs($employee)->get('/dashboard')->assertOk();

        $response->assertSee($own->clocked_in_at->format('H:i'));
        $response->assertDontSee($foreign->clocked_in_at->format('H:i'));
    }

    public function test_dashboard_shows_empty_message_when_no_records(): void
    {
        $employee = User::factory()->create();

        $this->actingAs($employee)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('No records for this month yet.');
    }

    public function test_button_shows_fichar_entrada_when_no_open_record(): void
    {
        $employee = User::factory()->create();

        ClockRecord::factory()->create([
            'user_id' => $employee->id,
            'clocked_in_at' => today()->setTime(9, 0),
            'clocked_out_at' => today()->setTime(17, 0),
        ]);

        $this->actingAs($employee)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Fichar entrada');
    }

    public function test_button_shows_fichar_salida_when_open_record_exists(): void
    {
        $employee = User::factory()->create();

        ClockRecord::factory()->create([
            'user_id' => $employee->id,
            'clocked_in_at' => today()->setTime(9, 0),
            'clocked_out_at' => null,
        ]);

        $this->actingAs($employee)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Fichar salida');
    }
}
