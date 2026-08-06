<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClockRecord extends Model
{
    /** @use HasFactory<\Database\Factories\ClockRecordFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'clocked_in_at',
        'clocked_out_at',
    ];

    protected $casts = [
        'clocked_in_at'  => 'datetime',
        'clocked_out_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
