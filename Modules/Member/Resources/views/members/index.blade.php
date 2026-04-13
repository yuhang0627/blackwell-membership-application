@extends('layouts.app')

@section('title', 'Members')
@section('page-title', 'Member List')

@section('content')

{{-- Actions bar --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <div></div>
    <div class="d-flex gap-2">
        <a href="{{ route('members.export', request()->query()) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-download me-1"></i> Export CSV
        </a>
        <a href="{{ route('members.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-person-plus me-1"></i> Register Member
        </a>
    </div>
</div>

{{-- Search / Filters --}}
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('members.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small mb-1">Search (name / email / phone / IC)</label>
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Search…" value="{{ $filters['search'] ?? '' }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Referral Code</label>
                <input type="text" name="referral_code" class="form-control form-control-sm"
                       placeholder="Referrer's code" value="{{ $filters['referral_code'] ?? '' }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    @foreach(['pending','approved','rejected','terminated'] as $s)
                        <option value="{{ $s }}" @selected(($filters['status'] ?? '') === $s)>
                            {{ ucfirst($s) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('members.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-circle"></i> Clear
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0" id="membersTable">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Referral Code</th>
                    <th>Referred By</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $member)
                <tr>
                    <td class="text-muted small">{{ $member->id }}</td>
                    <td>
                        <div class="fw-semibold">{{ $member->full_name }}</div>
                        @if($member->ic_number)
                            <small class="text-muted">{{ $member->ic_number }}</small>
                        @endif
                    </td>
                    <td>{{ $member->email }}</td>
                    <td>{{ $member->phone ?? '—' }}</td>
                    <td><code class="bg-light px-2 py-1 rounded">{{ $member->referral_code }}</code></td>
                    <td>
                        @if($member->referrer)
                            <a href="{{ route('members.show', ['member' => $member->referrer->getKey()]) }}" class="text-decoration-none">
                                {{ $member->referrer->full_name }}
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-{{ $member->status }}">{{ ucfirst($member->status) }}</span>
                    </td>
                    <td><small>{{ $member->created_at->format('d M Y') }}</small></td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('members.show', ['member' => $member->getKey()]) }}"
                               class="btn btn-outline-info" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('members.edit', ['member' => $member->getKey()]) }}"
                               class="btn btn-outline-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-outline-danger btn-delete"
                                    data-id="{{ $member->id }}"
                                    data-name="{{ $member->full_name }}"
                                    title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        {{-- Hidden delete form --}}
                        <form id="delete-form-{{ $member->getKey() }}"
                              action="{{ route('members.destroy', ['member' => $member->getKey()]) }}"
                              method="POST" class="d-none">
                            @csrf @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-4 text-muted">No members found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($members->hasPages())
    <div class="card-footer d-flex justify-content-between align-items-center">
        <small class="text-muted">
            Showing {{ $members->firstItem() }}–{{ $members->lastItem() }} of {{ $members->total() }} members
        </small>
        {{ $members->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {
    // Delete confirmation
    $(document).on('click', '.btn-delete', function () {
        const id   = $(this).data('id');
        const name = $(this).data('name');
        if (confirm(`Delete member "${name}"? This action cannot be undone.`)) {
            $(`#delete-form-${id}`).submit();
        }
    });
});
</script>
@endpush
