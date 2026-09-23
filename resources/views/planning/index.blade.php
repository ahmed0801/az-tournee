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








        /* ── Recherche en temps réel ─────────────────────────── */
.search-bar {
    background: white;
    border-radius: 12px;
    padding: 12px 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    margin-bottom: 16px;
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}
.search-input {
    border: 1.5px solid #e0e8ff;
    border-radius: 8px;
    padding: 7px 14px 7px 36px;
    font-size: 0.85rem;
    outline: none;
    transition: border-color 0.2s;
    background: #f8fbff;
    min-width: 200px;
    flex: 1;
}
.search-input:focus { border-color: #0d6efd; background: white; }
.search-wrap { position: relative; flex: 1; min-width: 180px; }
.search-wrap i { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: #9bacc4; font-size: 0.8rem; }
.search-label { font-size: 0.72rem; font-weight: 700; color: #6c8aad; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
#search-count { font-size: 0.78rem; color: #6c757d; white-space: nowrap; }
.line-row.hidden { display: none !important; }
.fourn-card.all-hidden .fourn-header { opacity: 0.4; }
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

    {{-- Raccourcis rapides --}}
    <div class="d-flex gap-1 align-items-center me-2">
        <a href="{{ route('planning.index') }}?site_id={{ session('planning_site_id','') }}"
           class="btn btn-sm {{ !$modeHistorique ? 'btn-primary' : 'btn-outline-secondary' }}">
            📅 Aujourd'hui
        </a>
        <a href="?date={{ today()->subDay()->format('Y-m-d') }}&site_id={{ session('planning_site_id','') }}"
           class="btn btn-sm btn-outline-secondary">Hier</a>
        <a href="?date_from={{ today()->startOfWeek()->format('Y-m-d') }}&date_to={{ today()->format('Y-m-d') }}&site_id={{ session('planning_site_id','') }}&search_article={{ request('search_article') }}"
           class="btn btn-sm btn-outline-secondary">Cette semaine</a>
        <a href="?date_from={{ today()->startOfMonth()->format('Y-m-d') }}&date_to={{ today()->format('Y-m-d') }}&site_id={{ session('planning_site_id','') }}&search_article={{ request('search_article') }}"
           class="btn btn-sm btn-outline-secondary">Ce mois</a>
    </div>

    {{-- Date / plage --}}
    @if($modeHistorique)
        <div>
            <label class="form-label small fw-bold mb-1">Du</label>
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}">
        </div>
        <div>
            <label class="form-label small fw-bold mb-1">Au</label>
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}">
        </div>
    @else
        <div>
            <label class="form-label small fw-bold mb-1">Date</label>
            <input type="date" name="date" class="form-control form-control-sm" value="{{ $date }}">
        </div>
    @endif

    {{-- Référence article ← LE FILTRE CLÉ --}}
    <div>
        <label class="form-label small fw-bold mb-1">
            🔍 Référence article
            @if($searchArticle)
                <span class="badge bg-warning text-dark ms-1">Historique activé</span>
            @endif
        </label>
        <input type="text" name="search_article" class="form-control form-control-sm"
               placeholder="Code ou désignation..." value="{{ $searchArticle }}"
               style="min-width:180px;">
    </div>

    {{-- N° document --}}
    <div>
        <label class="form-label small fw-bold mb-1">N° facture/BL</label>
        <input type="text" name="search_numdoc" class="form-control form-control-sm"
               placeholder="Ex: FAC-2025-001" value="{{ request('search_numdoc') }}"
               style="min-width:140px;">
    </div>

    {{-- Chauffeur --}}
    <div>
        <label class="form-label small fw-bold mb-1">Chauffeur</label>
        <select name="chauffeur_id" class="form-select form-select-sm" style="min-width:130px;">
            <option value="">Tous</option>
            @foreach($chauffeurs as $c)
                <option value="{{ $c->id }}" {{ request('chauffeur_id')==$c->id ? 'selected':'' }}>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Magasin --}}
    <div>
        <label class="form-label small fw-bold mb-1">Magasin</label>
        <select name="site_id" class="form-select form-select-sm" style="min-width:120px;">
            <option value="">Tous</option>
            @foreach($sites as $s)
                <option value="{{ $s->id }}" {{ session('planning_site_id')==$s->id ? 'selected':'' }}>{{ $s->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Statut --}}
    <div>
        <label class="form-label small fw-bold mb-1">Statut</label>
        <select name="statut" class="form-select form-select-sm">
            <option value="">Tous</option>
            <option value="en_attente" {{ request('statut')=='en_attente' ? 'selected':'' }}>⏳ En attente</option>
            <option value="assigné"    {{ request('statut')=='assigné'    ? 'selected':'' }}>👤 Assigné</option>
            <option value="en_route"   {{ request('statut')=='en_route'   ? 'selected':'' }}>🚗 En route</option>
            <option value="recupere"   {{ request('statut')=='recupere'   ? 'selected':'' }}>✅ Récupéré</option>
            <option value="probleme"   {{ request('statut')=='probleme'   ? 'selected':'' }}>⚠️ Problème</option>
        </select>
    </div>

    {{-- Vendeur --}}
    <div>
        <label class="form-label small fw-bold mb-1">Vendeur</label>
        <input type="text" name="search_vendeur" class="form-control form-control-sm"
               placeholder="Nom vendeur..." value="{{ request('search_vendeur') }}"
               style="min-width:120px;">
    </div>

    <button type="submit" class="btn btn-primary btn-sm px-3">
        <i class="fas fa-filter me-1"></i> Filtrer
    </button>
    <a href="{{ route('planning.index') }}?site_id={{ session('planning_site_id','') }}"
       class="btn btn-outline-secondary btn-sm px-3">
        <i class="fas fa-undo me-1"></i> Reset
    </a>
</form>

{{-- Bannière mode historique --}}
@if($modeHistorique)
<div class="alert alert-warning py-2 px-3 mb-3 d-flex align-items-center gap-2" style="font-size:0.82rem;">
    <i class="fas fa-history"></i>
    <strong>Mode historique</strong> — Recherche
    @if($searchArticle) "<strong>{{ $searchArticle }}</strong>" @endif
    du {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }}
    au {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}
    — <strong>{{ $stats['total'] }}</strong> résultat(s).
    Les lignes sont groupées par date.
</div>
@endif



    {{-- ── STATS ───────────────────────────────────────────────── --}}
    <div class="row g-2 mb-3">
        <div class="col"><div class="stat-card" style="background:#6c757d;"><h3>{{ $stats['total'] }}</h3><p>Total</p></div></div>
        <div class="col"><div class="stat-card" style="background:#e67e00;"><h3>{{ $stats['non_assignees'] }}</h3><p>⚠️ Non assignées</p></div></div>
        <div class="col"><div class="stat-card" style="background:#0d6efd;"><h3>{{ $stats['en_attente'] }}</h3><p>En attente</p></div></div>
        <div class="col"><div class="stat-card" style="background:#0dcaf0;"><h3>{{ $stats['assigné'] }}</h3><p>Assignés</p></div></div>
        <div class="col"><div class="stat-card" style="background:#198754;"><h3>{{ $stats['recupere'] }}</h3><p>Récupérés</p></div></div>
        <div class="col"><div class="stat-card" style="background:#dc3545;"><h3>{{ $stats['probleme'] }}</h3><p>Problèmes</p></div></div>
    </div>




    {{-- ── RECHERCHE EN TEMPS RÉEL ─────────────────────── --}}
<div class="search-bar">
    <span class="search-label"><i class="fas fa-bolt me-1 text-warning"></i> Recherche rapide</span>
    <div class="search-wrap">
        <i class="fas fa-barcode"></i>
        <input type="text" id="search-piece" class="search-input" placeholder="Référence article...">
    </div>
    <div class="search-wrap">
        <i class="fas fa-user-tie"></i>
        <input type="text" id="search-vendeur" class="search-input" placeholder="Vendeur...">
    </div>
    <span id="search-count" class="ms-2"></span>
    <button onclick="clearSearch()" class="btn btn-sm btn-outline-secondary px-3">
        <i class="fas fa-times me-1"></i> Effacer
    </button>
</div>





    {{-- ── MATIN ───────────────────────────────────────────────── --}}
    @php
    $allCreneaux = [];
    foreach ($lignesParCreneau->keys() as $slotKey) {
        $allCreneaux[$slotKey] = [
            'icon'  => '🕐',
            'label' => $slotKey,
            'color' => 'slot-header',
        ];
    }
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
                'chauffeurs'      => $chauffeurs,
                    'modeHistorique'  => $modeHistorique ?? false
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










// Charger les créneaux selon le site de la ligne
document.querySelectorAll('.slot-select').forEach(function(sel) {
    var siteId  = sel.getAttribute('data-site-id');
    var lineId  = sel.getAttribute('data-line-id');
    var current = sel.value;

    fetch('/planning/creneaux/' + siteId)
        .then(function(r) { return r.json(); })
        .then(function(creneaux) {
            sel.innerHTML = '';
            creneaux.forEach(function(c) {
                var opt = document.createElement('option');
                opt.value = c.label;
                opt.textContent = c.label;
                if (c.label === current) opt.selected = true;
                sel.appendChild(opt);
            });
        });
});

function updateSlot(lineId, slot) {
    fetch('{{ route("planning.update_slot") }}', {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ line_id: lineId, slot: slot })
    }).then(r => r.json()).then(d => {
        if (!d.success) alert('Erreur mise à jour créneau');
    });
}

function updateDate(lineId, date) {
    fetch('{{ route("planning.update_date") }}', {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ line_id: lineId, date: date })
    }).then(r => r.json()).then(d => {
        if (!d.success) alert('Erreur mise à jour date');
    });
}

function deleteLine(lineId, btn) {
    if (!confirm('Supprimer cette ligne ?')) return;
    fetch('/planning/delete-line/' + lineId, {
        method: 'DELETE',
        credentials: 'same-origin',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(r => r.json()).then(d => {
        if (d.success) {
            var row = document.getElementById('line-row-' + lineId);
            if (row) row.remove();
        } else alert('Erreur suppression');
    });
}









// ── Recherche en temps réel ──────────────────────────────
var searchPiece  = document.getElementById('search-piece');
var searchVendeur = document.getElementById('search-vendeur');
var searchCount  = document.getElementById('search-count');

function applySearch() {
    var piece   = searchPiece.value.trim().toLowerCase();
    var vendeur = searchVendeur.value.trim().toLowerCase();
    var total   = 0;
    var visible = 0;

    document.querySelectorAll('.line-row').forEach(function(row) {
        total++;
        var code    = (row.getAttribute('data-search-code')    || '').toLowerCase();
        var name    = (row.getAttribute('data-search-name')    || '').toLowerCase();
        var vend    = (row.getAttribute('data-search-vendeur') || '').toLowerCase();

        var matchPiece   = !piece   || code.includes(piece)   || name.includes(piece);
        var matchVendeur = !vendeur || vend.includes(vendeur);

        if (matchPiece && matchVendeur) {
            row.classList.remove('hidden');
            visible++;
        } else {
            row.classList.add('hidden');
        }
    });

    // Masquer les fournisseurs sans lignes visibles
    document.querySelectorAll('.fourn-card').forEach(function(card) {
        var hasVisible = card.querySelectorAll('.line-row:not(.hidden)').length > 0;
        card.style.display = hasVisible ? '' : 'none';
    });

    // Afficher le compteur si recherche active
    if (piece || vendeur) {
        searchCount.textContent = visible + ' / ' + total + ' pièce(s)';
        searchCount.style.color = visible === 0 ? '#dc3545' : '#198754';
    } else {
        searchCount.textContent = '';
    }
}

function clearSearch() {
    searchPiece.value = '';
    searchVendeur.value = '';
    applySearch();
    searchPiece.focus();
}

searchPiece.addEventListener('input', applySearch);
searchVendeur.addEventListener('input', applySearch);


</script>
</body>
</html>