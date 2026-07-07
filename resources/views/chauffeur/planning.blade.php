<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>🚗 Ma Tournée — {{ $chauffeur->name }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { -webkit-tap-highlight-color: transparent; box-sizing: border-box; }

        body {
            background: #0f1923;
            color: #e8ecf0;
            font-family: 'Segoe UI', sans-serif;
            padding-bottom: 90px;
            margin: 0;
        }

        /* ── Top bar ─────────────────────────────────── */
        .top-bar {
            background: linear-gradient(135deg, #1a2b4a, #2d4a8a);
            padding: 12px 16px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0,0,0,0.4);
        }
        .top-bar h5 { margin: 0; font-size: 0.95rem; font-weight: 700; }
        .stats-bar { display: flex; gap: 8px; margin-top: 8px; flex-wrap: wrap; }
        .stat-pill {
            background: rgba(255,255,255,0.12);
            border-radius: 20px;
            padding: 3px 10px;
            font-size: 0.72rem;
            font-weight: 600;
        }
        .stat-pill.green { background: rgba(40,167,69,0.25); color: #7fff8c; }
        .stat-pill.orange { background: rgba(255,193,7,0.25); color: #ffe566; }
        .stat-pill.red { background: rgba(220,53,69,0.25); color: #ff8888; }

        /* ── Slot header ─────────────────────────────── */
        .slot-header {
            padding: 10px 16px;
            margin: 10px 0 4px;
            border-left: 4px solid #ffc107;
            background: rgba(255,193,7,0.07);
            color: #ffc107;
            font-weight: 700;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .slot-header.apm {
            border-color: #17a2b8;
            background: rgba(23,162,184,0.07);
            color: #17a2b8;
        }

        /* ── Groupe fournisseur ──────────────────────── */
        .fourn-group { margin: 6px 10px; background: #1a2535; border-radius: 10px; overflow: hidden; }
        .fourn-header {
            background: #243450;
            padding: 10px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            user-select: none;
        }
        .fourn-name { font-weight: 700; font-size: 0.88rem; }
        .fourn-address { font-size: 0.68rem; color: #6080a0; margin-top: 2px; }
        .fourn-count {
            background: rgba(255,255,255,0.12);
            border-radius: 12px;
            padding: 2px 9px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .fourn-count.done { background: rgba(40,167,69,0.3); color: #7fff8c; }

        /* ── Article card ────────────────────────────── */
        .article-card {
            padding: 12px 14px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            transition: background 0.2s;
        }
        .article-card:last-child { border-bottom: none; }
        .article-card.recupere { opacity: 0.45; }
        .article-card.probleme { background: rgba(220,53,69,0.07); }

        .article-code {
            font-family: 'Courier New', monospace;
            font-size: 1.05rem;
            font-weight: 900;
            color: #60a5fa;
            letter-spacing: 0.04em;
        }
        .article-name {
            font-size: 0.8rem;
            color: #8090a8;
            margin-top: 2px;
            line-height: 1.3;
        }
        .article-meta {
            display: flex;
            gap: 6px;
            margin-top: 6px;
            flex-wrap: wrap;
            align-items: center;
        }
        .meta-badge {
            font-size: 0.67rem;
            padding: 2px 7px;
            border-radius: 10px;
        }
        .meta-site    { background: #1e3560; color: #80b0ff; }
        .meta-qty     { background: #0d2e1e; color: #60e090; }
        .meta-doc     { background: #2e1e00; color: #ffa030; }
        .meta-note    { background: #1e0e30; color: #c080ff; }
        .meta-vendeur { background: #1a1a2e; color: #8080c0; }

        /* ── Statut dot ──────────────────────────────── */
        .status-dot {
            width: 9px; height: 9px;
            border-radius: 50%;
            display: inline-block;
            flex-shrink: 0;
            margin-right: 4px;
        }
        .status-dot.en_attente { background: #555; }
        .status-dot.assigné    { background: #0d6efd; }
        .status-dot.en_route   { background: #17a2b8; }
        .status-dot.recupere   { background: #28a745; }
        .status-dot.au_magasin { background: #6c757d; }
        .status-dot.probleme   { background: #dc3545; box-shadow: 0 0 6px #dc3545; }

        /* ── Boutons d'action ────────────────────────── */
        .action-btns { display: flex; gap: 6px; margin-top: 8px; flex-wrap: wrap; }

        .btn-scan {
            background: #1e3560;
            color: #80b0ff;
            border: 1.5px solid #2d4a8a;
            border-radius: 8px;
            padding: 8px 14px;
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            flex: 1;
            transition: all 0.15s;
            min-height: 38px;
        }
        .btn-scan:active { transform: scale(0.96); }
        .btn-scan.active {
            background: #0d6efd;
            border-color: #0d6efd;
            color: white;
            box-shadow: 0 0 12px rgba(13,110,253,0.4);
        }

        .btn-done {
            background: rgba(40,167,69,0.2);
            color: #60e090;
            border: 1.5px solid rgba(40,167,69,0.3);
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            min-height: 38px;
            transition: all 0.15s;
        }
        .btn-done:active { transform: scale(0.96); }

        .btn-probleme {
            background: rgba(220,53,69,0.15);
            color: #ff8888;
            border: 1.5px solid rgba(220,53,69,0.3);
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 0.78rem;
            cursor: pointer;
            min-height: 38px;
            transition: all 0.15s;
        }
        .btn-probleme:active { transform: scale(0.96); }

        /* ── Zone de scan ────────────────────────────── */
        .scan-zone {
            background: #0a1520;
            border: 2px solid #2d4a8a;
            border-radius: 8px;
            padding: 10px;
            margin-top: 8px;
            display: none;
        }
        .scan-zone.active {
            display: block;
            border-color: #0d6efd;
        }
        .scan-info {
            font-size: 0.72rem;
            color: #5070a0;
            margin-bottom: 6px;
        }
        .scan-info code { color: #60a5fa; background: #1a2535; padding: 1px 5px; border-radius: 3px; }
        .scan-input {
            background: #1a2535;
            color: #e8ecf0;
            border: 1.5px solid #2d4a8a;
            border-radius: 6px;
            padding: 10px 12px;
            font-size: 1rem;
            width: 100%;
            outline: none;
            font-family: monospace;
        }
        .scan-input:focus { border-color: #0d6efd; box-shadow: 0 0 8px rgba(13,110,253,0.25); }

        .scan-feedback {
            margin-top: 6px;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.80rem;
            display: none;
        }
        .scan-feedback.success { display: block; background: rgba(40,167,69,0.15); color: #7fff8c; border: 1px solid rgba(40,167,69,0.3); }
        .scan-feedback.warning { display: block; background: rgba(255,193,7,0.12); color: #ffd066; border: 1px solid rgba(255,193,7,0.3); }
        .scan-feedback.error   { display: block; background: rgba(220,53,69,0.12); color: #ff8888; border: 1px solid rgba(220,53,69,0.3); }

        .btn-validate-scan {
            background: #0d6efd;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 6px;
            width: 100%;
            min-height: 36px;
        }

        /* ── État vide ───────────────────────────────── */
        .empty-state {
            text-align: center;
            padding: 36px 20px;
            color: #304060;
        }
        .empty-state i { font-size: 2.5rem; margin-bottom: 10px; display: block; }

        /* ── FAB bouton scan global ──────────────────── */
        .fab-scan {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #0d6efd;
            color: white;
            border: none;
            width: 58px;
            height: 58px;
            border-radius: 50%;
            font-size: 1.3rem;
            box-shadow: 0 4px 16px rgba(13,110,253,0.5);
            cursor: pointer;
            z-index: 200;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
        }
        .fab-scan:active { transform: scale(0.92); }

        /* ── Overlay scan global ─────────────────────── */
        .scan-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.88);
            z-index: 300;
            align-items: center;
            justify-content: center;
        }
        .scan-overlay.active { display: flex; }
        .scan-modal-box {
            background: #1a2535;
            border-radius: 16px;
            padding: 24px 20px;
            width: 92%;
            max-width: 400px;
            border: 1.5px solid #2d4a8a;
        }
        .scan-modal-title { color: #60a5fa; font-weight: 700; margin-bottom: 14px; font-size: 1rem; }
        .scan-modal-input {
            background: #0d1a2e;
            color: #e8ecf0;
            border: 2px solid #0d6efd;
            border-radius: 10px;
            padding: 14px;
            font-size: 1.3rem;
            width: 100%;
            outline: none;
            font-family: monospace;
            text-align: center;
        }
        .scan-modal-feedback {
            margin-top: 10px;
            font-size: 0.82rem;
            min-height: 24px;
        }

        /* ── Modal problème ──────────────────────────── */
        .modal-dark .modal-content { background: #1a2535; color: #e8ecf0; border: 1px solid #2d4a8a; }
        .modal-dark .modal-header  { border-color: #2d4a8a; }
        .modal-dark .modal-footer  { border-color: #2d4a8a; }
        .modal-dark .form-control  { background: #0d1a2e; color: #e8ecf0; border-color: #2d4a8a; }
        .modal-dark .form-control:focus { border-color: #0d6efd; box-shadow: none; }

        /* ── Résultat récupéré ───────────────────────── */
        .result-recupere {
            color: #60e090;
            font-size: 0.78rem;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .result-probleme {
            color: #ff8888;
            font-size: 0.78rem;
            margin-top: 6px;
        }
    </style>
</head>
<body>

{{-- ── TOP BAR ─────────────────────────────────────────────────── --}}
<div class="top-bar">
    <div class="d-flex justify-content-between align-items-center">
        <h5>
            🚗 {{ $chauffeur->name }}
            <span style="color:#4a7aaa; font-weight:400; font-size:0.78rem; margin-left:6px;">
                {{ now()->format('d/m/Y') }}
            </span>
        </h5>

        <div class="d-flex align-items-center gap-2">
            <!-- Bouton Actualiser - Version plus petite -->
            <button onclick="window.location.reload()" 
                    class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1"
                    style="padding: 5px 12px; font-size: 0.85rem;">
                <i class="fas fa-sync-alt"></i>
                <span>Actualiser</span>
            </button>

            <!-- Bouton Déconnexion -->
            <form action="{{ route('chauffeur.logout') }}" method="POST" class="mb-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-light" style="font-size:0.85rem; padding:6px 12px;">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="stats-bar">
        <div class="stat-pill orange">📦 {{ $stats['total'] }} pièce{{ $stats['total'] > 1 ? 's' : '' }}</div>
        <div class="stat-pill green">✅ {{ $stats['recupere'] }} récupérée{{ $stats['recupere'] > 1 ? 's' : '' }}</div>
        <div class="stat-pill {{ $stats['restant'] > 0 ? 'red' : 'green' }}">
            ⏳ {{ $stats['restant'] }} restante{{ $stats['restant'] > 1 ? 's' : '' }}
        </div>
    </div>
</div>

{{-- ── PIÈCES NON ASSIGNÉES ─────────────────────────────────────── --}}
@php
    $nonAssignees = \App\Models\TourneeLine::with(['site', 'fournisseur'])
        ->whereDate('date_tournee', today())
        ->whereNull('chauffeur_id')
        ->whereNotIn('statut', ['recupere', 'au_magasin'])
        ->orderBy('fournisseur_name')
        ->get();
@endphp

@if($nonAssignees->count() > 0)
<div style="margin:10px;background:#1a1a2e;border-radius:10px;border:2px solid #ffc107;overflow:hidden;">
    <div style="background:#2a2500;padding:10px 14px;display:flex;justify-content:space-between;align-items:center;">
        <span style="color:#ffc107;font-weight:700;font-size:0.88rem;">
            <i class="fas fa-exclamation-circle me-2"></i>
            Pièces à prendre — Non assignées
        </span>
        <span style="background:rgba(255,193,7,0.2);color:#ffc107;border-radius:12px;padding:2px 10px;font-size:0.72rem;">
            {{ $nonAssignees->count() }} pièce(s)
        </span>
    </div>
    @foreach($nonAssignees->groupBy('fournisseur_name') as $fourn => $lignes)
    <div style="border-bottom:1px solid rgba(255,255,255,0.05);">
        <div style="padding:8px 14px;background:#1e1a00;font-size:0.78rem;color:#ffa030;font-weight:600;">
            <i class="fas fa-industry me-1"></i>{{ $fourn ?: 'Fournisseur non défini' }}
            <small style="color:#888;font-weight:400;margin-left:8px;">
                {{ optional($lignes->first()->site)->name }}
            </small>
        </div>
        @foreach($lignes as $ligne)
        <div class="article-card" id="nonassign-card-{{ $ligne->id }}" style="padding:10px 14px;">
            <div class="d-flex align-items-start justify-content-between gap-2">
                <div class="flex-fill">
                    <span class="article-code">{{ $ligne->article_code }}</span>
                    <div class="article-name">{{ $ligne->article_name }}</div>
                    <div class="article-meta">
                        <span class="meta-badge meta-doc">📄 {{ $ligne->source_numdoc }}</span>
                        <span class="meta-badge meta-qty">×{{ number_format($ligne->quantity, 0) }}</span>
                        @if($ligne->notes)
                            <span class="meta-badge meta-note">📝 {{ Str::limit($ligne->notes, 25) }}</span>
                        @endif
                    </div>
                </div>
                <button class="btn-self-assign"
                        id="btn-assign-{{ $ligne->id }}"
                        onclick="selfAssign({{ $ligne->id }})"
                        style="background:linear-gradient(135deg,#ffc107,#e67e00);color:#1a1a00;border:none;
                               border-radius:8px;padding:8px 14px;font-size:0.78rem;font-weight:700;
                               cursor:pointer;white-space:nowrap;flex-shrink:0;transition:all 0.15s;">
                    <i class="fas fa-hand-point-right me-1"></i> Je prends
                </button>
            </div>
        </div>
        @endforeach
    </div>
    @endforeach
</div>
@endif

{{-- ── TOURNÉE MATIN ────────────────────────────────────────────── --}}
<div class="slot-header">
    🌅 Matin — 8h à 12h
    <span style="font-size:0.68rem; font-weight:400; margin-left:8px; opacity:0.8;">
        {{ $matin->flatten()->count() }} pièce(s)
    </span>
</div>

@if($matin->isEmpty())
    <div class="empty-state">
        <i class="fas fa-coffee"></i>
        Aucune pièce à récupérer ce matin
    </div>
@else
    @foreach($matin as $fournisseurName => $lignes)
        @php $gid = 'm-' . $loop->index; @endphp
        @include('chauffeur.partials.fourn-group', [
            'fournisseurName' => $fournisseurName,
            'lignes'          => $lignes,
            'groupId'         => $gid,
        ])
    @endforeach
@endif

{{-- ── TOURNÉE APRÈS-MIDI ───────────────────────────────────────── --}}
<div class="slot-header apm" style="margin-top:14px;">
    🌇 Après-midi — 13h à 18h
    <span style="font-size:0.68rem; font-weight:400; margin-left:8px; opacity:0.8;">
        {{ $apresMidi->flatten()->count() }} pièce(s)
    </span>
</div>

@if($apresMidi->isEmpty())
    <div class="empty-state">
        <i class="fas fa-sun"></i>
        Aucune pièce à récupérer cet après-midi
    </div>
@else
    @foreach($apresMidi as $fournisseurName => $lignes)
        @php $gid = 'am-' . $loop->index; @endphp
        @include('chauffeur.partials.fourn-group', [
            'fournisseurName' => $fournisseurName,
            'lignes'          => $lignes,
            'groupId'         => $gid,
        ])
    @endforeach
@endif

{{-- ── FAB SCAN ─────────────────────────────────────────────────── --}}
<button class="fab-scan" onclick="openGlobalScan()" title="Scanner une pièce">
    <i class="fas fa-barcode"></i>
</button>

{{-- ── OVERLAY SCAN GLOBAL ──────────────────────────────────────── --}}
<div class="scan-overlay" id="globalScanOverlay" onclick="if(event.target===this) closeGlobalScan()">
    <div class="scan-modal-box">
        <div class="scan-modal-title"><i class="fas fa-barcode me-2"></i>Scanner une pièce</div>
        <input type="text" id="globalScanInput" class="scan-modal-input"
               placeholder="Scanner ici..." autocomplete="off">
        <div id="globalScanFeedback" class="scan-modal-feedback"></div>
        <div class="d-flex gap-2 mt-3">
            <button class="btn btn-sm btn-secondary flex-fill" onclick="closeGlobalScan()">
                <i class="fas fa-times me-1"></i>Fermer
            </button>
        </div>
    </div>
</div>

{{-- ── MODAL PROBLÈME ───────────────────────────────────────────── --}}
<div class="modal fade modal-dark" id="problemeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Signaler un problème
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="probleme-article-info" class="mb-2" style="font-size:0.82rem; color:#8090a8;"></p>
                <textarea id="probleme-notes-input" class="form-control" rows="3"
                          placeholder="Pièce absente, mauvaise référence, fournisseur fermé, erreur de quantité..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger btn-sm px-4" id="probleme-confirm-btn">
                    <i class="fas fa-paper-plane me-1"></i>Envoyer
                </button>
            </div>
        </div>
    </div>
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const CHAUFFEUR_ID = {{ session('chauffeur_id') ?? 'null' }};
let currentProblemeLineId = null;

// ════════════════════════════════════════════════════════
// GROUPE FOURNISSEUR : toggle
// ════════════════════════════════════════════════════════
function toggleGroup(id) {
    const body = document.getElementById('fourn-body-' + id);
    const icon = document.getElementById('fourn-icon-' + id);
    if (!body) return;
    const isHidden = body.style.display === 'none';
    body.style.display = isHidden ? 'block' : 'none';
    icon.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
}

// ════════════════════════════════════════════════════════
// ZONE DE SCAN PAR LIGNE
// ════════════════════════════════════════════════════════
function toggleScan(lineId) {
    const zone = document.getElementById('scan-zone-' + lineId);
    const btn  = document.getElementById('btn-scan-' + lineId);
    const isActive = zone.classList.contains('active');

    // Fermer toutes les zones ouvertes
    document.querySelectorAll('.scan-zone.active').forEach(z => z.classList.remove('active'));
    document.querySelectorAll('.btn-scan.active').forEach(b => b.classList.remove('active'));

    if (!isActive) {
        zone.classList.add('active');
        btn.classList.add('active');
        const inp = document.getElementById('scan-input-' + lineId);
        inp.value = '';
        inp.focus();
        document.getElementById('scan-feedback-' + lineId).className = 'scan-feedback';
    }
}

// ════════════════════════════════════════════════════════
// SOUMETTRE UN SCAN
// ════════════════════════════════════════════════════════
function submitScan(lineId) {
    const inp      = document.getElementById('scan-input-' + lineId);
    const feedback = document.getElementById('scan-feedback-' + lineId);
    const barcode  = inp.value.trim();
    if (!barcode) { inp.focus(); return; }

    feedback.className = 'scan-feedback warning';
    feedback.textContent = '⏳ Vérification...';

    fetch('{{ route("chauffeur.scan") }}', {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ line_id: lineId, barcode: barcode, chauffeur_id: CHAUFFEUR_ID })
    })
    .then(r => r.json())
    .then(d => {
        if (d.matched) {
            feedback.className = 'scan-feedback success';
            feedback.textContent = d.message;
            setTimeout(() => markRecuperee(lineId), 1200);
        } else if (d.barcode_scanned) {
            // Code inconnu — proposer association
            feedback.className = 'scan-feedback warning';
            feedback.innerHTML = d.message +
                `<div class="mt-2 d-flex gap-2">
                    <button class="btn-validate-scan" onclick="confirmScan(${lineId}, '${d.barcode_scanned}')">
                        ✅ Oui, associer ce code
                    </button>
                    <button style="flex:0.4; background:#2d4a8a; color:#80b0ff; border:none; border-radius:6px; padding:8px; font-size:0.75rem; cursor:pointer;"
                            onclick="document.getElementById('scan-zone-${lineId}').classList.remove('active'); document.getElementById('btn-scan-${lineId}').classList.remove('active');">
                        Non
                    </button>
                </div>`;
        } else {
            feedback.className = 'scan-feedback error';
            feedback.textContent = '❌ Erreur : ' + (d.message || 'Réessayez');
        }
    })
    .catch(() => {
        feedback.className = 'scan-feedback error';
        feedback.textContent = '❌ Erreur réseau';
    });
}

// ════════════════════════════════════════════════════════
// CONFIRMER ASSOCIATION BARCODE
// ════════════════════════════════════════════════════════
function confirmScan(lineId, barcode) {
    fetch('{{ route("chauffeur.scan.confirm") }}', {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ line_id: lineId, barcode: barcode })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            const feedback = document.getElementById('scan-feedback-' + lineId);
            feedback.className = 'scan-feedback success';
            feedback.textContent = d.message;
            setTimeout(() => markRecuperee(lineId), 1200);
        }
    });
}

// ════════════════════════════════════════════════════════
// MARQUER COMME RÉCUPÉRÉE SANS SCAN
// ════════════════════════════════════════════════════════
function markDone(lineId) {
    if (!confirm('Marquer cette pièce comme récupérée ?')) return;

    fetch('{{ route("planning.statut") }}', {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ line_id: lineId, statut: 'recupere' })
    })
    .then(r => r.json())
    .then(d => { if (d.success) markRecuperee(lineId); });
}

// ════════════════════════════════════════════════════════
// MISE À JOUR VISUELLE
// ════════════════════════════════════════════════════════
function markRecuperee(lineId) {
    const card = document.getElementById('article-card-' + lineId);
    if (!card) return;
    card.classList.add('recupere');
    const dot = card.querySelector('.status-dot');
    if (dot) { dot.className = 'status-dot recupere'; }
    const btns = card.querySelector('.action-btns');
    if (btns) {
        btns.innerHTML = '<div class="result-recupere"><i class="fas fa-check-circle"></i> Récupérée</div>';
    }
    const zone = document.getElementById('scan-zone-' + lineId);
    if (zone) zone.classList.remove('active');

    // Mettre à jour le compteur du groupe
    updateGroupCounter(card);
}

function updateGroupCounter(card) {
    const group = card.closest('.fourn-group');
    if (!group) return;
    const total   = group.querySelectorAll('.article-card').length;
    const recupere = group.querySelectorAll('.article-card.recupere').length;
    const counter  = group.querySelector('.fourn-count');
    if (counter) {
        counter.textContent = recupere + '/' + total;
        if (recupere === total) counter.classList.add('done');
    }
}

// ════════════════════════════════════════════════════════
// SIGNALER UN PROBLÈME
// ════════════════════════════════════════════════════════
function openProbleme(lineId, code, name) {
    currentProblemeLineId = lineId;
    document.getElementById('probleme-article-info').textContent = code + ' — ' + name;
    document.getElementById('probleme-notes-input').value = '';
    new bootstrap.Modal(document.getElementById('problemeModal')).show();
    setTimeout(() => document.getElementById('probleme-notes-input').focus(), 400);
}

document.getElementById('probleme-confirm-btn').addEventListener('click', function () {
    const notes = document.getElementById('probleme-notes-input').value.trim();
    if (!notes) {
        document.getElementById('probleme-notes-input').classList.add('is-invalid');
        return;
    }
    this.disabled = true;
    this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Envoi...';

    fetch('{{ route("chauffeur.probleme") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ line_id: currentProblemeLineId, notes: notes })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            bootstrap.Modal.getInstance(document.getElementById('problemeModal')).hide();
            const card = document.getElementById('article-card-' + currentProblemeLineId);
            if (card) {
                card.classList.add('probleme');
                const btns = card.querySelector('.action-btns');
                if (btns) btns.innerHTML = `<div class="result-probleme"><i class="fas fa-exclamation-triangle me-1"></i>${notes}</div>`;
            }
        }
    })
    .finally(() => {
        this.disabled = false;
        this.innerHTML = '<i class="fas fa-paper-plane me-1"></i>Envoyer';
    });
});

// ════════════════════════════════════════════════════════
// SCAN GLOBAL (FAB)
// ════════════════════════════════════════════════════════
function openGlobalScan() {
    document.getElementById('globalScanOverlay').classList.add('active');
    document.getElementById('globalScanFeedback').innerHTML = '';
    setTimeout(() => document.getElementById('globalScanInput').focus(), 100);
}

function closeGlobalScan() {
    document.getElementById('globalScanOverlay').classList.remove('active');
    document.getElementById('globalScanInput').value = '';
    document.getElementById('globalScanFeedback').innerHTML = '';
}

document.getElementById('globalScanInput').addEventListener('keydown', function (e) {
    if (e.key !== 'Enter') return;
    const barcode  = this.value.trim();
    const feedback = document.getElementById('globalScanFeedback');
    if (!barcode) return;

    feedback.innerHTML = '<span style="color:#ffc107;">⏳ Recherche...</span>';

    // Chercher parmi toutes les cartes d'articles non récupérées
    const allCodes = document.querySelectorAll('[data-barcode]');
    let found = null;

    allCodes.forEach(el => {
        if (el.dataset.barcode === barcode && !el.closest('.article-card').classList.contains('recupere')) {
            found = el.dataset.lineId;
        }
    });

    if (found) {
        closeGlobalScan();
        // Ouvrir et pré-remplir le scan de la ligne trouvée
        const zone = document.getElementById('scan-zone-' + found);
        const btn  = document.getElementById('btn-scan-' + found);
        if (zone) {
            zone.classList.add('active');
            if (btn) btn.classList.add('active');
            const inp = document.getElementById('scan-input-' + found);
            if (inp) { inp.value = barcode; }
            submitScan(found);
            // Scroller vers la carte
            document.getElementById('article-card-' + found)?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    } else {
        feedback.innerHTML = '<span style="color:#ff8888;">❌ Code-barres non trouvé dans votre tournée du jour</span>';
        this.value = '';
    }
});

// ════════════════════════════════════════════════════════
// AUTO-ASSIGNATION
// ════════════════════════════════════════════════════════
function selfAssign(lineId) {
    var btn = document.getElementById('btn-assign-' + lineId);
    if (!btn) return;

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>...';

    fetch('{{ route("chauffeur.self_assign") }}', {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ line_id: lineId, chauffeur_id: CHAUFFEUR_ID })
    })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        if (d.success) {
            // Masquer la carte de la section "non assignées"
            var card = document.getElementById('nonassign-card-' + lineId);
            if (card) {
                card.style.transition = 'opacity 0.3s';
                card.style.opacity = '0';
                setTimeout(function() {
                    card.style.display = 'none';
                    // Recharger la page pour mettre à jour les deux sections
                    setTimeout(function() { location.reload(); }, 500);
                }, 300);
            }
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-hand-point-right me-1"></i> Je prends';
            alert(d.error || 'Erreur');
        }
    })
    .catch(function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-hand-point-right me-1"></i> Je prends';
        alert('Erreur réseau');
    });
}

// Écouter les scans clavier depuis le Panasonic (il envoie un Enter après le code)
document.addEventListener('keydown', function (e) {
    const overlay = document.getElementById('globalScanOverlay');
    // Si l'overlay n'est pas ouvert et qu'on appuie sur une touche alphanumérique
    // depuis le scanner → ouvrir automatiquement le scan global
    if (!overlay.classList.contains('active') &&
        e.key.length === 1 &&
        !e.ctrlKey && !e.altKey &&
        !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
        openGlobalScan();
        document.getElementById('globalScanInput').value = e.key;
    }
});
</script>
</body>
</html>