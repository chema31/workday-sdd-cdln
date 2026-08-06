<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClockRecord>
 */
class ClockRecordFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'        => User::factory(),
            'clocked_in_at'  => now()->subHours(rand(1, 8)),
            'clocked_out_at' => null,
        ];
    }

    public function withClockOut(): static
    {
        return $this->state(function (array $attributes) {
            $clockedIn = $attributes['clocked_in_at'] ?? now()->subHours(rand(1, 8));

            return [
                'clocked_out_at' => $clockedIn->copy()->addHours(rand(1, 4)),
            ];
        });
    }
}
