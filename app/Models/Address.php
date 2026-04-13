<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Address extends Model
{
    protected $fillable = [
        'member_id',
        'address_type_id',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postcode',
        'country',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function addressType(): BelongsTo
    {
        return $this->belongsTo(AddressType::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function getProofOfAddressAttribute(): ?Document
    {
        return $this->documents()->where('type', 'proof_of_address')->latest()->first();
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address_line_1,
            $this->address_line_2,
            $this->city,
            $this->state,
            $this->postcode,
            $this->country,
        ]);
        return implode(', ', $parts);
    }
}
