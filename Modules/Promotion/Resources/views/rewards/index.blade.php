@extends('layouts.app')

@section('title', 'Reward Report')
@section('page-title', 'Reward Report')

@section('content')

{{-- Filters --}}
<div class="card mb-3">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-funnel me-1"></i> Filters
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('rewards.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">Member</label>
                <select name="member_id" class="form-select form-select-sm">
                    <option value="">All Members</option>
                    @foreach($members as $m)
                    <option value="{{ $m->id }}" @selected(($filters['member_id'] ?? '') == $m->id)>
                        {{ $m->full_name }} ({{ $m->referral_code }})
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Promotion</label>
                <select name="promotion_id" class="form-select form-select-sm">
                    <option value="">All Promotions</option>
                    @foreach($promotions as $p)
                    <option value="{{ $p->id }}" @selected(($filters['promotion_id'] ?? '') == $p->id)>
                        {{ $p->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Date From</label>
                <input type="date" name="date_from" class="form-control form-control-sm"
                       value="{{ $filters['date_from'] ?? '' }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Date To</label>
                <input type="date" name="date_to" class="form-control form-control-sm"
                       value="{{ $filters['date_to'] ?? '' }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('rewards.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-circle"></i> Clear
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Actions --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <small class="text-muted">
        Showing {{ $rewards->firstItem() ?? 0 }}–{{ $rewards->lastItem() ?? 0 }}
        of {{ $rewards->total() }} reward records
    </small>
    <a href="{{ route('rewards.export', request()->query()) }}"
       class="btn btn-sm btn-outline-success">
        <i class="bi bi-download me-1"></i> Export CSV
    </a>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Member</th>
                    <th>Referral Code</th>
                    <th>Promotion</th>
                    <th>Tier</th>
                    <th class="text-end">Referrals at Achievement</th>
                    <th class="text-end">Reward (USD)</th>
                    <th>Achieved On</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rewards as $reward)
                <tr>
                    <td class="text-muted small">{{ $reward->id }}</td>
                    <td>
                        @if($reward->member)
                        <a href="{{ route('members.show', ['member' => $reward->member->getKey()]) }}" class="text-decoration-none fw-semibold">
                            {{ $reward->member->full_name }}
                        </a>
                        <br><small class="text-muted">{{ $reward->member->email }}</small>
                        @else
                        <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if($reward->member)
                        <code>{{ $reward->member->referral_code }}</code>
                        @else —
                        @endif
                    </td>
                    <td>
                        <span>{{ $reward->promotion?->name ?? '—' }}</span>
                        @if($reward->promotion)
                        <br><small class="text-muted">
                            {{ $reward->promotion->start_date->format('d M Y') }}
                            – {{ $reward->promotion->end_date->format('d M Y') }}
                        </small>
                        @endif
                    </td>
                    <td>
                        @php
                            $tierColors = [1 => 'info', 2 => 'primary', 3 => 'warning', 4 => 'danger'];
                            $color = $tierColors[$reward->tier_number] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $color }} text-{{ $reward->tier_number === 3 ? 'dark' : 'white' }}">
                            Tier {{ $reward->tier_number }}
                        </span>
                    </td>
                    <td class="text-end">{{ number_format($reward->referral_count_at_achievement) }}</td>
                    <td class="text-end fw-bold text-success">
                        {{ number_format($reward->reward_amount, 2) }}
                    </td>
                    <td>{{ $reward->achieved_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                        No reward records found for the selected filters.
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($rewards->count() > 0)
            <tfoot class="table-light fw-semibold">
                <tr>
                    <td colspan="6" class="text-end">Page Total:</td>
                    <td class="text-end text-success">
                        USD {{ number_format($rewards->sum('reward_amount'), 2) }}
                    </td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    @if($rewards->hasPages())
    <div class="card-footer d-flex justify-content-between align-items-center">
        <small class="text-muted">Total records: {{ $rewards->total() }}</small>
        {{ $rewards->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

{{-- Tier Reference --}}
<div class="card mt-3">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-info-circle me-1"></i> Reward Tier Reference
    </div>
    <div class="card-body">
        <div class="row g-2 text-center">
            <div class="col-sm-3">
                <div class="border rounded p-3">
                    <span class="badge bg-info mb-2">Tier 1</span>
                    <div class="fw-bold">10 referrals</div>
                    <div class="text-success fw-semibold">USD 100</div>
                    <small class="text-muted">One-time</small>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="border rounded p-3">
                    <span class="badge bg-primary mb-2">Tier 2</span>
                    <div class="fw-bold">50 referrals</div>
                    <div class="text-success fw-semibold">USD 500</div>
                    <small class="text-muted">One-time</small>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="border rounded p-3">
                    <span class="badge bg-warning text-dark mb-2">Tier 3</span>
                    <div class="fw-bold">100 referrals</div>
                    <div class="text-success fw-semibold">USD 1,000</div>
                    <small class="text-muted">One-time</small>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="border rounded p-3">
                    <span class="badge bg-danger mb-2">Tier 4</span>
                    <div class="fw-bold">Every 10 beyond 100</div>
                    <div class="text-success fw-semibold">USD 150</div>
                    <small class="text-muted">Recurring</small>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
