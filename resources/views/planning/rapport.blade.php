<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AZ Tournée — Rapport</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f0f4f8; font-family: 'Segoe UI', sans-serif; }
        .navbar-top { background: linear-gradient(135deg, #1a2b4a, #2d4a8a); }
        .section-title {
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6c757d;
            margin: 20px 0 10px;
            padding-bottom: 6px;
            border-bottom: 2px solid #e0e8f0;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 16px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .stat-card h2 { font-size: 2.2rem; font-weight: 700; margin: 0; }
        .stat-card p  { font-size: 0.72rem; color: #6c757d; margin: 4px 0 0; text-transform: uppercase; }
        .progress-bar-custom { height: 8px; border-radius: 4px; }
        .table-rapport th { background: #1a2b4a; color: white; font-size: 0.78rem; font-weight: 600; }
        .table-rapport td { font-size: 0.82rem; vertical-align: middle; }
        .badge-statut { font-size: 0.68rem; padding: 3px 8px; border-radius: 8px; }
        .code-ref { font-family: monospace; font-weight: 700; color: #0040c0; background: #eef4ff; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>

{{-- ── NAVBAR ───────────────────────────────────────────────────── --}}
<nav class="navbar navbar-top navbar-expand-lg px-4 py-2 mb-3">
    <span class="navbar-brand text-white fw-bold">🚗 AZ Tournée — Rapport</span>
    <div class="ms-auto d-flex gap-2">
        <a href="{{ route('planning.index') }}" class="btn btn-sm btn-outline-light">
            <i class="fas fa-arrow-left me-1"></i> Planning
        </a>
    </div>
</nav>

<div class="container-fluid px-4">

    {{-- ── FILTRES DATE ────────────────────────────────────────── --}}
    <form method="GET" class="d-flex flex-wrap gap-2 align-items-end mb-4">
        <div>
            <label class="form-label small fw-bold mb-1">Du</label>
            <input type="date" name="date_from" class="form-control form-control-sm"
                   value="{{ $dateFrom }}">
        </div>
        <div>
            <label class="form-label small fw-bold mb-1">Au</label>
            <input type="date" name="date_to" class="form-control form-control-sm"
                   value="{{ $dateTo }}">
        </div>
        <button type="submit" class="btn btn-primary btn-sm px-3">
            <i class="fas fa-filter me-1"></i>Filtrer
        </button>
        <a href="{{ route('planning.rapport') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="fas fa-undo me-1"></i>Cette semaine
        </a>
    </form>

    {{-- ── STATS GLOBALES ──────────────────────────────────────── --}}
    @php
        $recupere     = $lines->where('statut', 'recupere')->count();
$livreClient  = $lines->where('statut', 'livre_client')->count();
$probleme     = $lines->where('statut', 'probleme')->count();
$enAttente    = $lines->whereIn('statut', ['en_attente', 'assigné', 'en_route'])->count();
    $total        = $lines->count();
$tauxRecup    = $total > 0 ? round((($recupere + $livreClient) / $total) * 100) : 0;
    @endphp

    <div class="row g-3 mb-2">
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <h2 class="text-secondary">{{ $total }}</h2>
                <p>Total pièces</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <h2 class="text-success">{{ $recupere }}</h2>
                <p>Récupérées</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <h2 class="text-danger">{{ $probleme }}</h2>
                <p>Problèmes</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <h2 class="{{ $tauxRecup >= 80 ? 'text-success' : ($tauxRecup >= 50 ? 'text-warning' : 'text-danger') }}">
                    {{ $tauxRecup }}%
                </h2>
                <p>Taux de récupération</p>
            </div>
        </div>
    </div>

    {{-- Barre de progression globale --}}
    @if($total > 0)
    <div class="bg-white rounded-3 p-3 mb-4 shadow-sm">
        <div class="d-flex justify-content-between mb-1" style="font-size:0.78rem;">
            <span class="text-success fw-bold">✅ {{ $recupere }} récupérées</span>
            <span class="text-danger">⚠️ {{ $probleme }} problèmes</span>
            <span class="text-muted">⏳ {{ $enAttente }} en cours</span>
        </div>
        <div class="progress" style="height:10px; border-radius:6px;">
            <div class="progress-bar bg-success" style="width:{{ $tauxRecup }}%;"></div>
            <div class="progress-bar bg-danger"  style="width:{{ $total > 0 ? round($probleme/$total*100) : 0 }}%;"></div>
        </div>
    </div>
    @endif

    {{-- ── STATS PAR JOUR ───────────────────────────────────────── --}}
    <div class="section-title"><i class="fas fa-calendar-alt me-2"></i>Par jour</div>
    <div class="table-responsive mb-4">
        <table class="table table-sm table-hover table-rapport">
            <thead>
                <tr>
                    <th>Date</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">Récupérées</th>
                    <th class="text-center">Problèmes</th>
                    <th class="text-center">En attente</th>
                    <th class="text-center">Taux</th>
                </tr>
            </thead>
            <tbody>
                @forelse($statsByDay as $jour => $s)
                @php $taux = $s['total'] > 0 ? round($s['recupere']/$s['total']*100) : 0; @endphp
                <tr>
                    <td><strong>{{ \Carbon\Carbon::parse($jour)->format('d/m/Y') }}</strong>
                        <small class="text-muted d-block">{{ \Carbon\Carbon::parse($jour)->isoFormat('dddd') }}</small>
                    </td>
                    <td class="text-center">{{ $s['total'] }}</td>
                    <td class="text-center text-success fw-bold">{{ $s['recupere'] }}</td>
                    <td class="text-center text-danger">{{ $s['probleme'] }}</td>
                    <td class="text-center text-muted">{{ $s['en_attente'] }}</td>
                    <td class="text-center">
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-fill" style="height:6px;">
                                <div class="progress-bar {{ $taux >= 80 ? 'bg-success' : ($taux >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                     style="width:{{ $taux }}%;"></div>
                            </div>
                            <span style="font-size:0.75rem; min-width:32px;">{{ $taux }}%</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-3">Aucune donnée sur cette période</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── STATS PAR CHAUFFEUR ─────────────────────────────────── --}}
    <div class="section-title"><i class="fas fa-user me-2"></i>Par chauffeur</div>
    <div class="row g-3 mb-4">
        @forelse($statsByChauffeur as $cid => $s)
        @php $taux = $s['total'] > 0 ? round($s['recupere']/$s['total']*100) : 0; @endphp
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="bg-white rounded-3 p-3 shadow-sm h-100">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div style="background:#1a2b4a; color:white; border-radius:50%; width:36px; height:36px; display:flex; align-items:center; justify-content:center; font-size:0.9rem;">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <div style="font-weight:700; font-size:0.88rem;">{{ $s['name'] }}</div>
                        <div style="font-size:0.72rem; color:#6c757d;">{{ $s['total'] }} pièce(s)</div>
                    </div>
                </div>
                <div class="progress mb-1" style="height:8px; border-radius:4px;">
                    <div class="progress-bar {{ $taux >= 80 ? 'bg-success' : ($taux >= 50 ? 'bg-warning' : 'bg-danger') }}"
                         style="width:{{ $taux }}%;"></div>
                </div>
                <div class="d-flex justify-content-between" style="font-size:0.72rem;">
                    <span class="text-success">✅ {{ $s['recupere'] }}</span>
                    <span class="fw-bold">{{ $taux }}%</span>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-muted text-center py-3">Aucune donnée</div>
        @endforelse
    </div>

    {{-- ── STATS PAR MAGASIN ───────────────────────────────────── --}}
    <div class="section-title"><i class="fas fa-store me-2"></i>Par magasin</div>
    <div class="row g-3 mb-4">
        @forelse($statsBySite as $sid => $s)
        @php $taux = $s['total'] > 0 ? round($s['recupere']/$s['total']*100) : 0; @endphp
        <div class="col-sm-6 col-md-3">
            <div class="bg-white rounded-3 p-3 shadow-sm text-center h-100">
                <div style="font-size:1.4rem; margin-bottom:6px;">🏪</div>
                <div style="font-weight:700; color:#1a2b4a;">{{ $s['name'] }}</div>
                <div style="font-size:1.5rem; font-weight:700; color:{{ $taux >= 80 ? '#28a745' : ($taux >= 50 ? '#ffc107' : '#dc3545') }};">
                    {{ $taux }}%
                </div>
                <div style="font-size:0.72rem; color:#6c757d;">{{ $s['recupere'] }}/{{ $s['total'] }}</div>
                <div class="progress mt-2" style="height:6px;">
                    <div class="progress-bar {{ $taux >= 80 ? 'bg-success' : ($taux >= 50 ? 'bg-warning' : 'bg-danger') }}"
                         style="width:{{ $taux }}%;"></div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-muted text-center py-3">Aucune donnée</div>
        @endforelse
    </div>

    {{-- ── TABLEAU DÉTAIL ──────────────────────────────────────── --}}
    <div class="section-title"><i class="fas fa-list me-2"></i>Détail des lignes</div>
    <div class="table-responsive mb-5">
        <table class="table table-sm table-hover table-bordered table-rapport">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Créneau</th>
                    <th>Magasin</th>
                    <th>Référence</th>
                    <th>Désignation</th>
                    <th class="text-center">Qté</th>
                    <th>Fournisseur</th>
                    <th>Chauffeur</th>
                    <th class="text-center">Statut</th>
                    <th>Scanné à</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lines as $ligne)
                <tr class="{{ $ligne->statut === 'probleme' ? 'table-danger' : ($ligne->statut === 'recupere' ? 'table-success bg-opacity-10' : '') }}">
                    <td style="white-space:nowrap;">{{ $ligne->date_tournee->format('d/m/Y') }}</td>
                    <td style="white-space:nowrap;">
                        @php
                            $slotLabels = [
                                '9h-11h'     => '🌅 9h-11h',
                                '11h-12h'    => '🕚 11h-12h',
                                '13h-14h'    => '🌞 13h-14h',
                                '15h-16h'    => '🕒 15h-16h',
                                '17h-18h'    => '🌇 17h-18h',
                                'matin'      => '🌅 Matin',
                                'apres_midi' => '🌇 AM',
                            ];
                        @endphp
                        {{ $slotLabels[$ligne->slot] ?? $ligne->slot }}
                    </td>
                    <td>
                        <span class="badge bg-primary" style="font-size:0.68rem;">
                            {{ optional($ligne->site)->name }}
                        </span>
                    </td>
                    <td><span class="code-ref">{{ $ligne->article_code }}</span></td>
                    <td style="font-size:0.78rem; max-width:180px;">{{ Str::limit($ligne->article_name, 40) }}</td>
                    <td class="text-center">{{ number_format($ligne->quantity, 0) }}</td>
                    <td style="font-size:0.78rem;">{{ (optional($ligne->fournisseur)->name ?? $ligne->fournisseur_name ?? '-') }}</td>
                    <td style="font-size:0.78rem;">{{ (optional($ligne->chauffeur)->name ?? '—') }}</td>
                    <td class="text-center">
                        @php
                            $colors = [
                                'en_attente' => 'secondary',
                                'assigné'    => 'primary',
                                'en_route'   => 'info',
                                'recupere'   => 'success',
                                'au_magasin' => 'dark',
                                'livre_client'=> 'secondary',
                                'probleme'   => 'danger',
                            ];
                            $labels = [
                                'en_attente' => '⏳ En attente',
                                'assigné'    => '👤 Assigné',
                                'en_route'   => '🚗 En route',
                                'recupere'   => '✅ Récupéré',
                                'au_magasin' => '🏪 Au magasin',
                                'livre_client'=> '🚪 Livré client',
                                'probleme'   => '⚠️ Problème',
                            ];
                        @endphp
                        <span class="badge bg-{{ $colors[$ligne->statut] ?? 'secondary' }} badge-statut"
      style="{{ $ligne->statut === 'livre_client' ? 'background:#6f42c1!important;color:white;' : '' }}">
                            {{ $labels[$ligne->statut] ?? $ligne->statut }}
                        </span>
                        @if($ligne->probleme_notes)
                            <div style="font-size:0.65rem; color:#dc3545; margin-top:2px;">
                                {{ Str::limit($ligne->probleme_notes, 30) }}
                            </div>
                        @endif
                    </td>
                    <td style="font-size:0.75rem; white-space:nowrap;">
                        {{ $ligne->scanned_at ? $ligne->scanned_at->format('H:i') : '—' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center text-muted py-4">
                        Aucune ligne sur cette période
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>