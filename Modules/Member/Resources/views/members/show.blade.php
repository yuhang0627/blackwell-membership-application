@extends('layouts.app')

@section('title', $member->full_name . ' — Member Detail')
@section('page-title', 'Member Detail')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('members.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to List
    </a>
    <div class="d-flex gap-2">
        <a href="{{ route('members.edit', ['member' => $member->getKey()]) }}" class="btn btn-sm btn-warning">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <form action="{{ route('members.destroy', ['member' => $member->getKey()]) }}" method="POST"
              onsubmit="return confirm('Delete this member?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-danger"><i class="bi bi-trash me-1"></i> Delete</button>
        </form>
    </div>
</div>

<div class="row g-3">

    {{-- Personal Info --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-person-badge me-1"></i> Personal Information
            </div>
            <div class="card-body">
                {{-- Profile Image --}}
                @if($member->profile_image_url)
                    <div class="text-center mb-3">
                        <img src="{{ $member->profile_image_url }}" alt="Profile"
                             class="rounded-circle" style="width:100px;height:100px;object-fit:cover;">
                    </div>
                @else
                    <div class="text-center mb-3">
                        <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center text-white"
                             style="width:80px;height:80px;font-size:2rem;">
                            {{ strtoupper(substr($member->first_name, 0, 1)) }}
                        </div>
                    </div>
                @endif

                <table class="table table-sm table-borderless">
                    <tr>
                        <th class="text-muted small" width="40%">Full Name</th>
                        <td class="fw-semibold">{{ $member->full_name }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted small">Email</th>
                        <td>{{ $member->email }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted small">Phone</th>
                        <td>{{ $member->phone ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted small">Date of Birth</th>
                        <td>{{ $member->date_of_birth?->format('d M Y') ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted small">Gender</th>
                        <td>{{ ucfirst($member->gender ?? '—') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted small">Nationality</th>
                        <td>{{ $member->nationality ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted small">IC / Passport</th>
                        <td>{{ $member->ic_number ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted small">Status</th>
                        <td><span class="badge badge-{{ $member->status }}">{{ ucfirst($member->status) }}</span></td>
                    </tr>
                    <tr>
                        <th class="text-muted small">Joined</th>
                        <td>{{ $member->created_at->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    {{-- Referral Info --}}
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-share me-1"></i> Referral Information
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted small">My Referral Code</label>
                    <div class="d-flex align-items-center gap-2">
                        <code class="bg-light px-3 py-2 rounded fs-5 fw-bold">{{ $member->referral_code }}</code>
                        <button class="btn btn-sm btn-outline-secondary"
                                onclick="navigator.clipboard.writeText('{{ $member->referral_code }}')">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                </div>

                @if($member->referrer)
                <div class="mb-3">
                    <label class="form-label text-muted small">Referred By</label>
                    <a href="{{ route('members.show', ['member' => $member->referrer->getKey()]) }}" class="d-flex align-items-center text-decoration-none">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white me-2"
                             style="width:36px;height:36px;font-size:.9rem;">
                            {{ strtoupper(substr($member->referrer->first_name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-semibold text-dark">{{ $member->referrer->full_name }}</div>
                            <small class="text-muted">{{ $member->referrer->referral_code }}</small>
                        </div>
                    </a>
                </div>
                @else
                <p class="text-muted small">This member was not referred by anyone.</p>
                @endif

                <div>
                    <label class="form-label text-muted small">Direct Referrals</label>
                    <div class="fs-4 fw-bold text-primary">{{ $member->referrals->count() }}</div>
                    <small class="text-muted">Total tree members: {{ count($referralTree) }}</small>
                </div>
            </div>
        </div>

        {{-- Rewards --}}
        <div class="card">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-trophy me-1"></i> Rewards Earned
            </div>
            <div class="card-body p-0">
                @if($member->rewardAchievers->isEmpty())
                    <p class="text-muted small p-3 mb-0">No rewards earned yet.</p>
                @else
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Promotion</th>
                            <th>Tier</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($member->rewardAchievers as $r)
                        <tr>
                            <td><small>{{ $r->promotion?->name }}</small></td>
                            <td><span class="badge bg-info text-dark">Tier {{ $r->tier_number }}</span></td>
                            <td class="fw-semibold text-success">USD {{ number_format($r->reward_amount, 2) }}</td>
                            <td><small>{{ $r->achieved_at->format('d M Y') }}</small></td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="2" class="fw-semibold text-end">Total</td>
                            <td class="fw-bold text-success">
                                USD {{ number_format($member->rewardAchievers->sum('reward_amount'), 2) }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                @endif
            </div>
        </div>
    </div>

    {{-- Addresses --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-geo-alt me-1"></i> Addresses
            </div>
            <div class="card-body">
                @forelse($member->addresses as $address)
                <div class="border rounded p-3 mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="badge bg-secondary">{{ $address->addressType?->name ?? 'Unknown' }}</span>
                    </div>
                    <address class="mb-0 small">
                        {{ $address->address_line_1 }}<br>
                        @if($address->address_line_2){{ $address->address_line_2 }}<br>@endif
                        {{ $address->city }}, {{ $address->state }} {{ $address->postcode }}<br>
                        {{ $address->country }}
                    </address>

                    {{-- Proof of Address --}}
                    @php $proof = $address->documents()->where('type','proof_of_address')->latest()->first(); @endphp
                    @if($proof)
                    <div class="mt-2">
                        <a href="{{ $proof->url }}" target="_blank" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-file-earmark me-1"></i> View Proof of Address
                        </a>
                    </div>
                    @endif
                </div>
                @empty
                <p class="text-muted small">No addresses recorded.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>

{{-- Referral Tree --}}
@if(count($referralTree) > 0)
<div class="card mt-3">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-diagram-3 me-1"></i> Referral Tree (from {{ $member->first_name }}'s perspective)
    </div>
    <div class="card-body referral-tree">
        @php
            $grouped = collect($referralTree)->groupBy('level');
        @endphp

        @foreach($grouped as $level => $entries)
        <div class="mb-2">
            <span class="badge bg-primary me-2">Level {{ $level }}</span>
            @foreach($entries as $item)
                <a href="{{ route('members.show', ['member' => $item['member']->getKey()]) }}"
                   class="badge bg-light text-dark border me-1 text-decoration-none py-1 px-2">
                    {{ $item['member']->full_name }}
                    <small class="text-muted">({{ $item['member']->referral_code }})</small>
                </a>
            @endforeach
        </div>
        @endforeach
    </div>
</div>
@endif

@endsection
