<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RewardAchiever extends Model
{
    protected $fillable = [
        'member_id',
        'promotion_id',
        'tier_number',
        'referral_count_at_achievement',
        'reward_amount',
        'achieved_at',
    ];

    protected $casts = [
        'reward_amount' => 'decimal:2',
        'achieved_at'   => 'date',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    public function getTierLabelAttribute(): string
    {
        return "Tier {$this->tier_number}";
    }
}
