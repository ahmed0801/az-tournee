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

        /* ── Recherche temps réel ─────────────────────────── */
        .search-bar { background: white; border-radius: 12px; padding: 12px 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); margin-bottom: 16px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .search-input { border: 1.5px solid #e0e8ff; border-radius: 8px; padding: 7px 14px 7px 36px; font-size: 0.85rem; outline: none; transition: border-color 0.2s; background: #f8fbff; min-width: 200px; flex: 1; }
        .search-input:focus { border-color: #0d6efd; background: white; }
        .search-wrap { position: relative; flex: 1; min-width: 180px; }
        .search-wrap i { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: #9bacc4; font-size: 0.8rem; }
        .search-label { font-size: 0.72rem; font-weight: 700; color: #6c8aad; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
        #search-count { font-size: 0.78rem; color: #6c757d; white-space: nowrap; }
        .line-row.hidden { display: none !important; }
        .fourn-card.all-hidden .fourn-header { opacity: 0.4; }

        /* ── Filter panel ─────────────────────────────────── */
        .filter-panel {
            background: white;
            border-radius: 14px;
            box-shadow: 0 2px 16px rgba(30,45,74,0.08);
            margin-bottom: 16px;
            overflow: hidden;
        }
        .filter-panel-header {
            background: linear-gradient(135deg, #1a2b4a, #2d4a8a);
            padding: 10px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            user-select: none;
        }
        .filter-panel-header span { color: white; font-weight: 700; font-size: 0.82rem; letter-spacing: 0.04em; }
        .filter-panel-body { padding: 14px 18px 16px; }

        /* Période pills */
        .period-pills { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 14px; }
        .period-pill {
            padding: 5px 14px; border-radius: 20px; font-size: 0.78rem; font-weight: 600;
            border: 1.5px solid #e2e8f0; background: white; color: #6b7a99;
            text-decoration: none; transition: all 0.18s; cursor: pointer;
            display: inline-flex; align-items: center; gap: 5px;
        }
        .period-pill:hover { border-color: #3b82f6; color: #3b82f6; background: #eff6ff; }
        .period-pill.active { background: #1e2d4a; color: white; border-color: #1e2d4a; }
        .period-pill.active-week { background: #7c3aed; color: white; border-color: #7c3aed; }
        .period-pill.active-month { background: #0891b2; color: white; border-color: #0891b2; }
        .period-pill.active-hier { background: #64748b; color: white; border-color: #64748b; }

        /* Filter grid */
        .filter-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 10px; }
        .filter-field { display: flex; flex-direction: column; gap: 4px; }
        .filter-label { font-size: 0.68rem; font-weight: 700; color: #6b7a99; text-transform: uppercase; letter-spacing: 0.06em; }
        .filter-input {
            border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 7px 10px;
            font-size: 0.8rem; color: #1a2b4a; background: #f8faff;
            transition: border-color 0.2s; outline: none; width: 100%;
        }
        .filter-input:focus { border-color: #3b82f6; background: white; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
        .filter-input.active-filter { border-color: #f59e0b; background: #fffbeb; }

        /* Article search — special */
        .filter-article-wrap { grid-column: span 2; }
        .filter-article-inner { position: relative; }
        .filter-article-inner i { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #9bacc4; }
        .filter-article-inner input { padding-left: 32px; }

        /* Date range */
        .filter-date-group { display: flex; align-items: center; gap: 6px; grid-column: span 2; }
        .filter-date-group .filter-input { flex: 1; }
        .filter-date-sep { color: #9bacc4; font-size: 0.75rem; font-weight: 600; white-space: nowrap; }

        /* Historique badge */
        .historique-badge {
            display: inline-flex; align-items: center; gap: 5px;
            background: #fef3c7; border: 1px solid #f59e0b; border-radius: 6px;
            padding: 2px 8px; font-size: 0.68rem; font-weight: 700; color: #92400e;
        }

        /* Actions */
        .filter-actions { display: flex; gap: 8px; margin-top: 14px; align-items: center; }
        .btn-filter { background: #1e2d4a; color: white; border: none; border-radius: 8px; padding: 8px 20px; font-size: 0.8rem; font-weight: 700; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px; }
        .btn-filter:hover { background: #2d4a8a; transform: translateY(-1px); }
        .btn-reset { background: none; border: 1.5px solid #e2e8f0; color: #6b7a99; border-radius: 8px; padding: 7px 16px; font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: all 0.2s; text-decoration: none; display: flex; align-items: center; gap: 5px; }
        .btn-reset:hover { border-color: #6b7a99; color: #1a2b4a; }
        .filter-result-count { font-size: 0.75rem; color: #6b7a99; margin-left: auto; }
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

    {{-- ════════════════════════════════════════════════════════
         FILTER PANEL
         ════════════════════════════════════════════════════════ --}}
    @php
        $todayStr     = today()->format('Y-m-d');
        $hierStr      = today()->subDay()->format('Y-m-d');
        $weekFromStr  = today()->startOfWeek()->format('Y-m-d');
        $monthFromStr = today()->startOfMonth()->format('Y-m-d');
        $siteParam    = session('planning_site_id', '');

        $isToday  = !$modeHistorique && $date === $todayStr;
        $isHier   = !$modeHistorique && $date === $hierStr;
        $isWeek   = $modeHistorique  && $dateFrom === $weekFromStr  && $dateTo === $todayStr && !$searchArticle && !request('search_numdoc');
        $isMonth  = $modeHistorique  && $dateFrom === $monthFromStr && $dateTo === $todayStr && !$searchArticle && !request('search_numdoc');
        $isCustom = !$isToday && !$isHier && !$isWeek && !$isMonth;
    @endphp

    <div class="filter-panel">
        <div class="filter-panel-header" onclick="toggleFilters()">
            <span>
                <i class="fas fa-sliders-h me-2"></i> Filtres & Recherche
                @if($modeHistorique)
                    &nbsp;<span class="historique-badge"><i class="fas fa-history"></i> Historique</span>
                @endif
            </span>
            <div class="d-flex align-items-center gap-3">
                @if($stats['total'] > 0)
                    <span style="color:rgba(255,255,255,0.7);font-size:0.75rem;">{{ $stats['total'] }} pièce(s)</span>
                @endif
                <i class="fas fa-chevron-down text-white" id="filter-chevron" style="transition:transform 0.2s;font-size:0.8rem;"></i>
            </div>
        </div>

        <div class="filter-panel-body" id="filter-body">

            {{-- ── Raccourcis période ─────────────────────────── --}}
            <div class="period-pills">
                <a href="{{ route('planning.index') }}?site_id={{ $siteParam }}"
                   class="period-pill {{ $isToday ? 'active' : '' }}">
                    📅 Aujourd'hui
                </a>
                <a href="?date={{ $hierStr }}&site_id={{ $siteParam }}"
                   class="period-pill {{ $isHier ? 'active-hier' : '' }}">
                    🌙 Hier
                </a>
                <a href="?date_from={{ $weekFromStr }}&date_to={{ $todayStr }}&site_id={{ $siteParam }}"
                   class="period-pill {{ $isWeek ? 'active-week' : '' }}">
                    📆 Cette semaine
                </a>
                <a href="?date_from={{ $monthFromStr }}&date_to={{ $todayStr }}&site_id={{ $siteParam }}"
                   class="period-pill {{ $isMonth ? 'active-month' : '' }}">
                    🗓 Ce mois
                </a>
                @if($isCustom && ($modeHistorique || $date !== $todayStr))
                    <span class="period-pill" style="background:#fef3c7;border-color:#f59e0b;color:#92400e;cursor:default;">
                        ✏️ Personnalisé
                    </span>
                @endif
            </div>

            {{-- ── Grille de filtres ──────────────────────────── --}}
            <form method="GET" id="filterForm">
                <div class="filter-grid">

                    {{-- Article — occupe 2 colonnes --}}
                    <div class="filter-field filter-article-wrap">
                        <label class="filter-label">
                            🔍 Référence / Désignation article
                            @if($searchArticle)
                                <span class="historique-badge ms-1"><i class="fas fa-history"></i> Historique auto</span>
                            @endif
                        </label>
                        <div class="filter-article-inner">
                            <i class="fas fa-barcode"></i>
                            <input type="text" name="search_article" class="filter-input {{ $searchArticle ? 'active-filter' : '' }}"
                                   placeholder="Ex: 1234567 ou Plaquettes de frein..."
                                   value="{{ $searchArticle }}">
                        </div>
                    </div>

                    {{-- Date — simple ou plage --}}
                    @if($modeHistorique)
                        <div class="filter-field filter-date-group" style="grid-column:span 2;">
                            <div style="flex:1;">
                                <label class="filter-label">Du</label>
                                <input type="date" name="date_from" class="filter-input" value="{{ $dateFrom }}">
                            </div>
                            <div class="filter-date-sep">→</div>
                            <div style="flex:1;">
                                <label class="filter-label">Au</label>
                                <input type="date" name="date_to" class="filter-input" value="{{ $dateTo }}">
                            </div>
                        </div>
                    @else
                        <div class="filter-field">
                            <label class="filter-label">📅 Date</label>
                            <input type="date" name="date" class="filter-input" value="{{ $date }}">
                        </div>
                    @endif

                    {{-- N° document --}}
                    <div class="filter-field">
                        <label class="filter-label">N° Facture / BL</label>
                        <input type="text" name="search_numdoc" class="filter-input {{ request('search_numdoc') ? 'active-filter' : '' }}"
                               placeholder="FAC-2025-001..." value="{{ request('search_numdoc') }}">
                    </div>

                    {{-- Vendeur --}}
                    <div class="filter-field">
                        <label class="filter-label">👤 Vendeur</label>
                        <input type="text" name="search_vendeur" class="filter-input {{ request('search_vendeur') ? 'active-filter' : '' }}"
                               placeholder="Nom du vendeur..." value="{{ request('search_vendeur') }}">
                    </div>

                    {{-- Chauffeur --}}
                    <div class="filter-field">
                        <label class="filter-label">🚗 Chauffeur</label>
                        <select name="chauffeur_id" class="filter-input {{ request('chauffeur_id') ? 'active-filter' : '' }}">
                            <option value="">Tous les chauffeurs</option>
                            @foreach($chauffeurs as $c)
                                <option value="{{ $c->id }}" {{ request('chauffeur_id')==$c->id ? 'selected':'' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Magasin --}}
                    <div class="filter-field">
                        <label class="filter-label">🏪 Magasin</label>
                        <select name="site_id" class="filter-input {{ session('planning_site_id') ? 'active-filter' : '' }}">
                            <option value="">Tous les magasins</option>
                            @foreach($sites as $s)
                                <option value="{{ $s->id }}" {{ session('planning_site_id')==$s->id ? 'selected':'' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Statut --}}
                    <div class="filter-field">
                        <label class="filter-label">⚡ Statut</label>
                        <select name="statut" class="filter-input {{ request('statut') ? 'active-filter' : '' }}">
                            <option value="">Tous les statuts</option>
                            <option value="en_attente" {{ request('statut')=='en_attente' ? 'selected':'' }}>⏳ En attente</option>
                            <option value="assigné"    {{ request('statut')=='assigné'    ? 'selected':'' }}>👤 Assigné</option>
                            <option value="en_route"   {{ request('statut')=='en_route'   ? 'selected':'' }}>🚗 En route</option>
                            <option value="recupere"   {{ request('statut')=='recupere'   ? 'selected':'' }}>✅ Récupéré</option>
                            <option value="au_magasin" {{ request('statut')=='au_magasin' ? 'selected':'' }}>🏪 Au magasin</option>
                            <option value="probleme"   {{ request('statut')=='probleme'   ? 'selected':'' }}>⚠️ Problème</option>
                        </select>
                    </div>

                </div>

                {{-- Actions --}}
                <div class="filter-actions">
                    <button type="submit" class="btn-filter">
                        <i class="fas fa-filter"></i> Appliquer
                    </button>
                    <a href="{{ route('planning.index') }}?site_id={{ $siteParam }}" class="btn-reset">
                        <i class="fas fa-undo"></i> Réinitialiser
                    </a>
                    @if($modeHistorique)
                        <span class="filter-result-count">
                            <i class="fas fa-history me-1 text-warning"></i>
                            <strong>{{ $stats['total'] }}</strong> résultat(s) —
                            {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }}
                            → {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}
                        </span>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- ── STATS ───────────────────────────────────────────────── --}}
    <div class="row g-2 mb-3">
        <div class="col"><div class="stat-card" style="background:#6c757d;"><h3>{{ $stats['total'] }}</h3><p>Total</p></div></div>
        <div class="col"><div class="stat-card" style="background:#e67e00;"><h3>{{ $stats['non_assignees'] }}</h3><p>⚠️ Non assignées</p></div></div>
        <div class="col"><div class="stat-card" style="background:#0d6efd;"><h3>{{ $stats['en_attente'] }}</h3><p>En attente</p></div></div>
        <div class="col"><div class="stat-card" style="background:#0dcaf0;"><h3>{{ $stats['assigné'] }}</h3><p>Assignés</p></div></div>
        <div class="col"><div class="stat-card" style="background:#198754;"><h3>{{ $stats['recupere'] }}</h3><p>Récupérés</p></div></div>
        <div class="col"><div class="stat-card" style="background:#dc3545;"><h3>{{ $stats['probleme'] }}</h3><p>Problèmes</p></div></div>
    </div>

    {{-- ── RECHERCHE TEMPS RÉEL ────────────────────────────────── --}}
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

    {{-- ── CRÉNEAUX ─────────────────────────────────────────────── --}}
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
        @php $lignesCreneau = $lignesParCreneau[$slotKey] ?? collect(); @endphp
        @if($lignesCreneau->isNotEmpty())
        <div class="{{ $slotInfo['color'] }}" style="{{ !$loop->first ? 'margin-top:24px;' : '' }}">
            {{ $slotInfo['icon'] }}
            @if($modeHistorique)
                📅 {{ \Carbon\Carbon::parse($slotKey)->format('d/m/Y') }}
            @else
                Tournée {{ $slotInfo['label'] }}
            @endif
            <span class="badge bg-white text-dark ms-2">{{ $lignesCreneau->flatten()->count() }} pièce(s)</span>
        </div>
        @foreach($lignesCreneau as $fournisseurName => $lignes)
            @include('planning.partials.fourn-group-dispatcher', [
                'fournisseurName' => $fournisseurName,
                'lignes'          => $lignes,
                'chauffeurs'      => $chauffeurs,
                'modeHistorique'  => $modeHistorique ?? false,
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

// ── Toggle panel filtres ──────────────────────────────────────
var filterOpen = true;
function toggleFilters() {
    var body    = document.getElementById('filter-body');
    var chevron = document.getElementById('filter-chevron');
    filterOpen  = !filterOpen;
    body.style.display    = filterOpen ? 'block' : 'none';
    chevron.style.transform = filterOpen ? 'rotate(0deg)' : 'rotate(-90deg)';
}

// ── Highlight du champ article si rempli ──────────────────────
document.getElementById('filterForm').querySelectorAll('.filter-input').forEach(function(el) {
    el.addEventListener('input', function() {
        this.classList.toggle('active-filter', this.value.trim() !== '');
    });
});

// ── Submit auto si date change ────────────────────────────────
document.getElementById('filterForm').querySelectorAll('input[type="date"]').forEach(function(el) {
    el.addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
});

function assignChauffeur(lineId, chauffeurId) {
    fetch('/planning/assign', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({line_id: lineId, chauffeur_id: chauffeurId})
    }).then(r => r.json()).then(d => {
        if (d.success) showToast('Chauffeur assigné', 'success');
    });
}

function updateStatut(lineId, statut) {
    fetch('/planning/statut', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({line_id: lineId, statut: statut})
    }).then(r => r.json()).then(d => {
        if (d.success) showToast('Statut mis à jour', 'success');
    });
}

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

setTimeout(() => location.reload(), 30000);

document.querySelectorAll('.slot-select').forEach(function(sel) {
    var siteId  = sel.getAttribute('data-site-id');
    var current = sel.value;
    fetch('/planning/creneaux/' + siteId)
        .then(function(r) { return r.json(); })
        .then(function(creneaux) {
            sel.innerHTML = '';
            creneaux.forEach(function(c) {
                var opt = document.createElement('option');
                opt.value = c.label; opt.textContent = c.label;
                if (c.label === current) opt.selected = true;
                sel.appendChild(opt);
            });
        });
});

function updateSlot(lineId, slot) {
    fetch('{{ route("planning.update_slot") }}', {
        method: 'POST', credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ line_id: lineId, slot: slot })
    }).then(r => r.json()).then(d => { if (!d.success) alert('Erreur créneau'); });
}

function updateDate(lineId, date) {
    fetch('{{ route("planning.update_date") }}', {
        method: 'POST', credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ line_id: lineId, date: date })
    }).then(r => r.json()).then(d => { if (!d.success) alert('Erreur date'); });
}

function deleteLine(lineId, btn) {
    if (!confirm('Supprimer cette ligne ?')) return;
    fetch('/planning/delete-line/' + lineId, {
        method: 'DELETE', credentials: 'same-origin',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(r => r.json()).then(d => {
        if (d.success) { var row = document.getElementById('line-row-' + lineId); if (row) row.remove(); }
        else alert('Erreur suppression');
    });
}

// ── Recherche temps réel ──────────────────────────────────────
var searchPiece   = document.getElementById('search-piece');
var searchVendeur = document.getElementById('search-vendeur');
var searchCount   = document.getElementById('search-count');

function applySearch() {
    var piece   = searchPiece.value.trim().toLowerCase();
    var vendeur = searchVendeur.value.trim().toLowerCase();
    var total = 0, visible = 0;

    document.querySelectorAll('.line-row').forEach(function(row) {
        total++;
        var code = (row.getAttribute('data-search-code')    || '').toLowerCase();
        var name = (row.getAttribute('data-search-name')    || '').toLowerCase();
        var vend = (row.getAttribute('data-search-vendeur') || '').toLowerCase();
        var ok = (!piece || code.includes(piece) || name.includes(piece)) &&
                 (!vendeur || vend.includes(vendeur));
        row.classList.toggle('hidden', !ok);
        if (ok) visible++;
    });

    document.querySelectorAll('.fourn-card').forEach(function(card) {
        card.style.display = card.querySelectorAll('.line-row:not(.hidden)').length > 0 ? '' : 'none';
    });

    if (piece || vendeur) {
        searchCount.textContent = visible + ' / ' + total + ' pièce(s)';
        searchCount.style.color = visible === 0 ? '#dc3545' : '#198754';
    } else {
        searchCount.textContent = '';
    }
}

function clearSearch() {
    searchPiece.value = ''; searchVendeur.value = '';
    applySearch(); searchPiece.focus();
}

searchPiece.addEventListener('input', applySearch);
searchVendeur.addEventListener('input', applySearch);
</script>
</body>
</html>