@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-2">
        <div class="card stat-card text-white bg-primary h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="fs-4 fw-bold stat-value">{{ number_format($stats['total_members']) }}</div>
                    <small class="stat-label">Total Members</small>
                </div>
                <i class="bi bi-people stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-2">
        <div class="card stat-card text-white bg-success h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="fs-4 fw-bold stat-value">{{ number_format($stats['approved']) }}</div>
                    <small class="stat-label">Approved</small>
                </div>
                <i class="bi bi-person-check stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-2">
        <div class="card stat-card text-dark bg-warning h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="fs-4 fw-bold stat-value">{{ number_format($stats['pending']) }}</div>
                    <small class="stat-label">Pending</small>
                </div>
                <i class="bi bi-hourglass stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-2">
        <div class="card stat-card text-white bg-secondary h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="fs-4 fw-bold stat-value">{{ number_format($stats['terminated']) }}</div>
                    <small class="stat-label">Terminated</small>
                </div>
                <i class="bi bi-person-x stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-2">
        <div class="card stat-card text-white h-100" style="background:#0dcaf0">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="fs-4 fw-bold stat-value">{{ $stats['active_promos'] }}</div>
                    <small class="stat-label">Active Promos</small>
                </div>
                <i class="bi bi-megaphone stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-2">
        <div class="card stat-card text-white bg-danger h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="fs-4 fw-bold stat-value">USD {{ number_format($stats['total_rewards'], 2) }}</div>
                    <small class="stat-label">Rewards Issued</small>
                </div>
                <i class="bi bi-trophy stat-icon"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Recent Members --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom">
                <span class="fw-semibold"><i class="bi bi-people me-1"></i> Recent Members</span>
                <a href="{{ route('members.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Referral Code</th>
                            <th>Status</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentMembers as $m)
                        <tr>
                            <td>
                                <a href="{{ route('members.show', ['member' => $m->getKey()]) }}" class="text-decoration-none fw-semibold">
                                    {{ $m->full_name }}
                                </a>
                                @if($m->referrer)
                                    <br><small class="text-muted">via {{ $m->referrer->full_name }}</small>
                                @endif
                            </td>
                            <td><code>{{ $m->referral_code }}</code></td>
                            <td>
                                <span class="badge badge-{{ $m->status }}">{{ ucfirst($m->status) }}</span>
                            </td>
                            <td><small>{{ $m->created_at->format('d M Y') }}</small></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">No members yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Recent Rewards --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom">
                <span class="fw-semibold"><i class="bi bi-trophy me-1"></i> Recent Rewards</span>
                <a href="{{ route('rewards.index') }}" class="btn btn-sm btn-outline-success">View All</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Member</th>
                            <th>Promotion</th>
                            <th>Tier</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentRewards as $r)
                        <tr>
                            <td>{{ $r->member?->full_name ?? '—' }}</td>
                            <td><small>{{ $r->promotion?->name ?? '—' }}</small></td>
                            <td><span class="badge bg-info text-dark">Tier {{ $r->tier_number }}</span></td>
                            <td class="fw-semibold text-success">USD {{ number_format($r->reward_amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">No rewards yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
