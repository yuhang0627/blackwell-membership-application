<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Member extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'nationality',
        'ic_number',
        'referral_code',
        'referred_by',
        'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    // ── Boot ─────────────────────────────────────────────────────────────
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Member $member) {
            if (empty($member->referral_code)) {
                $member->referral_code = static::generateUniqueReferralCode();
            }
        });
    }

    // ── Helpers ───────────────────────────────────────────────────────────
    public static function generateUniqueReferralCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (static::withTrashed()->where('referral_code', $code)->exists());

        return $code;
    }

    // ── Accessors ─────────────────────────────────────────────────────────
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getProfileImageUrlAttribute(): ?string
    {
        $doc = $this->documents()->where('type', 'profile_image')->latest()->first();
        return $doc ? asset('storage/' . $doc->file_path) : null;
    }

    // ── Relationships ─────────────────────────────────────────────────────
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'referred_by');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Member::class, 'referred_by');
    }

    public function rewardAchievers(): HasMany
    {
        return $this->hasMany(RewardAchiever::class);
    }

    // ── Referral Tree ─────────────────────────────────────────────────────
    /**
     * Returns a flat list of all descendants with their level.
     * e.g. [['member' => Member, 'level' => 1], ...]
     */
    public function getReferralTree(int $startLevel = 1): array
    {
        $tree = [];
        $this->buildTree($this->referrals()->with('referrals')->get(), $startLevel, $tree);
        return $tree;
    }

    private function buildTree($referrals, int $level, array &$tree): void
    {
        foreach ($referrals as $referral) {
            $tree[] = ['member' => $referral, 'level' => $level];
            if ($referral->referrals->isNotEmpty()) {
                $this->buildTree($referral->referrals, $level + 1, $tree);
            }
        }
    }

    // ── Scopes ────────────────────────────────────────────────────────────
    public function scopeSearch($query, ?string $search)
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('referral_code', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('ic_number', 'like', "%{$search}%");
        });
    }

    public function scopeFilterByStatus($query, ?string $status)
    {
        return $status ? $query->where('status', $status) : $query;
    }

    public function scopeFilterByReferrer($query, ?string $referralCode)
    {
        if (!$referralCode) {
            return $query;
        }
        $referrer = static::where('referral_code', $referralCode)->first();
        return $referrer
            ? $query->where('referred_by', $referrer->id)
            : $query->whereRaw('1=0');
    }
}
