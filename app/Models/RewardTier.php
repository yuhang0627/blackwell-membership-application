<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RewardTier extends Model
{
    protected $fillable = [
        'promotion_id',
        'tier_number',
        'referral_count',
        'reward_amount',
        'type',
        'recurring_interval',
    ];

    protected $casts = [
        'reward_amount' => 'decimal:2',
    ];

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    public function isRecurring(): bool
    {
        return $this->type === 'recurring';
    }
}
