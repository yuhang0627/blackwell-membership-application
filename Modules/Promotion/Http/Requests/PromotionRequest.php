<?php

namespace Modules\Promotion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PromotionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                   => ['required', 'string', 'max:255'],
            'description'            => ['nullable', 'string'],
            'start_date'             => ['required', 'date'],
            'end_date'               => ['required', 'date', 'after_or_equal:start_date'],
            'status'                 => ['required', Rule::in(['draft', 'active', 'inactive', 'ended'])],
            'tiers'                  => ['required', 'array', 'size:4'],
            'tiers.*.tier_number'    => ['required', 'integer', 'between:1,4'],
            'tiers.*.referral_count' => ['required', 'integer', 'min:1'],
            'tiers.*.reward_amount'  => ['required', 'numeric', 'min:0'],
            'tiers.*.type'           => ['required', Rule::in(['fixed', 'recurring'])],
            'tiers.*.recurring_interval' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
