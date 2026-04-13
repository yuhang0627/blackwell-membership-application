@extends('layouts.app')

@section('title', 'Register Member')
@section('page-title', 'Register New Member')

@section('content')

<a href="{{ route('members.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
    <i class="bi bi-arrow-left me-1"></i> Back to List
</a>

<form action="{{ route('members.store') }}" method="POST" enctype="multipart/form-data">
@csrf

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
                               value="{{ old('first_name') }}" required>
                        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                               value="{{ old('last_name') }}" required>
                        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                               value="{{ old('phone') }}" placeholder="+60-12-3456789">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror"
                               value="{{ old('date_of_birth') }}">
                        @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                            <option value="">— Select —</option>
                            @foreach(['male','female','other'] as $g)
                            <option value="{{ $g }}" @selected(old('gender') === $g)>{{ ucfirst($g) }}</option>
                            @endforeach
                        </select>
                        @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            @foreach(['pending','approved','rejected','terminated'] as $s)
                            <option value="{{ $s }}" @selected(old('status', 'pending') === $s)>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nationality</label>
                        <input type="text" name="nationality" class="form-control @error('nationality') is-invalid @enderror"
                               value="{{ old('nationality', 'Malaysian') }}">
                        @error('nationality')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">IC / Passport Number</label>
                        <input type="text" name="ic_number" class="form-control @error('ic_number') is-invalid @enderror"
                               value="{{ old('ic_number') }}" placeholder="YYMMDD-SS-NNNN">
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
                {{-- Address rows are injected by JS, also render old() errors --}}
            </div>
            <p id="noAddressMsg" class="text-muted small text-center pb-3">
                No addresses added yet. Click "Add Address" to add one.
            </p>
        </div>
    </div>

    {{-- Right sidebar --}}
    <div class="col-lg-4">
        {{-- Profile Image --}}
        <div class="card mb-3">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-image me-1"></i> Profile Image
            </div>
            <div class="card-body text-center">
                <div class="mb-2">
                    <img id="profilePreview" src="#" alt="Preview"
                         class="rounded-circle d-none"
                         style="width:100px;height:100px;object-fit:cover;">
                    <div id="profilePlaceholder" class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center text-muted"
                         style="width:100px;height:100px;font-size:2rem;">
                        <i class="bi bi-person"></i>
                    </div>
                </div>
                <input type="file" name="profile_image" id="profileInput"
                       class="form-control @error('profile_image') is-invalid @enderror"
                       accept="image/*">
                <div class="form-text">JPG/PNG/GIF/WebP · max 2 MB</div>
                @error('profile_image')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- Referral --}}
        <div class="card">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-share me-1"></i> Referral
            </div>
            <div class="card-body">
                <label class="form-label">Referred By (Referral Code)</label>
                <input type="text" name="referrer_code"
                       class="form-control @error('referrer_code') is-invalid @enderror"
                       value="{{ old('referrer_code') }}"
                       placeholder="Enter referrer's code">
                <div class="form-text">Leave blank if no referrer.</div>
                @error('referrer_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

</div>

<div class="d-flex justify-content-end gap-2 mt-3">
    <a href="{{ route('members.index') }}" class="btn btn-outline-secondary">Cancel</a>
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-person-check me-1"></i> Register Member
    </button>
</div>

</form>

{{-- Address template (hidden) --}}
<template id="addressTemplate">
    <div class="border rounded p-3 mb-3 address-item">
        <div class="d-flex justify-content-between mb-2">
            <span class="fw-semibold small text-muted">Address #__INDEX__</span>
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
let addressIndex = 0;

function updateVisibility() {
    const count = document.querySelectorAll('.address-item').length;
    document.getElementById('noAddressMsg').classList.toggle('d-none', count > 0);
}

document.getElementById('addAddressBtn').addEventListener('click', function () {
    const template = document.getElementById('addressTemplate').innerHTML
        .replaceAll('__INDEX__', addressIndex);
    document.getElementById('addressContainer').insertAdjacentHTML('beforeend', template);
    addressIndex++;
    updateVisibility();
});

document.getElementById('addressContainer').addEventListener('click', function (e) {
    if (e.target.closest('.remove-address')) {
        e.target.closest('.address-item').remove();
        updateVisibility();
    }
});

// Profile image preview
document.getElementById('profileInput').addEventListener('change', function () {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('profilePreview').src = e.target.result;
            document.getElementById('profilePreview').classList.remove('d-none');
            document.getElementById('profilePlaceholder').classList.add('d-none');
        };
        reader.readAsDataURL(file);
    }
});

updateVisibility();
</script>
@endpush
