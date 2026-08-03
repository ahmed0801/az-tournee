<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AZ Tournée — Planning du {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f0f4f8; font-family: 'Segoe UI', sans-serif; }
        .navbar-top { background: linear-gradient(135deg, #1a2b4a, #2d4a8a); }
        .stat-card { border-radius: 10px; padding: 16px; text-align: center; color: white; }
        .stat-card h3 { font-size: 2rem; font-weight: 700; margin: 0; }
        .stat-card p  { font-size: 0.75rem; margin: 4px 0 0; opacity: 0.85; }
        .slot-header { background: linear-gradient(135deg, #1a2b4a, #243b6e); color: white; border-radius: 10px; padding: 14px 20px; margin: 16px 0 8px; }
        .slot-header.apm { background: linear-gradient(135deg, #17403a, #1d5c52); }
        .fourn-card { background: white; border-radius: 10px; margin-bottom: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }
        .fourn-header { background: #f8faff; border-bottom: 1px solid #e8f0fe; padding: 10px 16px; font-weight: 700; color: #1a2b4a; cursor: pointer; }
        .line-row { padding: 12px 16px; border-bottom: 1px solid #f0f4f8; display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
        .line-row:last-child { border-bottom: none; }
        .article-code { font-family: monospace; font-weight: 700; color: #0040c0; background: #eef4ff; padding: 3px 8px; border-radius: 4px; font-size: 0.88rem; border-left: 3px solid #0040c0; }
        .site-badge { font-size: 0.68rem; padding: 2px 7px; border-radius: 10px; background: #e8f0fe; color: #1a2b4a; }
        .statut-select { font-size: 0.78rem; padding: 3px 8px; border-radius: 6px; border: 1px solid #dee2e6; }
        .chauffeur-select { font-size: 0.78rem; padding: 3px 8px; border-radius: 6px; border: 1px solid #dee2e6; min-width: 140px; }
        .badge-statut { font-size: 0.72rem; padding: 3px 8px; border-radius: 8px; }
        .empty-slot { text-align: center; padding: 30px; color: #9bacc4; }
    </style>
</head>
<body>

{{-- ── NAVBAR ───────────────────────────────────────────────────── --}}
<nav class="navbar navbar-top navbar-expand-lg px-4 py-2 mb-3">
    <span class="navbar-brand text-white fw-bold">🚗 AZ Tournée</span>
    <div class="ms-auto d-flex gap-2 align-items-center">
        <a href="{{ route('planning.rapport') }}" class="btn btn-sm btn-outline-light">
            <i class="fas fa-chart-bar me-1"></i> Rapport
        </a>
        <a href="{{ route('chauffeur.login') }}" class="btn btn-sm btn-outline-warning" target="_blank">
            <i class="fas fa-user me-1"></i> Interface Chauffeur
        </a>
    </div>
</nav>

<div class="container-fluid px-4">

    {{-- ── FILTRES ─────────────────────────────────────────────── --}}
    <form method="GET" class="d-flex flex-wrap gap-2 align-items-end mb-3">
        <div>
            <label class="form-label small fw-bold mb-1">Date</label>
            <input type="date" name="date" class="form-control form-control-sm" value="{{ $date }}">
        </div>
        <div>
            <label class="form-label small fw-bold mb-1">Chauffeur</label>
            <select name="chauffeur_id" class="form-select form-select-sm" style="min-width:140px;">
                <option value="">Tous</option>
                @foreach($chauffeurs as $c)
                    <option value="{{ $c->id }}" {{ request('chauffeur_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label small fw-bold mb-1">Magasin</label>
            <select name="site_id" class="form-select form-select-sm" style="min-width:130px;">
                <option value="">Tous</option>
                @foreach($sites as $s)
                    <option value="{{ $s->id }}" {{ request('site_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label small fw-bold mb-1">Statut</label>
            <select name="statut" class="form-select form-select-sm">
                <option value="">Tous</option>
                <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                <option value="assigné"    {{ request('statut') == 'assigné'    ? 'selected' : '' }}>Assigné</option>
                <option value="en_route"   {{ request('statut') == 'en_route'   ? 'selected' : '' }}>En route</option>
                <option value="recupere"   {{ request('statut') == 'recupere'   ? 'selected' : '' }}>Récupéré</option>
                <option value="probleme"   {{ request('statut') == 'probleme'   ? 'selected' : '' }}>Problème</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary btn-sm px-3">
            <i class="fas fa-filter me-1"></i> Filtrer
        </button>
        <!-- <a href="{{ route('planning.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="fas fa-undo me-1"></i> Aujourd'hui
        </a> -->

        <a href="{{ route('planning.index') }}?site_id={{ session('planning_site_id', '') }}" 
   class="btn btn-outline-secondary btn-sm px-3">
    <i class="fas fa-undo me-1"></i> Aujourd'hui
</a>

    </form>

    {{-- ── STATS ───────────────────────────────────────────────── --}}
    <div class="row g-2 mb-3">
        <div class="col"><div class="stat-card" style="background:#6c757d;"><h3>{{ $stats['total'] }}</h3><p>Total</p></div></div>
        <div class="col"><div class="stat-card" style="background:#e67e00;"><h3>{{ $stats['non_assignees'] }}</h3><p>⚠️ Non assignées</p></div></div>
        <div class="col"><div class="stat-card" style="background:#0d6efd;"><h3>{{ $stats['en_attente'] }}</h3><p>En attente</p></div></div>
        <div class="col"><div class="stat-card" style="background:#0dcaf0;"><h3>{{ $stats['assigné'] }}</h3><p>Assignés</p></div></div>
        <div class="col"><div class="stat-card" style="background:#198754;"><h3>{{ $stats['recupere'] }}</h3><p>Récupérés</p></div></div>
        <div class="col"><div class="stat-card" style="background:#dc3545;"><h3>{{ $stats['probleme'] }}</h3><p>Problèmes</p></div></div>
    </div>

    {{-- ── MATIN ───────────────────────────────────────────────── --}}
    @php
        $allCreneaux = [
            '9h-11h'  => ['icon' => '🌅', 'label' => '9h – 11h',  'color' => 'slot-header'],
            '11h-12h' => ['icon' => '🕚', 'label' => '11h – 12h', 'color' => 'slot-header'],
            '13h-14h' => ['icon' => '🌞', 'label' => '13h – 14h', 'color' => 'slot-header apm'],
            '15h-16h' => ['icon' => '🕒', 'label' => '15h – 16h', 'color' => 'slot-header apm'],
            '17h-18h' => ['icon' => '🌇', 'label' => '17h – 18h', 'color' => 'slot-header apm'],
            // Compatibilité anciens slots
            'matin'      => ['icon' => '🌅', 'label' => 'Matin (8h-12h)',      'color' => 'slot-header'],
            'apres_midi' => ['icon' => '🌇', 'label' => 'Après-midi (13h-18h)', 'color' => 'slot-header apm'],
        ];
    @endphp

    @foreach($allCreneaux as $slotKey => $slotInfo)
        @php
            $lignesCreneau = $lignesParCreneau[$slotKey] ?? collect();
        @endphp
        @if($lignesCreneau->isNotEmpty())
        <div class="{{ $slotInfo['color'] }}" style="{{ !$loop->first ? 'margin-top:24px;' : '' }}">
            {{ $slotInfo['icon'] }} Tournée {{ $slotInfo['label'] }}
            <span class="badge bg-white text-dark ms-2">{{ $lignesCreneau->flatten()->count() }} pièce(s)</span>
        </div>
        @foreach($lignesCreneau as $fournisseurName => $lignes)
            @include('planning.partials.fourn-group-dispatcher', [
                'fournisseurName' => $fournisseurName,
                'lignes'          => $lignes,
                'chauffeurs'      => $chauffeurs
            ])
        @endforeach
        @endif
    @endforeach

    @if($lignesParCreneau->isEmpty() || $lignesParCreneau->flatten()->isEmpty())
        <div class="empty-slot">📭 Aucune pièce planifiée pour cette date</div>
    @endif

</div>

<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// Changer le chauffeur d'une ligne
function assignChauffeur(lineId, chauffeurId) {
    fetch('/planning/assign', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({line_id: lineId, chauffeur_id: chauffeurId})
    }).then(r => r.json()).then(d => {
        if (d.success) showToast('Chauffeur assigné', 'success');
    });
}

// Changer le statut d'une ligne
function updateStatut(lineId, statut) {
    fetch('/planning/statut', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({line_id: lineId, statut: statut})
    }).then(r => r.json()).then(d => {
        if (d.success) showToast('Statut mis à jour', 'success');
    });
}

// Toggle groupe fournisseur
function toggleGroup(id) {
    const body = document.getElementById('group-' + id);
    body.style.display = body.style.display === 'none' ? 'block' : 'none';
}

function showToast(msg, type) {
    const t = document.createElement('div');
    t.style.cssText = 'position:fixed;top:16px;right:16px;z-index:9999;';
    t.innerHTML = `<div class="alert alert-${type} shadow mb-0">${msg}</div>`;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 2500);
}

// Auto-refresh toutes les 30 secondes
setTimeout(() => location.reload(), 30000);
</script>
</body>
</html>