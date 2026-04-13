<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Blackwell Membership')</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

    <style>
        body { background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background: #1e2a3a; }
        .sidebar .nav-link { color: #adb5bd; padding: .6rem 1.2rem; border-radius: 6px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background: rgba(255,255,255,.08); }
        .sidebar .nav-link i { margin-right: .5rem; }
        .sidebar-brand { color: #fff; font-size: 1.2rem; font-weight: 700; }
        .main-content { padding: 1.5rem; }
        .card { border: none; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
        .stat-card .card-body { padding: 1.4rem; }
        .stat-card .stat-icon { font-size: 2rem; opacity: .6; }
        .badge-pending   { background: #ffc107; color: #000; }
        .badge-approved  { background: #198754; }
        .badge-rejected  { background: #dc3545; }
        .badge-terminated{ background: #6c757d; }
        .referral-tree ul { list-style: none; padding-left: 1.5rem; border-left: 2px solid #dee2e6; }
        .referral-tree > ul { border: none; padding-left: 0; }
        .referral-tree li { margin: .25rem 0; }
    </style>

    @stack('styles')
</head>
<body>

<div class="container-fluid p-0">
    <div class="row g-0">

        {{-- Sidebar --}}
        <nav class="col-md-2 sidebar d-none d-md-flex flex-column p-3">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-brand text-decoration-none mb-4 d-flex align-items-center">
                <i class="bi bi-people-fill me-2 fs-5"></i> Blackwell
            </a>

            <ul class="nav flex-column gap-1">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}"
                       href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('members.*') ? 'active' : '' }}"
                       href="{{ route('members.index') }}">
                        <i class="bi bi-people"></i> Members
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('rewards.*') ? 'active' : '' }}"
                       href="{{ route('rewards.index') }}">
                        <i class="bi bi-trophy"></i> Reward Report
                    </a>
                </li>
            </ul>
        </nav>

        {{-- Main --}}
        <main class="col-md-10 main-content">
            {{-- Top bar --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0 fw-semibold">@yield('page-title', 'Dashboard')</h5>
                <small class="text-muted">{{ now()->format('d M Y') }}</small>
            </div>

            {{-- Alerts --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>

    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery (required by DataTables) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

@stack('scripts')
</body>
</html>
