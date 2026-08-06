<?php

namespace Tests\Unit\Models;

use App\Models\ClockRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClockRecordModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_clock_record_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $record = ClockRecord::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $record->user);
        $this->assertEquals($user->id, $record->user->id);
    }

    public function test_user_has_many_clock_records(): void
    {
        $user = User::factory()->create();
        ClockRecord::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertCount(3, $user->clockRecords);
        $user->clockRecords->each(
            fn (ClockRecord $r) => $this->assertEquals($user->id, $r->user_id)
        );
    }

    public function test_clocked_in_at_is_cast_to_datetime(): void
    {
        $record = ClockRecord::factory()->create();

        $this->assertInstanceOf(\Carbon\Carbon::class, $record->clocked_in_at);
    }

    public function test_clocked_out_at_is_nullable(): void
    {
        $record = ClockRecord::factory()->create(['clocked_out_at' => null]);

        $this->assertNull($record->clocked_out_at);
    }

    public function test_factory_with_clock_out_state_sets_clocked_out_at(): void
    {
        $record = ClockRecord::factory()->withClockOut()->create();

        $this->assertNotNull($record->clocked_out_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $record->clocked_out_at);
    }
}
