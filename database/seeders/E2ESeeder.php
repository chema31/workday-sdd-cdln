<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Fixed accounts for the Playwright end-to-end suite.
 *
 * Idempotent: safe to run against an already-seeded database.
 */
class E2ESeeder extends Seeder
{
    public function run(): void
    {
        $this->call(AdminSeeder::class);

        User::firstOrCreate(
            ['email' => 'employee@test.com'],
            [
                'name' => 'Test Employee',
                'password' => Hash::make('Employee1'),
                'role' => UserRole::Employee,
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'inactive@test.com'],
            [
                'name' => 'Inactive Employee',
                'password' => Hash::make('Employee1'),
                'role' => UserRole::Employee,
                'is_active' => false,
            ]
        );
    }
}
