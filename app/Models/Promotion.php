<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promotion extends Model
{
    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function rewardTiers(): HasMany
    {
        return $this->hasMany(RewardTier::class)->orderBy('tier_number');
    }

    public function rewardAchievers(): HasMany
    {
        return $this->hasMany(RewardAchiever::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && now()->startOfDay()->between($this->start_date, $this->end_date);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where('start_date', '<=', now())
                     ->where('end_date', '>=', now());
    }
}
