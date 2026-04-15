@extends('layouts.app')

@section('title', 'Promotions')
@section('page-title', 'Promotion Setup')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted mb-0">Manage promotion periods and the referral reward tiers attached to them.</p>
    <a href="{{ route('promotions.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i> New Promotion
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Period</th>
                    <th>Status</th>
                    <th>Reward Tiers</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($promotions as $promotion)
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $promotion->name }}</div>
                        @if($promotion->description)
                            <small class="text-muted">{{ $promotion->description }}</small>
                        @endif
                    </td>
                    <td>
                        {{ $promotion->start_date->format('d M Y') }} - {{ $promotion->end_date->format('d M Y') }}
                    </td>
                    <td>
                        <span class="badge text-bg-secondary">{{ ucfirst($promotion->status) }}</span>
                    </td>
                    <td>
                        <small class="text-muted">
                            {{ $promotion->rewardTiers->count() }} tier(s) configured
                        </small>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('promotions.edit', $promotion) }}" class="btn btn-outline-warning btn-sm">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">No promotions created yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($promotions->hasPages())
    <div class="card-footer">
        {{ $promotions->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection
