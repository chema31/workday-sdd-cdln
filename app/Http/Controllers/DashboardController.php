<?php

namespace App\Http\Controllers;

use App\Helpers\TimeGreetingHelper;
use App\Models\ClockRecord;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const RECORDS_PER_PAGE = 50;

    public function index(): View
    {
        $user = auth()->user();

        // Scoped to the authenticated user, mirroring ClockRecordPolicy as
        // defence in depth: the query can never surface another employee's rows.
        $records = ClockRecord::where('user_id', $user->id)
            ->whereMonth('clocked_in_at', now()->month)
            ->whereYear('clocked_in_at', now()->year)
            ->orderBy('clocked_in_at', 'desc')
            ->paginate(self::RECORDS_PER_PAGE);

        $hasOpenRecord = ClockRecord::where('user_id', $user->id)
            ->whereDate('clocked_in_at', today())
            ->whereNull('clocked_out_at')
            ->exists();

        return view('dashboard', [
            'records' => $records,
            'greeting' => TimeGreetingHelper::getGreeting($user->name),
            'initials' => $this->initialsFor($user->name),
            'hasOpenRecord' => $hasOpenRecord,
        ]);
    }

    /**
     * First letter of the first word plus first letter of the last word.
     */
    private function initialsFor(string $name): string
    {
        $words = preg_split('/\s+/', trim($name), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if ($words === []) {
            return '';
        }

        $first = Str::substr($words[0], 0, 1);
        $last = count($words) > 1 ? Str::substr($words[count($words) - 1], 0, 1) : '';

        return Str::upper($first.$last);
    }
}
