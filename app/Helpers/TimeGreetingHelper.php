<?php

namespace App\Helpers;

class TimeGreetingHelper
{
    /**
     * Build a time-sensitive greeting.
     *
     * The hour is injectable so the behaviour can be tested without freezing time.
     */
    public static function getGreeting(string $name, ?int $hour = null): string
    {
        $hour ??= now()->hour;

        $moment = match (true) {
            $hour >= 6 && $hour < 12 => 'Buenos días',
            $hour >= 12 && $hour < 20 => 'Buenas tardes',
            default => 'Buenas noches',
        };

        return "¡{$moment}, {$name}!";
    }
}
