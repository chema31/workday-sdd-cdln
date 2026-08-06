<?php

namespace Tests\Unit\Helpers;

use App\Helpers\TimeGreetingHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class TimeGreetingHelperTest extends TestCase
{
    public static function morningHours(): array
    {
        return [[6], [7], [9], [11]];
    }

    public static function afternoonHours(): array
    {
        return [[12], [15], [19]];
    }

    public static function nightHours(): array
    {
        return [[20], [23], [0], [3], [5]];
    }

    #[DataProvider('morningHours')]
    public function test_returns_buenos_dias_between_6_and_12(int $hour): void
    {
        $this->assertSame(
            '¡Buenos días, Ada!',
            TimeGreetingHelper::getGreeting('Ada', $hour)
        );
    }

    #[DataProvider('afternoonHours')]
    public function test_returns_buenas_tardes_between_12_and_20(int $hour): void
    {
        $this->assertSame(
            '¡Buenas tardes, Ada!',
            TimeGreetingHelper::getGreeting('Ada', $hour)
        );
    }

    #[DataProvider('nightHours')]
    public function test_returns_buenas_noches_between_20_and_6(int $hour): void
    {
        $this->assertSame(
            '¡Buenas noches, Ada!',
            TimeGreetingHelper::getGreeting('Ada', $hour)
        );
    }

    public function test_includes_name_in_greeting(): void
    {
        $this->assertStringContainsString(
            'Grace Hopper',
            TimeGreetingHelper::getGreeting('Grace Hopper', 10)
        );
    }
}
