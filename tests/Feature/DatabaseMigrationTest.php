<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_table_has_role_column(): void
    {
        $this->assertTrue(Schema::hasColumn('users', 'role'));
    }

    public function test_users_table_has_is_active_column(): void
    {
        $this->assertTrue(Schema::hasColumn('users', 'is_active'));
    }

    public function test_clock_records_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('clock_records'));
    }

    public function test_clock_records_user_id_is_foreign_key(): void
    {
        $user = \App\Models\User::factory()->create();

        \DB::table('clock_records')->insert([
            'user_id'       => $user->id,
            'clocked_in_at' => now(),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        $this->assertDatabaseHas('clock_records', ['user_id' => $user->id]);

        $user->delete();

        $this->assertDatabaseMissing('clock_records', ['user_id' => $user->id]);
    }
}
