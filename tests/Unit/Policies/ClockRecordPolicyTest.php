<?php

namespace Tests\Unit\Policies;

use App\Enums\UserRole;
use App\Models\ClockRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClockRecordPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_own_clock_record(): void
    {
        $user = User::factory()->create();
        $clockRecord = ClockRecord::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->can('view', $clockRecord));
    }

    public function test_user_cannot_view_other_users_clock_record(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $clockRecord = ClockRecord::factory()->create(['user_id' => $owner->id]);

        $this->assertFalse($otherUser->can('view', $clockRecord));
    }

    /**
     * Administrators reach attendance data through the Filament panel, never
     * through this policy.
     */
    public function test_admin_cannot_view_employee_clock_record_via_policy(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $employee = User::factory()->create(['role' => UserRole::Employee]);
        $clockRecord = ClockRecord::factory()->create(['user_id' => $employee->id]);

        $this->assertFalse($admin->can('view', $clockRecord));
    }
}
