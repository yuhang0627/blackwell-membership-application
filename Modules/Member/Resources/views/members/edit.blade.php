@extends('layouts.app')

@section('title', 'Edit ' . $member->full_name)
@section('page-title', 'Edit Member')

@section('content')

<a href="{{ route('members.show', ['member' => $member->getKey()]) }}" class="btn btn-sm btn-outline-secondary mb-3">
    <i class="bi bi-arrow-left me-1"></i> Back to Detail
</a>

<form action="{{ route('members.update', ['member' => $member->getKey()]) }}" method="POST" enctype="multipart/form-data">
@csrf @method('PUT')

<div class="row g-3">

    {{-- Personal Details --}}
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-person me-1"></i> Personal Details
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                               value="{{ old('first_name', $member->first_name) }}" required>
                        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                               value="{{ old('last_name', $member->last_name) }}" required>
                        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $member->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                               value="{{ old('phone', $member->phone) }}">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror"
                               value="{{ old('date_of_birth', $member->date_of_birth?->format('Y-m-d')) }}">
                        @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                            <option value="">— Select —</option>
                            @foreach(['male','female','other'] as $g)
                            <option value="{{ $g }}" @selected(old('gender', $member->gender) === $g)>{{ ucfirst($g) }}</option>
                            @endforeach
                        </select>
                        @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            @foreach(['pending','approved','rejected','terminated'] as $s)
                            <option value="{{ $s }}" @selected(old('status', $member->status) === $s)>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nationality</label>
                        <input type="text" name="nationality" class="form-control @error('nationality') is-invalid @enderror"
                               value="{{ old('nationality', $member->nationality) }}">
                        @error('nationality')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">IC / Passport Number</label>
                        <input type="text" name="ic_number" class="form-control @error('ic_number') is-invalid @enderror"
                               value="{{ old('ic_number', $member->ic_number) }}">
                        @error('ic_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Addresses --}}
        <div class="card mb-3">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><i class="bi bi-geo-alt me-1"></i> Addresses</span>
                <button type="button" class="btn btn-sm btn-outline-primary" id="addAddressBtn">
                    <i class="bi bi-plus-circle me-1"></i> Add Address
                </button>
            </div>
            <div class="card-body" id="addressContainer">
                @foreach($member->addresses as $idx => $address)
                <div class="border rounded p-3 mb-3 address-item" data-existing="1">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-semibold small text-muted">Address #{{ $idx + 1 }}</span>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-address">
                            <i class="bi bi-trash"></i> Remove
                        </button>
                    </div>
                    <input type="hidden" name="addresses[{{ $idx }}][id]" value="{{ $address->id }}">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label small">Address Type <span class="text-danger">*</span></label>
                            <select name="addresses[{{ $idx }}][address_type_id]" class="form-select form-select-sm" required>
                                @foreach($addressTypes as $type)
                                <option value="{{ $type->id }}" @selected($type->id == $address->address_type_id)>
                                    {{ $type->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Country</label>
                            <input type="text" name="addresses[{{ $idx }}][country]" class="form-control form-control-sm"
                                   value="{{ $address->country }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Address Line 1 <span class="text-danger">*</span></label>
                            <input type="text" name="addresses[{{ $idx }}][address_line_1]" class="form-control form-control-sm"
                                   value="{{ $address->address_line_1 }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Address Line 2</label>
                            <input type="text" name="addresses[{{ $idx }}][address_line_2]" class="form-control form-control-sm"
                                   value="{{ $address->address_line_2 }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">City <span class="text-danger">*</span></label>
                            <input type="text" name="addresses[{{ $idx }}][city]" class="form-control form-control-sm"
                                   value="{{ $address->city }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">State <span class="text-danger">*</span></label>
                            <input type="text" name="addresses[{{ $idx }}][state]" class="form-control form-control-sm"
                                   value="{{ $address->state }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Postcode <span class="text-danger">*</span></label>
                            <input type="text" name="addresses[{{ $idx }}][postcode]" class="form-control form-control-sm"
                                   value="{{ $address->postcode }}" required>
                        </div>
                        <div class="col-12">
                            @php $proof = $address->documents()->where('type','proof_of_address')->latest()->first(); @endphp
                            <label class="form-label small">
                                Proof of Address
                                @if($proof)
                                    — <a href="{{ $proof->url }}" target="_blank" class="text-info">
                                        <i class="bi bi-file-earmark"></i> Current: {{ $proof->original_name }}
                                    </a>
                                @endif
                            </label>
                            <input type="file" name="addresses[{{ $idx }}][proof_of_address]"
                                   class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text">Upload a new file to replace the existing one.</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Right Sidebar --}}
    <div class="col-lg-4">
        {{-- Profile Image --}}
        <div class="card mb-3">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-image me-1"></i> Profile Image
            </div>
            <div class="card-body text-center">
                <div class="mb-2">
                    @if($member->profile_image_url)
                        <img id="profilePreview" src="{{ $member->profile_image_url }}" alt="Profile"
                             class="rounded-circle"
                             style="width:100px;height:100px;object-fit:cover;">
                    @else
                        <img id="profilePreview" src="#" alt="Preview"
                             class="rounded-circle d-none"
                             style="width:100px;height:100px;object-fit:cover;">
                        <div id="profilePlaceholder" class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center text-muted"
                             style="width:100px;height:100px;font-size:2rem;">
                            <i class="bi bi-person"></i>
                        </div>
                    @endif
                </div>
                <input type="file" name="profile_image" id="profileInput"
                       class="form-control @error('profile_image') is-invalid @enderror"
                       accept="image/*">
                <div class="form-text">Upload to replace current image. Max 2 MB.</div>
                @error('profile_image')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- Referral Info (read-only) --}}
        <div class="card">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-share me-1"></i> Referral Info
            </div>
            <div class="card-body">
                <label class="form-label small text-muted">My Referral Code</label>
                <div><code class="fs-5 fw-bold bg-light px-2 py-1 rounded">{{ $member->referral_code }}</code></div>
                @if($member->referrer)
                <div class="mt-3">
                    <label class="form-label small text-muted">Referred By</label>
                    <div>{{ $member->referrer->full_name }}</div>
                    <small class="text-muted">({{ $member->referrer->referral_code }})</small>
                </div>
                @endif
                <p class="text-muted small mt-3 mb-0">Referral information cannot be changed after registration.</p>
            </div>
        </div>
    </div>

</div>

{{-- Hidden inputs for deleted addresses --}}
<div id="deleteAddressInputs"></div>

<div class="d-flex justify-content-end gap-2 mt-3">
    <a href="{{ route('members.show', ['member' => $member->getKey()]) }}" class="btn btn-outline-secondary">Cancel</a>
    <button type="submit" class="btn btn-warning">
        <i class="bi bi-save me-1"></i> Save Changes
    </button>
</div>

</form>

{{-- Address template for new addresses --}}
<template id="addressTemplate">
    <div class="border rounded p-3 mb-3 address-item">
        <div class="d-flex justify-content-between mb-2">
            <span class="fw-semibold small text-muted">New Address</span>
            <button type="button" class="btn btn-sm btn-outline-danger remove-address">
                <i class="bi bi-trash"></i> Remove
            </button>
        </div>
        <div class="row g-2">
            <div class="col-md-6">
                <label class="form-label small">Address Type <span class="text-danger">*</span></label>
                <select name="addresses[__INDEX__][address_type_id]" class="form-select form-select-sm" required>
                    <option value="">— Select type —</option>
                    @foreach($addressTypes as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label small">Country</label>
                <input type="text" name="addresses[__INDEX__][country]" class="form-control form-control-sm" value="Malaysia">
            </div>
            <div class="col-12">
                <label class="form-label small">Address Line 1 <span class="text-danger">*</span></label>
                <input type="text" name="addresses[__INDEX__][address_line_1]" class="form-control form-control-sm" required>
            </div>
            <div class="col-12">
                <label class="form-label small">Address Line 2</label>
                <input type="text" name="addresses[__INDEX__][address_line_2]" class="form-control form-control-sm">
            </div>
            <div class="col-md-4">
                <label class="form-label small">City <span class="text-danger">*</span></label>
                <input type="text" name="addresses[__INDEX__][city]" class="form-control form-control-sm" required>
            </div>
            <div class="col-md-4">
                <label class="form-label small">State <span class="text-danger">*</span></label>
                <input type="text" name="addresses[__INDEX__][state]" class="form-control form-control-sm" required>
            </div>
            <div class="col-md-4">
                <label class="form-label small">Postcode <span class="text-danger">*</span></label>
                <input type="text" name="addresses[__INDEX__][postcode]" class="form-control form-control-sm" required>
            </div>
            <div class="col-12">
                <label class="form-label small">Proof of Address <span class="text-muted">(PDF/Image · max 5 MB)</span></label>
                <input type="file" name="addresses[__INDEX__][proof_of_address]"
                       class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png">
            </div>
        </div>
    </div>
</template>

@endsection

@push('scripts')
<script>
// Existing addresses start after the last existing index
let addressIndex = {{ $member->addresses->count() }};

document.getElementById('addAddressBtn').addEventListener('click', function () {
    const template = document.getElementById('addressTemplate').innerHTML
        .replaceAll('__INDEX__', addressIndex);
    document.getElementById('addressContainer').insertAdjacentHTML('beforeend', template);
    addressIndex++;
});

document.getElementById('addressContainer').addEventListener('click', function (e) {
    if (e.target.closest('.remove-address')) {
        const item = e.target.closest('.address-item');
        const hiddenId = item.querySelector('input[type="hidden"]');
        if (hiddenId) {
            // Mark existing address for deletion
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'delete_addresses[]';
            input.value = hiddenId.value;
            document.getElementById('deleteAddressInputs').appendChild(input);
        }
        item.remove();
    }
});

// Profile image preview
const profileInput = document.getElementById('profileInput');
if (profileInput) {
    profileInput.addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const preview = document.getElementById('profilePreview');
                preview.src = e.target.result;
                preview.classList.remove('d-none');
                const placeholder = document.getElementById('profilePlaceholder');
                if (placeholder) placeholder.classList.add('d-none');
            };
            reader.readAsDataURL(file);
        }
    });
}
</script>
@endpush
