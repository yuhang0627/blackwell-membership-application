<?php

namespace Modules\Member\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name'       => ['required', 'string', 'max:100'],
            'last_name'        => ['required', 'string', 'max:100'],
            'email'            => ['required', 'email', 'max:255', 'unique:members,email'],
            'phone'            => ['nullable', 'string', 'max:30'],
            'date_of_birth'    => ['nullable', 'date', 'before:today'],
            'gender'           => ['nullable', Rule::in(['male', 'female', 'other'])],
            'nationality'      => ['nullable', 'string', 'max:100'],
            'ic_number'        => ['nullable', 'string', 'max:50', 'unique:members,ic_number'],
            'referrer_code'    => ['nullable', 'string', 'exists:members,referral_code'],
            'status'           => ['nullable', Rule::in(['pending', 'approved', 'rejected', 'terminated'])],

            // Profile image
            'profile_image'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],

            // Addresses (array of address objects)
            'addresses'                          => ['nullable', 'array'],
            'addresses.*.address_type_id'        => ['required_with:addresses.*', 'integer', 'exists:address_types,id'],
            'addresses.*.address_line_1'         => ['required_with:addresses.*', 'string', 'max:255'],
            'addresses.*.address_line_2'         => ['nullable', 'string', 'max:255'],
            'addresses.*.city'                   => ['required_with:addresses.*', 'string', 'max:100'],
            'addresses.*.state'                  => ['required_with:addresses.*', 'string', 'max:100'],
            'addresses.*.postcode'               => ['required_with:addresses.*', 'string', 'max:20'],
            'addresses.*.country'                => ['required_with:addresses.*', 'string', 'max:100'],
            'addresses.*.proof_of_address'       => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'referrer_code.exists'          => 'The referral code provided does not match any existing member.',
            'profile_image.image'           => 'Profile image must be a valid image file.',
            'profile_image.max'             => 'Profile image must not exceed 2 MB.',
            'addresses.*.proof_of_address.max' => 'Proof of address must not exceed 5 MB.',
        ];
    }
}
