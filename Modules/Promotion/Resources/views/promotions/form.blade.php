@extends('layouts.app')

@php
    $isEdit = $promotion->exists;
@endphp

@section('title', $isEdit ? 'Edit Promotion' : 'Create Promotion')
@section('page-title', $isEdit ? 'Edit Promotion' : 'Create Promotion')

@section('content')

<a href="{{ route('promotions.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
    <i class="bi bi-arrow-left me-1"></i> Back to Promotions
</a>

<form action="{{ $isEdit ? route('promotions.update', $promotion) : route('promotions.store') }}" method="POST">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header bg-white fw-semibold">Promotion Details</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $promotion->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="4"
                                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $promotion->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                                   value="{{ old('start_date', optional($promotion->start_date)->format('Y-m-d') ?? $promotion->start_date) }}" required>
                            @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror"
                                   value="{{ old('end_date', optional($promotion->end_date)->format('Y-m-d') ?? $promotion->end_date) }}" required>
                            @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            @foreach(['draft', 'active', 'inactive', 'ended'] as $status)
                                <option value="{{ $status }}" @selected(old('status', $promotion->status) === $status)>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card">
                <div class="card-header bg-white fw-semibold">Reward Tiers</div>
                <div class="card-body">
                    @foreach($tiers as $index => $tier)
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">Tier {{ $tier['tier_number'] }}</h6>
                            <small class="text-muted">
                                {{ $tier['type'] === 'recurring' ? 'Recurring milestone' : 'One-time milestone' }}
                            </small>
                        </div>

                        <input type="hidden" name="tiers[{{ $index }}][tier_number]" value="{{ old("tiers.$index.tier_number", $tier['tier_number']) }}">
                        <input type="hidden" name="tiers[{{ $index }}][type]" value="{{ old("tiers.$index.type", $tier['type']) }}">

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Referral Count</label>
                                <input type="number" min="1" name="tiers[{{ $index }}][referral_count]"
                                       class="form-control @error("tiers.$index.referral_count") is-invalid @enderror"
                                       value="{{ old("tiers.$index.referral_count", $tier['referral_count']) }}" required>
                                @error("tiers.$index.referral_count")<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Reward Amount (USD)</label>
                                <input type="number" min="0" step="0.01" name="tiers[{{ $index }}][reward_amount]"
                                       class="form-control @error("tiers.$index.reward_amount") is-invalid @enderror"
                                       value="{{ old("tiers.$index.reward_amount", $tier['reward_amount']) }}" required>
                                @error("tiers.$index.reward_amount")<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Recurring Interval</label>
                                <input type="number" min="1" name="tiers[{{ $index }}][recurring_interval]"
                                       class="form-control @error("tiers.$index.recurring_interval") is-invalid @enderror"
                                       value="{{ old("tiers.$index.recurring_interval", $tier['recurring_interval']) }}"
                                       {{ $tier['type'] === 'recurring' ? '' : 'readonly' }}>
                                @error("tiers.$index.recurring_interval")<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-3">
        <a href="{{ route('promotions.index') }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i> {{ $isEdit ? 'Update Promotion' : 'Create Promotion' }}
        </button>
    </div>
</form>

@endsection
