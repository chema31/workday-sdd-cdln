<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Create the administrator account defined in the environment.
     *
     * Uses firstOrCreate so repeated seeding never duplicates the account.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => config('app.admin_email')],
            [
                'name' => config('app.admin_name'),
                'password' => Hash::make(config('app.admin_password')),
                'role' => UserRole::Admin,
                'is_active' => true,
            ]
        );
    }
}
