<?php

namespace App\Policies;

use App\Models\ClockRecord;
use App\Models\User;

class ClockRecordPolicy
{
    /**
     * An employee may only ever read their own attendance records.
     */
    public function view(User $user, ClockRecord $clockRecord): bool
    {
        return $user->id === $clockRecord->user_id;
    }
}
