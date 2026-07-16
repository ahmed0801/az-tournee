<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AZ Tournée — Admin @yield('title')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f0f4f8; font-family: 'Segoe UI', sans-serif; }
        .sidebar {
            width: 220px; min-height: 100vh;
            background: linear-gradient(180deg, #1a2b4a 0%, #0d1a2e 100%);
            position: fixed; top: 0; left: 0; z-index: 100;
            padding: 20px 0; overflow-y: auto;
        }
        .sidebar-brand {
            color: white; font-weight: 700; font-size: 1rem;
            padding: 0 20px 20px; border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 10px;
        }
        .sidebar-brand .logo-icon {
            width: 36px; height: 36px; border-radius: 8px;
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1rem; margin-bottom: 8px;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.65); padding: 10px 20px;
            font-size: 0.84rem; display: flex; align-items: center; gap: 10px;
            transition: all 0.15s; border-left: 3px solid transparent;
            text-decoration: none;
        }
        .sidebar .nav-link:hover { color: white; background: rgba(255,255,255,0.07); border-left-color: #4d9fff; }
        .sidebar .nav-link.active { color: white; background: rgba(13,110,253,0.15); border-left-color: #0d6efd; }
        .sidebar .nav-link i { width: 16px; text-align: center; font-size: 0.82rem; }
        .sidebar-section {
            padding: 10px 20px 4px; font-size: 0.62rem;
            text-transform: uppercase; letter-spacing: 1.5px;
            color: rgba(255,255,255,0.3); margin-top: 6px;
        }
        .main-content { margin-left: 220px; padding: 28px; }
        .page-header { margin-bottom: 24px; }
        .page-header h4 { font-weight: 700; color: #1a2b4a; margin: 0; font-size: 1.25rem; }
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); }
        .card-header {
            background: white; border-bottom: 1px solid #f0f4f8; font-weight: 700;
            border-radius: 12px 12px 0 0 !important; padding: 14px 20px;
        }
        .stat-card { border-radius: 12px; padding: 20px; color: white; }
        .stat-card h2 { font-size: 2.2rem; font-weight: 700; margin: 0; }
        .stat-card p { margin: 4px 0 0; opacity: 0.85; font-size: 0.8rem; }
        .alert { border-radius: 10px; }
        /* Fix modal backdrop */
        .modal-backdrop { z-index: 1040 !important; }
        .modal { z-index: 1050 !important; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand">
        <div class="logo-icon mb-2">
            <i class="fas fa-route text-white"></i>
        </div>
        <div>AZ Tournée</div>
        <small style="font-weight:400; font-size:0.7rem; opacity:0.5;">Administration</small>
    </div>

    <div class="sidebar-section">Navigation</div>
    <a href="{{ route('planning.index') }}" class="nav-link">
        <i class="fas fa-calendar-day"></i> Planning du jour
    </a>
    <a href="{{ route('planning.rapport') }}" class="nav-link">
        <i class="fas fa-chart-bar"></i> Rapport
    </a>
    <a href="{{ route('chauffeur.login') }}" class="nav-link" target="_blank">
        <i class="fas fa-mobile-alt"></i> Interface Chauffeur
    </a>

    <div class="sidebar-section">Administration</div>
    <a href="{{ route('admin.index') }}"
       class="nav-link {{ request()->routeIs('admin.index') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>
    <a href="{{ route('admin.chauffeurs') }}"
       class="nav-link {{ request()->routeIs('admin.chauffeurs*') ? 'active' : '' }}">
        <i class="fas fa-users"></i> Chauffeurs
    </a>
    <a href="{{ route('admin.sites') }}"
       class="nav-link {{ request()->routeIs('admin.sites*') ? 'active' : '' }}">
        <i class="fas fa-store"></i> Sites / Magasins
    </a>
    <a href="{{ route('admin.sync') }}"
       class="nav-link {{ request()->routeIs('admin.sync*') ? 'active' : '' }}">
        <i class="fas fa-sync"></i> Synchronisation
    </a>
    <a href="{{ route('admin.cleanup') }}"
       class="nav-link {{ request()->routeIs('admin.cleanup*') ? 'active' : '' }}">
        <i class="fas fa-trash-alt"></i> Nettoyage
    </a>
    <a href="{{ route('admin.parametres.index') }}"
       class="nav-link {{ request()->routeIs('admin.parametres*') ? 'active' : '' }}">
        <i class="fas fa-sliders-h"></i> Paramètres
    </a>
</div>

<div class="main-content">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</div>

{{-- Bootstrap JS DOIT être chargé avant les modals --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // S'assurer que tous les modals fonctionnent
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser tous les modals Bootstrap
        var modals = document.querySelectorAll('.modal');
        modals.forEach(function(modal) {
            new bootstrap.Modal(modal, { keyboard: true, backdrop: true });
        });
    });
</script>
@yield('scripts')
</body>
</html>