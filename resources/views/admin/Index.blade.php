@extends('admin.layout')
@section('title', '— Dashboard')

@section('content')
<div class="page-header">
    <h4><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h4>
    <small class="text-muted">{{ now()->format('d/m/Y à H:i') }}</small>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#1a2b4a,#2d4a8a);">
            <h2>{{ $stats['lignes_today'] }}</h2>
            <p><i class="fas fa-box me-1"></i>Pièces aujourd'hui</p>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#198754,#20c997);">
            <h2>{{ $stats['recuperees_today'] }}</h2>
            <p><i class="fas fa-check me-1"></i>Récupérées aujourd'hui</p>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#dc3545,#fd7e14);">
            <h2>{{ $stats['problemes_today'] }}</h2>
            <p><i class="fas fa-exclamation me-1"></i>Problèmes aujourd'hui</p>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#6f42c1,#0dcaf0);">
            <h2>{{ $stats['lignes_week'] }}</h2>
            <p><i class="fas fa-calendar-week me-1"></i>Pièces cette semaine</p>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <a href="{{ route('admin.chauffeurs') }}" class="text-decoration-none">
            <div class="card h-100">
                <div class="card-body text-center py-4">
                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                    <h3 class="fw-bold text-primary">{{ $stats['chauffeurs'] }}</h3>
                    <p class="text-muted mb-0">Chauffeurs actifs</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('admin.sites') }}" class="text-decoration-none">
            <div class="card h-100">
                <div class="card-body text-center py-4">
                    <i class="fas fa-store fa-2x text-success mb-2"></i>
                    <h3 class="fw-bold text-success">{{ $stats['sites'] }}</h3>
                    <p class="text-muted mb-0">Sites connectés</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('admin.sync') }}" class="text-decoration-none">
            <div class="card h-100">
                <div class="card-body text-center py-4">
                    <i class="fas fa-industry fa-2x text-warning mb-2"></i>
                    <h3 class="fw-bold text-warning">{{ $stats['fournisseurs'] }}</h3>
                    <p class="text-muted mb-0">Fournisseurs synchronisés</p>
                </div>
            </div>
        </a>
    </div>
</div>

{{-- Accès rapides --}}
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3">
                <i class="fas fa-bolt me-2 text-warning"></i>Accès rapides
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('planning.index') }}" class="btn btn-primary">
                        <i class="fas fa-calendar-day me-1"></i>Planning du jour
                    </a>
                    <a href="{{ route('admin.chauffeurs') }}" class="btn btn-outline-primary">
                        <i class="fas fa-user-plus me-1"></i>Ajouter un chauffeur
                    </a>
                    <a href="{{ route('admin.sync') }}" class="btn btn-outline-success">
                        <i class="fas fa-sync me-1"></i>Synchroniser maintenant
                    </a>
                    <a href="{{ route('planning.rapport') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-chart-bar me-1"></i>Voir le rapport
                    </a>
                    <a href="{{ route('admin.cleanup') }}" class="btn btn-outline-danger">
                        <i class="fas fa-trash me-1"></i>Nettoyage
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Dernières lignes --}}
<div class="card">
    <div class="card-header py-3">
        <i class="fas fa-history me-2"></i>10 dernières pièces ajoutées
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Référence</th>
                    <th>Désignation</th>
                    <th>Facture</th>
                    <th>Magasin</th>
                    <th>Chauffeur</th>
                    <th>Créneau</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dernieresLignes as $l)
                @php
                    $colors = ['en_attente'=>'secondary','assigné'=>'primary','en_route'=>'info','recupere'=>'success','au_magasin'=>'dark','probleme'=>'danger'];
                    $labels = ['en_attente'=>'En attente','assigné'=>'Assigné','en_route'=>'En route','recupere'=>'Récupéré','au_magasin'=>'Au magasin','probleme'=>'Problème'];
                @endphp
                <tr>
                    <td style="white-space:nowrap;">{{ $l->date_tournee->format('d/m/Y') }}</td>
                    <td><code style="font-size:0.8rem;">{{ $l->article_code }}</code></td>
                    <td style="font-size:0.8rem; max-width:180px;">{{ \Illuminate\Support\Str::limit($l->article_name, 35) }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $l->source_numdoc }}</span></td>
                    <td><span class="badge bg-primary" style="font-size:0.68rem;">{{ optional($l->site)->name ?? '-' }}</span></td>
                    <td style="font-size:0.82rem;">{{ optional($l->chauffeur)->name ?? '—' }}</td>
                    <td style="font-size:0.78rem;">{{ $l->slot === 'matin' ? '🌅 Matin' : '🌇 AM' }}</td>
                    <td><span class="badge bg-{{ $colors[$l->statut] ?? 'secondary' }}" style="font-size:0.68rem;">{{ $labels[$l->statut] ?? $l->statut }}</span></td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-3">Aucune donnée</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection