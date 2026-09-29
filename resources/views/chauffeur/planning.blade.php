<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>🚗 Ma Tournée — {{ $chauffeur->name }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        :root {
            --bg:     #F2F5FF;
            --white:  #FFFFFF;
            --navy:   #1E2D4A;
            --blue:   #3B82F6;
            --mint:   #10B981;
            --amber:  #F59E0B;
            --red:    #EF4444;
            --purple: #7C3AED;
            --text:   #1A2B4A;
            --sub:    #6B7A99;
            --border: #E2E8F0;
            --shadow: 0 2px 12px rgba(30,45,74,0.08);
        }
        body { background:var(--bg); color:var(--text); font-family:'Segoe UI',-apple-system,BlinkMacSystemFont,sans-serif; margin:0; padding-bottom:110px; }

        /* ══════════════════ TOP BAR ══════════════════ */
        .top-bar {
            background: linear-gradient(135deg, #1E2D4A 0%, #2D4A8A 100%);
            padding: 14px 16px 10px;
            position: sticky; top: 0; z-index: 500;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        .top-row1 { display:flex; justify-content:space-between; align-items:center; }
        .driver-info h1 { color:white; font-size:1.05rem; font-weight:800; margin:0; }
        .driver-info span { color:rgba(255,255,255,0.5); font-size:0.72rem; }
        .top-btns { display:flex; gap:6px; }
        .tbtn {
            background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.18);
            color:white; border-radius:8px; padding:7px 10px;
            font-size:0.72rem; font-weight:600; cursor:pointer;
            display:flex; align-items:center; gap:4px; text-decoration:none;
            transition:background 0.15s;
        }
        .tbtn:hover { background:rgba(255,255,255,0.2); color:white; }
        .tbtn.red { background:rgba(239,68,68,0.25); border-color:rgba(239,68,68,0.35); }

        /* Barre de progression */
        .prog-wrap { margin-top:12px; }
        .prog-labels { display:flex; justify-content:space-between; color:rgba(255,255,255,0.6); font-size:0.69rem; margin-bottom:5px; }
        .prog-labels strong { color:white; font-size:0.82rem; }
        .prog-track { background:rgba(255,255,255,0.15); border-radius:20px; height:7px; overflow:hidden; }
        .prog-fill { height:100%; border-radius:20px; background:linear-gradient(90deg,#10B981,#34D399); transition:width 0.6s cubic-bezier(.4,0,.2,1); }
        .prog-pills { display:flex; gap:6px; margin-top:8px; flex-wrap:wrap; }
        .ppill { border-radius:20px; padding:3px 10px; font-size:0.69rem; font-weight:700; }
        .ppill.t { background:rgba(255,255,255,0.12); color:rgba(255,255,255,0.8); }
        .ppill.g { background:rgba(16,185,129,0.28); color:#6EE7B7; }
        .ppill.o { background:rgba(245,158,11,0.28); color:#FCD34D; }
        .ppill.r { background:rgba(239,68,68,0.28); color:#FCA5A5; }

        /* ══════════════════ TABS ══════════════════════ */
        .tabs-bar { display:flex; background:white; border-bottom:2px solid var(--border); position:sticky; top:0; z-index:400; }
        .tab {
            flex:1; padding:12px 4px; text-align:center; font-size:0.76rem; font-weight:700;
            color:var(--sub); cursor:pointer; border-bottom:3px solid transparent;
            margin-bottom:-2px; transition:all 0.2s; position:relative;
        }
        .tab.active { color:var(--blue); border-bottom-color:var(--blue); background:#FAFBFF; }
        .tbadge {
    display: inline-block;
    min-width: 18px;
    height: 18px;
    border-radius: 9px;
    font-size: 0.6rem;
    font-weight: 800;
    color: white;
    padding: 0 4px;
    line-height: 18px;
    margin-left: 4px;
    vertical-align: middle;
}
        .tab-section { display:none; }
        .tab-section.active { display:block; }

        /* ══════════════════ PIÈCES LIBRES (accordéon) ══════════════════ */
        .libre-block {
            margin:12px 12px 0;
            border:2px solid var(--amber);
            border-radius:14px;
            overflow:hidden;
            background:#FFFBEB;
        }
        .libre-header {
            background:linear-gradient(135deg,#F59E0B,#D97706);
            padding:11px 16px;
            display:flex; justify-content:space-between; align-items:center;
            cursor:pointer; user-select:none;
        }
        .libre-title { color:white; font-weight:800; font-size:0.85rem; display:flex; align-items:center; gap:7px; }
        .libre-badge { background:rgba(0,0,0,0.2); color:white; border-radius:10px; padding:2px 9px; font-size:0.68rem; font-weight:700; }
        .libre-chevron { color:white; font-size:0.75rem; transition:transform 0.2s; }
        .libre-body { display:none; } /* fermé par défaut */
        .libre-fourn-label {
            padding:7px 16px; background:#FEF3C7;
            font-size:0.74rem; color:#78350F; font-weight:700;
            border-bottom:1px solid #FDE68A;
            display:flex; align-items:center; gap:6px;
        }
        .libre-line { padding:12px 16px; border-bottom:1px solid #FEF9C3; }
        .libre-line:last-child { border-bottom:none; }

        /* ══════════════════ CRÉNEAU ══════════════════════ */
        .slot-hdr { display:flex; align-items:center; gap:10px; margin:16px 12px 6px; }
        .slot-ico { width:34px; height:34px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1rem; flex-shrink:0; }
        .slot-ico.m { background:#FEF3C7; }
        .slot-ico.a { background:#EDE9FE; }
        .slot-txt { font-weight:800; font-size:0.88rem; color:var(--navy); line-height:1.2; }
        .slot-txt small { display:block; color:var(--sub); font-weight:500; font-size:0.68rem; }
        .slot-line { flex:1; height:1.5px; background:var(--border); }

        /* ══════════════════ CARTE FOURNISSEUR ═══════════ */
        .fc { margin:5px 12px; background:white; border-radius:14px; box-shadow:var(--shadow); overflow:hidden; border:1.5px solid var(--border); }
        .fc-header {
            padding:11px 15px; display:flex; justify-content:space-between; align-items:center;
            cursor:pointer; background:#F8FAFF; border-bottom:1.5px solid var(--border);
            user-select:none; -webkit-user-select:none;
        }
        .fc-name { font-weight:700; font-size:0.86rem; color:var(--navy); display:flex; align-items:center; gap:6px; }
        .fc-addr { font-size:0.67rem; color:var(--sub); margin-top:2px; }
        .fc-badge { border-radius:12px; padding:3px 9px; font-size:0.68rem; font-weight:700; white-space:nowrap; }
        .fc-badge.p { background:#FEF3C7; color:#92400E; }
        .fc-badge.d { background:#D1FAE5; color:#065F46; }
        .fc-chevron { color:var(--sub); font-size:0.72rem; transition:transform 0.2s; flex-shrink:0; }

        /* ══════════════════ ARTICLE CARD ═══════════════ */
        .ac { padding:14px 15px; border-bottom:1px solid #F0F5FF; background:white; }
        .ac:last-child { border-bottom:none; }
        .ac.done { background:#F0FDF4; opacity:0.72; }
        .ac.prob { background:#FFF5F5; }

        .ac-top { display:flex; align-items:flex-start; gap:9px; }
        .sdot { width:8px; height:8px; border-radius:50%; flex-shrink:0; margin-top:5px; }
        .sdot.en_attente { background:#CBD5E1; }
        .sdot.assigné    { background:var(--blue); }
        .sdot.en_route   { background:#0EA5E9; }
        .sdot.recupere   { background:var(--mint); }
        .sdot.probleme   { background:var(--red); box-shadow:0 0 5px var(--red); }

        .acode { font-family:'Courier New',monospace; font-weight:900; font-size:0.95rem; color:var(--blue); background:#EFF6FF; padding:3px 8px; border-radius:6px; border-left:3px solid var(--blue); letter-spacing:0.03em; display:inline-block; }
        .aname { font-size:0.79rem; color:var(--sub); margin-top:3px; line-height:1.4; }

        .mrow { display:flex; gap:4px; margin-top:8px; flex-wrap:wrap; }
        .mb { font-size:0.63rem; font-weight:600; padding:2px 7px; border-radius:7px; }
        .mb-doc  { background:#FFF7ED; color:#9A3412; }
        .mb-qty  { background:#ECFDF5; color:#065F46; }
        .mb-site { background:#EFF6FF; color:#1D4ED8; }
        .mb-slot { background:#F5F3FF; color:#5B21B6; }
        .mb-note { background:#FDF4FF; color:#7E22CE; }
        .mb-vend { background:#F0F9FF; color:#075985; }

        /* ══════════════════ BOUTONS ACTION ══════════════ */
        .arow { display:flex; gap:8px; margin-top:11px; align-items:stretch; }

        /* Scanner — prend toute la place disponible */
        .btn-sc {
            flex:1;
            background:var(--blue); color:white;
            border:none; border-radius:10px;
            padding:0 16px; font-size:0.82rem; font-weight:700;
            cursor:pointer; min-height:46px;
            display:flex; align-items:center; justify-content:center; gap:7px;
            transition:all 0.15s;
        }
        .btn-sc:active { transform:scale(0.97); filter:brightness(0.92); }
        .btn-sc.open { background:#1D4ED8; }

        /* ✓ Check vert — carré fixe */
        .btn-ok {
            width:46px; height:46px; flex-shrink:0;
            background:var(--mint); color:white;
            border:none; border-radius:10px;
            font-size:1.1rem; cursor:pointer;
            display:flex; align-items:center; justify-content:center;
            transition:all 0.15s;
        }
        .btn-ok:active { transform:scale(0.97); filter:brightness(0.9); }

        /* ⚠ Problème — carré fixe */
        .btn-pb {
            width:46px; height:46px; flex-shrink:0;
            background:#FFF1F2; color:var(--red);
            border:1.5px solid #FECDD3; border-radius:10px;
            font-size:1rem; cursor:pointer;
            display:flex; align-items:center; justify-content:center;
            transition:all 0.15s;
        }
        .btn-pb:active { transform:scale(0.97); }

        /* ══════════════════ ZONE SCAN ════════════════════ */
        .scan-zone { display:none; background:#F0F7FF; border:2px solid var(--blue); border-radius:10px; padding:12px; margin-top:10px; }
        .scan-zone.open { display:block; }
        .scan-hint { font-size:0.69rem; color:var(--sub); margin-bottom:8px; }
        .scan-hint code { background:#DBEAFE; color:#1D4ED8; padding:1px 5px; border-radius:4px; }
        .scan-inp { background:white; color:var(--text); border:2px solid var(--blue); border-radius:8px; padding:12px 14px; font-size:1.05rem; width:100%; outline:none; font-family:monospace; letter-spacing:0.05em; }
        .scan-inp:focus { box-shadow:0 0 0 3px rgba(59,130,246,0.18); }
        .btn-val-scan { background:var(--blue); color:white; border:none; border-radius:8px; padding:11px; font-size:0.82rem; font-weight:700; width:100%; cursor:pointer; margin-top:8px; min-height:42px; }
        .scan-fb { margin-top:8px; padding:9px 12px; border-radius:8px; font-size:0.77rem; display:none; }
        .scan-fb.ok  { display:block; background:#ECFDF5; color:#065F46; border:1px solid #A7F3D0; }
        .scan-fb.war { display:block; background:#FFFBEB; color:#92400E; border:1px solid #FDE68A; }
        .scan-fb.err { display:block; background:#FFF5F5; color:#9B1C1C; border:1px solid #FECACA; }

        /* ══════════════════ ÉTATS POST-ACTION ═══════════ */
        .res-ok {
            display:flex; align-items:center; gap:8px;
            background:#ECFDF5; color:#065F46;
            border:1px solid #A7F3D0; border-radius:10px;
            padding:10px 14px; font-weight:700; font-size:0.82rem; margin-top:10px;
        }
        .res-ok i { font-size:1rem; color:var(--mint); }

        /* 🚪 Livré directement au client */
        .btn-livre {
            display:flex; align-items:center; justify-content:center; gap:7px;
            width:100%; margin-top:8px;
            background:linear-gradient(135deg,#7C3AED,#5B21B6);
            color:white; border:none; border-radius:10px;
            padding:12px 14px; font-size:0.82rem; font-weight:700;
            cursor:pointer; min-height:44px; transition:all 0.15s;
        }
        .btn-livre:active { transform:scale(0.98); filter:brightness(0.92); }

        /* ↩ Annuler */
        .btn-annuler {
            display:flex; align-items:center; justify-content:center; gap:5px;
            width:100%; margin-top:6px;
            background:none; color:var(--sub);
            border:1.5px solid var(--border); border-radius:8px;
            padding:8px; font-size:0.75rem; font-weight:600;
            cursor:pointer; transition:all 0.15s;
        }
        .btn-annuler:hover { border-color:var(--red); color:var(--red); }

        .res-prob { color:var(--red); font-size:0.78rem; margin-top:8px; padding:9px 12px; background:#FFF5F5; border-radius:8px; border-left:3px solid var(--red); }

        /* ══════════════════ BOUTON JE PRENDS ════════════ */
        .btn-prends {
            display:flex; align-items:center; justify-content:center; gap:7px;
            width:100%; margin-top:10px;
            background:linear-gradient(135deg,#F59E0B,#D97706);
            color:white; border:none; border-radius:10px;
            padding:12px; font-size:0.82rem; font-weight:700;
            cursor:pointer; min-height:44px; transition:all 0.15s;
        }
        .btn-prends:active { transform:scale(0.98); }

        /* ══════════════════ TERMINÉES (done) ════════════ */
        .done-res { display:flex; align-items:center; gap:6px; color:var(--mint); font-size:0.78rem; font-weight:600; margin-top:8px; }

        /* ══════════════════ EMPTY STATE ════════════════ */
        .empty { text-align:center; padding:52px 24px; color:var(--sub); }
        .empty-ico { font-size:3.2rem; display:block; margin-bottom:14px; }
        .empty h5 { color:var(--navy); font-weight:700; margin-bottom:6px; }
        .empty p { font-size:0.82rem; }

        /* ══════════════════ FAB ═════════════════════════ */
        .fab { position:fixed; bottom:26px; right:20px; width:62px; height:62px; border-radius:50%; background:var(--blue); color:white; border:none; font-size:1.4rem; box-shadow:0 6px 24px rgba(59,130,246,0.5); cursor:pointer; z-index:600; display:flex; align-items:center; justify-content:center; transition:all 0.15s; }
        .fab:active { transform:scale(0.9); }

        /* ══════════════════ SCAN OVERLAY ════════════════ */
        .s-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.65); backdrop-filter:blur(5px); z-index:700; align-items:flex-end; justify-content:center; }
        .s-overlay.open { display:flex; }
        .s-box { background:white; border-radius:22px 22px 16px 16px; padding:24px 20px 28px; width:100%; max-width:480px; border-top:5px solid var(--blue); }
        .s-title { font-weight:800; color:var(--navy); font-size:1.02rem; margin-bottom:14px; display:flex; align-items:center; gap:8px; }
        .s-input { background:#EFF6FF; color:var(--text); border:2px solid var(--blue); border-radius:12px; padding:16px; font-size:1.3rem; width:100%; outline:none; font-family:monospace; text-align:center; letter-spacing:0.06em; }
        .s-fb { margin-top:10px; font-size:0.82rem; min-height:22px; }
        .s-close { background:#F1F5F9; color:var(--sub); border:none; border-radius:10px; padding:12px; font-size:0.82rem; font-weight:700; width:100%; cursor:pointer; margin-top:12px; }

        /* ══════════════════ MODAL PROBLÈME ══════════════ */
        .modal-content { border-radius:20px!important; border:none!important; box-shadow:0 20px 60px rgba(0,0,0,0.2)!important; }
        .modal-header { border-color:var(--border)!important; }
        .modal-footer { border-color:var(--border)!important; }
        .form-control:focus { border-color:var(--blue)!important; box-shadow:0 0 0 3px rgba(59,130,246,0.15)!important; }

        /* ══════════════════ DROPDOWN ════════════════════ */
        .dropdown-menu { border-radius:12px; border:1.5px solid var(--border); box-shadow:0 8px 24px rgba(0,0,0,0.12); min-width:180px; }
        .dropdown-item { font-size:0.82rem; padding:8px 16px; }
        .dropdown-item.active { background:var(--blue); }





        [id^="post-"] { display: none; }
[id^="post-"].visible { display: block !important; }
    </style>
</head>
<body>

{{-- ══ TOP BAR ══════════════════════════════════════════════════ --}}
@php
    $totalPieces = $stats['total'];
    $recuperees  = $stats['recupere'];
    $restantes   = $stats['restant'];
    $problemes   = $stats['probleme'] ?? 0;
    $pct         = $totalPieces > 0 ? round(($recuperees / $totalPieces) * 100) : 0;

    $nonAssignees = \App\Models\TourneeLine::with(['site','fournisseur'])
        ->whereDate('date_tournee', today())
        ->whereNull('chauffeur_id')
        ->whereNotIn('statut', ['recupere','au_magasin','livre_client'])
        ->when($currentSiteId ?? null, fn($q) => $q->where('site_id', $currentSiteId))
        ->orderBy('fournisseur_name')->get();
    $countLibres = $nonAssignees->count();

    $allCreneaux = [
        '9h-11h'     => ['icon'=>'🌅','label'=>'9h – 11h',  'apm'=>false],
        '11h-12h'    => ['icon'=>'🕚','label'=>'11h – 12h', 'apm'=>false],
        '13h-14h'    => ['icon'=>'🌞','label'=>'13h – 14h', 'apm'=>true],
        '15h-16h'    => ['icon'=>'🕒','label'=>'15h – 16h', 'apm'=>true],
        '17h-18h'    => ['icon'=>'🌇','label'=>'17h – 18h', 'apm'=>true],
        'matin'      => ['icon'=>'🌅','label'=>'Matin',      'apm'=>false],
        'apres_midi' => ['icon'=>'🌇','label'=>'Après-midi', 'apm'=>true],
    ];
    $slotIcons = ['9h-11h'=>'🌅','11h-12h'=>'🕚','13h-14h'=>'🌞','15h-16h'=>'🕒','17h-18h'=>'🌇','matin'=>'🌅','apres_midi'=>'🌇'];
@endphp

<div class="top-bar">
    <div class="top-row1">
        <div class="driver-info">
            <h1><i class="fas fa-truck" style="opacity:.7;font-size:.85rem;margin-right:6px;"></i>{{ $chauffeur->name }}</h1>
            <span>{{ now()->format('l d/m/Y') }}</span>
        </div>
        <div class="top-btns">
            @if(!$chauffeur->site_id || $sites->count() > 1)
            <div class="dropdown">
                <button class="tbtn" data-bs-toggle="dropdown">
                    <i class="fas fa-store"></i>
                    {{ $currentSiteId ? optional($sites->find($currentSiteId))->name : 'Tous' }}
                    <i class="fas fa-chevron-down" style="font-size:.55rem;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><form method="POST" action="{{ route('chauffeur.switch_site') }}">@csrf<input type="hidden" name="site_id" value=""><button class="dropdown-item {{ !$currentSiteId?'active':'' }}">🌐 Tous les sites</button></form></li>
                    @foreach($sites as $site)
                    <li><form method="POST" action="{{ route('chauffeur.switch_site') }}">@csrf<input type="hidden" name="site_id" value="{{ $site->id }}"><button class="dropdown-item {{ $currentSiteId==$site->id?'active':'' }}">🏪 {{ $site->name }}</button></form></li>
                    @endforeach
                </ul>
            </div>
            @endif
            <button onclick="location.reload()" class="tbtn"><i class="fas fa-sync-alt"></i></button>
            <form action="{{ route('chauffeur.logout') }}" method="POST" class="mb-0">@csrf
                <button type="submit" class="tbtn red"><i class="fas fa-sign-out-alt"></i></button>
            </form>
        </div>
    </div>

    <div class="prog-wrap">
        <div class="prog-labels">
            <span>Progression <strong>{{ $pct }}%</strong></span>
            <span><strong>{{ $recuperees }}</strong>/{{ $totalPieces }} pièces</span>
        </div>
        <div class="prog-track"><div class="prog-fill" style="width:{{ $pct }}%"></div></div>
        <div class="prog-pills">
            <span class="ppill t">📦 {{ $totalPieces }}</span>
            @if($restantes > 0)<span class="ppill o">⏳ {{ $restantes }} restante{{ $restantes>1?'s':'' }}</span>
            @else<span class="ppill g">🎉 Tout récupéré !</span>@endif
            @if($problemes > 0)<span class="ppill r">⚠️ {{ $problemes }}</span>@endif
            @if($countLibres > 0)<span class="ppill" style="background:rgba(245,158,11,.28);color:#FCD34D;">🆓 {{ $countLibres }} libre{{ $countLibres>1?'s':'' }}</span>@endif
        </div>
    </div>
</div>

{{-- ══ TABS ══════════════════════════════════════════════════════ --}}
<div class="tabs-bar">
    <div class="tab active" onclick="switchTab('restant')" id="tab-restant">
    À faire @if($restantes > 0)<span class="tbadge" style="background:var(--red);">{{ $restantes }}</span>@endif
</div>
<div class="tab" onclick="switchTab('done')" id="tab-done">
    Terminées @if($recuperees > 0)<span class="tbadge" style="background:var(--mint);">{{ $recuperees }}</span>@endif
</div>
@if($countLibres > 0)
<div class="tab" onclick="switchTab('libre')" id="tab-libre">
    Libres <span class="tbadge" style="background:var(--amber);">{{ $countLibres }}</span>
</div>
@endif
</div>

{{-- ══════════════════════════════════════════════════════════════
     ONGLET : À FAIRE
══════════════════════════════════════════════════════════════ --}}
<div class="tab-section active" id="section-restant">

    {{-- Pièces libres en accordéon FERMÉ par défaut --}}
    @if($countLibres > 0)
    <div class="libre-block">
        <div class="libre-header" onclick="toggleLibre()">
            <div class="libre-title">
                <i class="fas fa-layer-group"></i>
                Pièces non assignées
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="libre-badge">{{ $countLibres }}</span>
                <i class="fas fa-chevron-down libre-chevron" id="libre-chevron"></i>
            </div>
        </div>
        <div class="libre-body" id="libre-body">
            @foreach($nonAssignees->groupBy('fournisseur_name') as $fourn => $lignes)
            <div>
                <div class="libre-fourn-label">
                    <i class="fas fa-industry"></i>{{ $fourn ?: 'Fournisseur non défini' }}
                    <span style="color:var(--sub);font-weight:400;">— {{ optional($lignes->first()->site)->name }}</span>
                </div>
                @foreach($lignes as $ligne)
                <div class="libre-line" id="nonassign-{{ $ligne->id }}">
                    <span class="acode">{{ $ligne->article_code }}</span>
                    <div class="aname">{{ $ligne->article_name }}</div>
                    <div class="mrow">
                        <span class="mb mb-doc">📄 {{ $ligne->source_numdoc }}</span>
                        <span class="mb mb-qty">×{{ number_format($ligne->quantity,0) }}</span>
                        <span class="mb mb-slot">{{ $slotIcons[$ligne->slot]??'🕐' }} {{ $ligne->slot }}</span>
                        @if($ligne->created_by_name)<span class="mb mb-vend">👤 {{ $ligne->created_by_name }}</span>@endif
                        @if($ligne->notes)<span class="mb mb-note">📝 {{ Str::limit($ligne->notes,30) }}</span>@endif
                    </div>
                    <button class="btn-prends" id="btn-prends-{{ $ligne->id }}" onclick="selfAssign({{ $ligne->id }}, 'nonassign-{{ $ligne->id }}', 'btn-prends-{{ $ligne->id }}')">
                        <i class="fas fa-hand-point-right"></i> Je prends cette pièce
                    </button>
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Pièces assignées par créneau --}}
    @php $hasRestant = false; @endphp
    @foreach($lignesParCreneau as $slotKey => $lignesParFourn)
        @php
            $lr = collect();
            foreach ($lignesParFourn as $f => $ll) {
                $en = $ll->filter(fn($l) => !in_array($l->statut,['recupere','au_magasin','livre_client']));
                if ($en->isNotEmpty()) $lr[$f] = $en;
            }
        @endphp
        @if($lr->isNotEmpty())
            @php
                $hasRestant = true;
                $si = $allCreneaux[$slotKey] ?? ['icon'=>'🕐','label'=>$slotKey,'apm'=>false];
                $total = $lr->flatten()->count();
            @endphp
            <div class="slot-hdr">
                <div class="slot-ico {{ $si['apm']?'a':'m' }}">{{ $si['icon'] }}</div>
                <div class="slot-txt">{{ $si['label'] }}<small>{{ $total }} pièce{{ $total>1?'s':'' }}</small></div>
                <div class="slot-line"></div>
            </div>
            @foreach($lr as $fournisseurName => $lignes)
                @php $gid = 'r-'.Str::slug($slotKey).'-'.Str::slug($fournisseurName); @endphp
                <div class="fc">
                    <div class="fc-header" onclick="toggleFC('{{ $gid }}')">
                        <div>
                            <div class="fc-name"><i class="fas fa-industry" style="color:var(--amber);font-size:.8rem;"></i>{{ $fournisseurName ?: 'Fournisseur non défini' }}</div>
                            @php $adr = optional($lignes->first()->fournisseur)->address; @endphp
                            @if($adr)<div class="fc-addr"><i class="fas fa-map-marker-alt me-1"></i>{{ $adr }}</div>@endif
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span class="fc-badge p" id="badge-{{ $gid }}">⏳ {{ $lignes->count() }}</span>
                            <i class="fas fa-chevron-down fc-chevron" id="chev-{{ $gid }}"></i>
                        </div>
                    </div>
                    <div id="body-{{ $gid }}">
                        @foreach($lignes as $ligne)
                        <div class="ac" id="ac-{{ $ligne->id }}">
                            <div class="ac-top">
                                <div class="sdot {{ $ligne->statut }}"></div>
                                <div style="flex:1;">
                                    <span class="acode">{{ $ligne->article_code }}</span>
                                    <div class="aname">{{ $ligne->article_name }}</div>
                                </div>
                            </div>
                            <div class="mrow">
                                <span class="mb mb-doc">📄 {{ $ligne->source_numdoc }}</span>
                                <span class="mb mb-qty">×{{ number_format($ligne->quantity,0) }}</span>
                                <span class="mb mb-site">🏪 {{ optional($ligne->site)->name }}</span>
                                <span class="mb mb-slot">{{ $slotIcons[$ligne->slot]??'🕐' }} {{ $ligne->slot }}</span>
                                @if($ligne->created_by_name)<span class="mb mb-vend">👤 {{ $ligne->created_by_name }}</span>@endif
                                @if($ligne->notes)<span class="mb mb-note">📝 {{ Str::limit($ligne->notes,30) }}</span>@endif
                            </div>

                            {{-- ══ 3 BOUTONS : Scanner | ✓ | ⚠ ══ --}}
                            <div class="arow" id="arow-{{ $ligne->id }}">
                                <button type="button" class="btn-sc" id="bsc-{{ $ligne->id }}"
                                        onclick="toggleScan({{ $ligne->id }})">
                                    <i class="fas fa-barcode"></i> Scanner
                                </button>
                                <button type="button" class="btn-ok" id="bok-{{ $ligne->id }}"
                                        onclick="markDone({{ $ligne->id }})"
                                        title="Récupérée sans scan">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button type="button" class="btn-pb"
                                        onclick="openProbleme({{ $ligne->id }},'{{ $ligne->article_code }}','{{ addslashes($ligne->article_name) }}')"
                                        title="Signaler un problème">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </button>
                            </div>

                            {{-- Zone de scan --}}
                            <div class="scan-zone" id="sz-{{ $ligne->id }}">
                                <div class="scan-hint">
                                    @if($ligne->barcode)Code attendu : <code>{{ $ligne->barcode }}</code>
                                    @else<span style="color:var(--amber);">Pas de code — scanner pour associer</span>@endif
                                </div>
                                <input type="text" class="scan-inp" id="si-{{ $ligne->id }}"
                                       placeholder="Scanner le code-barres..." autocomplete="off"
                                       onkeydown="if(event.key==='Enter'){event.preventDefault();submitScan({{ $ligne->id }});}">
                                <button type="button" class="btn-val-scan" onclick="submitScan({{ $ligne->id }})">
                                    <i class="fas fa-check me-1"></i> Valider
                                </button>
                                <div class="scan-fb" id="sfb-{{ $ligne->id }}"></div>
                            </div>

                            {{-- Boutons post-récupération (cachés au départ) --}}
                            <div id="post-{{ $ligne->id }}" style="display:none;">
                                <div class="res-ok" id="res-{{ $ligne->id }}">
                                    <i class="fas fa-check-circle"></i> Récupérée avec succès !
                                </div>
                                <button type="button" class="btn-livre" id="bliv-{{ $ligne->id }}"
                                        onclick="livreClient({{ $ligne->id }})">
                                    🚪 Livré directement au client
                                </button>
                                <button type="button" class="btn-annuler" onclick="annulerScan({{ $ligne->id }})">
                                    <i class="fas fa-undo"></i> Annuler — remettre en attente
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    @endforeach

    @if(!$hasRestant && $countLibres === 0)
    <div class="empty">
        <span class="empty-ico">🎉</span>
        <h5>Tournée terminée !</h5>
        <p>Toutes les pièces ont été récupérées. Excellent travail !</p>
    </div>
    @elseif(!$hasRestant)
    <div class="empty" style="padding:24px;">
        <p style="color:var(--sub);font-size:.82rem;">Vos pièces sont toutes traitées.<br>Des pièces libres sont disponibles ci-dessus.</p>
    </div>
    @endif
</div>

{{-- ══════════════════════════════════════════════════════════════
     ONGLET : TERMINÉES
══════════════════════════════════════════════════════════════ --}}
<div class="tab-section" id="section-done">
    @php $hasDone = false; @endphp
    @foreach($lignesParCreneau as $slotKey => $lignesParFourn)
        @php
            $ld = collect();
            foreach ($lignesParFourn as $f => $ll) {
                $t = $ll->filter(fn($l) => in_array($l->statut,['recupere','au_magasin','livre_client']));
                if ($t->isNotEmpty()) $ld[$f] = $t;
            }
        @endphp
        @if($ld->isNotEmpty())
            @php $hasDone = true; $si = $allCreneaux[$slotKey]??['icon'=>'🕐','label'=>$slotKey,'apm'=>false]; $td = $ld->flatten()->count(); @endphp
            <div class="slot-hdr">
                <div class="slot-ico {{ $si['apm']?'a':'m' }}">{{ $si['icon'] }}</div>
                <div class="slot-txt">{{ $si['label'] }}<small>{{ $td }} pièce{{ $td>1?'s':'' }}</small></div>
                <div class="slot-line"></div>
            </div>
            @foreach($ld as $fournisseurName => $lignes)
                @php $gid = 'd-'.Str::slug($slotKey).'-'.Str::slug($fournisseurName); @endphp
                <div class="fc">
                    <div class="fc-header" onclick="toggleFC('{{ $gid }}')">
                        <div><div class="fc-name"><i class="fas fa-industry" style="color:var(--mint);font-size:.8rem;"></i>{{ $fournisseurName ?: 'Non défini' }}</div></div>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span class="fc-badge d">✅ {{ $lignes->count() }}</span>
                            <i class="fas fa-chevron-down fc-chevron" id="chev-{{ $gid }}"></i>
                        </div>
                    </div>
                    <div id="body-{{ $gid }}" style="display:none;">
                        @foreach($lignes as $ligne)
                        <div class="ac done">
                            <div class="ac-top">
                                <div class="sdot recupere"></div>
                                <div style="flex:1;">
                                    <span class="acode">{{ $ligne->article_code }}</span>
                                    <div class="aname">{{ $ligne->article_name }}</div>
                                </div>
                            </div>
                            <div class="mrow">
                                <span class="mb mb-doc">📄 {{ $ligne->source_numdoc }}</span>
                                <span class="mb mb-qty">×{{ number_format($ligne->quantity,0) }}</span>
                            </div>
                            <div class="done-res">
    <i class="fas fa-check-circle" style="color:var(--mint);"></i>
    @if($ligne->statut==='livre_client') Livré au client
    @elseif($ligne->statut==='au_magasin') Au magasin
    @else Récupéré @if($ligne->scanned_at)— {{ $ligne->scanned_at->format('H:i') }}@endif
        @if($ligne->scanned_barcode)<code style="font-size:.65rem;margin-left:4px;">{{ $ligne->scanned_barcode }}</code>@endif
    @endif
</div>
@if($ligne->statut !== 'livre_client')
<button type="button" class="btn-annuler" onclick="annulerScan({{ $ligne->id }})"
        style="margin-top:6px;">
    <i class="fas fa-undo"></i> Annuler — remettre en attente
</button>
@endif
                        </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    @endforeach
    @if(!$hasDone)
    <div class="empty"><span class="empty-ico">📦</span><h5>Aucune pièce terminée</h5><p>Les pièces récupérées apparaîtront ici.</p></div>
    @endif
</div>

{{-- ══════════════════════════════════════════════════════════════
     ONGLET : LIBRES
══════════════════════════════════════════════════════════════ --}}
@if($countLibres > 0)
<div class="tab-section" id="section-libre">
    @foreach($nonAssignees->groupBy('fournisseur_name') as $fourn => $lignes)
        @php $gid2 = 'fl-'.Str::slug($fourn); @endphp
        <div class="fc" style="margin-top:10px;">
            <div class="fc-header" onclick="toggleFC('{{ $gid2 }}')">
                <div>
                    <div class="fc-name"><i class="fas fa-industry" style="color:var(--amber);font-size:.8rem;"></i>{{ $fourn ?: 'Non défini' }}</div>
                    <div class="fc-addr">{{ optional($lignes->first()->site)->name }}</div>
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <span class="fc-badge p">🆓 {{ $lignes->count() }}</span>
                    <i class="fas fa-chevron-down fc-chevron" id="chev-{{ $gid2 }}"></i>
                </div>
            </div>
            <div id="body-{{ $gid2 }}">
                @foreach($lignes as $ligne)
                <div class="ac" id="fl-{{ $ligne->id }}">
                    <div class="ac-top">
                        <div class="sdot en_attente"></div>
                        <div style="flex:1;">
                            <span class="acode">{{ $ligne->article_code }}</span>
                            <div class="aname">{{ $ligne->article_name }}</div>
                        </div>
                    </div>
                    <div class="mrow">
                        <span class="mb mb-doc">📄 {{ $ligne->source_numdoc }}</span>
                        <span class="mb mb-qty">×{{ number_format($ligne->quantity,0) }}</span>
                        <span class="mb mb-slot">{{ $slotIcons[$ligne->slot]??'🕐' }} {{ $ligne->slot }}</span>
                        @if($ligne->created_by_name)<span class="mb mb-vend">👤 {{ $ligne->created_by_name }}</span>@endif
                        @if($ligne->notes)<span class="mb mb-note">📝 {{ Str::limit($ligne->notes,30) }}</span>@endif
                    </div>
                    <button type="button" class="btn-prends" id="bfl-{{ $ligne->id }}"
                            onclick="selfAssign({{ $ligne->id }},'fl-{{ $ligne->id }}','bfl-{{ $ligne->id }}')">
                        <i class="fas fa-hand-point-right"></i> Je prends cette pièce
                    </button>
                </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
@endif

{{-- ══ FAB ══════════════════════════════════════════════════════ --}}
<button type="button" class="fab" onclick="openGS()" title="Scanner"><i class="fas fa-barcode"></i></button>

{{-- ══ SCAN OVERLAY ══════════════════════════════════════════════ --}}
<div class="s-overlay" id="gsOverlay" onclick="if(event.target===this)closeGS()">
    <div class="s-box">
        <div class="s-title"><i class="fas fa-barcode" style="color:var(--blue);"></i> Scanner une pièce</div>
        <input type="text" id="gsInput" class="s-input" placeholder="Scanner ici..." autocomplete="off">
        <div id="gsFb" class="s-fb"></div>
        <button type="button" class="s-close" onclick="closeGS()"><i class="fas fa-times me-1"></i> Fermer</button>
    </div>
</div>

{{-- ══ MODAL PROBLÈME ════════════════════════════════════════════ --}}
<div class="modal fade" id="probModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" style="color:var(--red);font-weight:700;"><i class="fas fa-exclamation-triangle me-2"></i>Signaler un problème</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="prob-info" style="font-size:.82rem;color:var(--sub);background:var(--bg);padding:9px 12px;border-radius:8px;margin-bottom:12px;"></p>
                <textarea id="prob-notes" class="form-control" rows="3"
                          placeholder="Pièce absente, mauvaise référence, fournisseur fermé..."
                          style="border-radius:10px;border:1.5px solid var(--border);font-size:.85rem;resize:none;"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger btn-sm px-4" id="prob-send"><i class="fas fa-paper-plane me-1"></i> Envoyer</button>
            </div>
        </div>
    </div>
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const CID  = {{ session('chauffeur_id') ?? 'null' }};
let probLineId = null;

/* ══ TABS ══════════════════════════════════════════════════════ */
function switchTab(n) {
    document.querySelectorAll('.tab-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    document.getElementById('section-' + n).classList.add('active');
    document.getElementById('tab-' + n).classList.add('active');
}

/* ══ ACCORDÉON LIBRES (fermé par défaut) ══════════════════════ */
function toggleLibre() {
    const body   = document.getElementById('libre-body');
    const chev   = document.getElementById('libre-chevron');
    const isOpen = body.style.display === 'block';
    body.style.display = isOpen ? 'none' : 'block';
    chev.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
}

/* ══ TOGGLE FOURNISSEUR ════════════════════════════════════════ */
function toggleFC(id) {
    const body = document.getElementById('body-' + id);
    const chev = document.getElementById('chev-' + id);
    if (!body) return;
    const open = body.style.display === 'block';
    body.style.display = open ? 'none' : 'block';
    if (chev) chev.style.transform = open ? 'rotate(0deg)' : 'rotate(180deg)';
}

/* ══ SCAN PAR LIGNE ════════════════════════════════════════════ */
function toggleScan(id) {
    const sz  = document.getElementById('sz-' + id);
    const btn = document.getElementById('bsc-' + id);
    const isOpen = sz.classList.contains('open');
    // Fermer les autres
    document.querySelectorAll('.scan-zone.open').forEach(z => z.classList.remove('open'));
    document.querySelectorAll('.btn-sc.open').forEach(b => b.classList.remove('open'));
    if (!isOpen) {
        sz.classList.add('open');
        btn.classList.add('open');
        const inp = document.getElementById('si-' + id);
        if (inp) { inp.value = ''; inp.focus(); }
        const fb = document.getElementById('sfb-' + id);
        if (fb) fb.className = 'scan-fb';
    }
}

function submitScan(id) {
    const inp = document.getElementById('si-' + id);
    const fb  = document.getElementById('sfb-' + id);
    const bc  = inp ? inp.value.trim() : '';
    if (!bc) { if (inp) inp.focus(); return; }
    fb.className = 'scan-fb war'; fb.textContent = '⏳ Vérification...';

    fetch('{{ route("chauffeur.scan") }}', {
        method:'POST', credentials:'same-origin',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({ line_id:id, barcode:bc, chauffeur_id:CID })
    })
    .then(r=>r.json())
    .then(d=>{
        if (d.matched) {
            fb.className='scan-fb ok'; fb.textContent=d.message;
            setTimeout(()=>markRec(id), 900);
        } else if (d.barcode_scanned) {
            fb.className='scan-fb war'; fb.textContent='🔄 Association en cours...';
            confirmScan(id, d.barcode_scanned);
        } else {
            fb.className='scan-fb err'; fb.textContent='❌ '+(d.message||'Réessayez');
        }
    })
    .catch(()=>{ fb.className='scan-fb err'; fb.textContent='❌ Erreur réseau'; });
}

function confirmScan(id, bc) {
    fetch('{{ route("chauffeur.scan.confirm") }}', {
        method:'POST', credentials:'same-origin',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({ line_id:id, barcode:bc })
    })
    .then(r=>r.json())
    .then(d=>{
        if (d.success) {
            const fb=document.getElementById('sfb-'+id);
            if (fb){fb.className='scan-fb ok'; fb.textContent=d.message;}
            setTimeout(()=>markRec(id), 900);
        }
    });
}

/* ══ ✓ BOUTON CHECK VERT (sans scan) ══════════════════════════ */
function markDone(id) {
    if (!confirm('Marquer comme récupérée sans scanner ?')) return;
    fetch('{{ route("planning.statut") }}', {
        method:'POST', credentials:'same-origin',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({ line_id:id, statut:'recupere' })
    })
    .then(r=>r.json())
    .then(d=>{ if (d.success) markRec(id); });
}

/* ══ MISE À JOUR VISUELLE ═══════════════════════════════════════
   Affiche : ✅ Récupérée | 🚪 Livré client | ↩ Annuler
═══════════════════════════════════════════════════════════════ */
function markRec(id) {
    const card = document.getElementById('ac-' + id);
    if (!card) return;

    card.classList.remove('prob');
    card.classList.add('done');

    // 1. Cacher les 3 boutons d'action
    const arow = document.getElementById('arow-' + id);
if (arow) arow.style.setProperty('display', 'none', 'important');

    // 2. Fermer la zone de scan
    const sz = document.getElementById('sz-' + id);
    if (sz) sz.classList.remove('open');

    // 3. Afficher le bloc post-récupération (déjà dans le HTML, juste caché)
const post = document.getElementById('post-' + id);
if (post) post.classList.add('visible');

    // 4. Màj badge groupe fournisseur
    updateBadge(card);
}

function updateBadge(card) {
    const fc    = card.closest('.fc');
    if (!fc) return;
    const total = fc.querySelectorAll('.ac').length;
    const done  = fc.querySelectorAll('.ac.done').length;
    const badge = fc.querySelector('.fc-badge');
    if (!badge) return;
    if (done === total) { badge.className='fc-badge d'; badge.textContent='✅ '+total; }
    else                { badge.className='fc-badge p'; badge.textContent='⏳ '+(total-done); }
}

/* ══ 🚪 LIVRÉ DIRECTEMENT AU CLIENT ═══════════════════════════ */
function livreClient(id) {
    if (!confirm('Confirmer la livraison directe au client ?')) return;
    const btn = document.getElementById('bliv-' + id);
    if (btn) { btn.disabled=true; btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> En cours...'; }

    fetch('{{ route("chauffeur.livre_client") }}', {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({ line_id:id, chauffeur_id:CID })
    })
    .then(r=>r.json())
    .then(d=>{
        if (d.success) {
            const post = document.getElementById('post-' + id);
            if (post) post.innerHTML = '<div class="res-ok"><i class="fas fa-check-circle"></i> Livré directement au client ✅</div>';
            const res = document.getElementById('res-' + id);
            if (res) res.style.display = 'none';
        } else {
            if (btn) { btn.disabled=false; btn.innerHTML='🚪 Livré directement au client'; }
            alert(d.error || 'Erreur');
        }
    })
    .catch(()=>{
        if (btn) { btn.disabled=false; btn.innerHTML='🚪 Livré directement au client'; }
        alert('Erreur réseau');
    });
}

/* ══ ↩ ANNULER SCAN ════════════════════════════════════════════ */
function annulerScan(id) {
    if (!confirm('Annuler et remettre en attente ?')) return;
    fetch('{{ route("planning.statut") }}', {
        method:'POST', credentials:'same-origin',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({ line_id:id, statut:'en_attente' })
    })
    .then(r=>r.json())
    .then(d=>{ if (d.success) location.reload(); });
}

/* ══ PROBLÈME ══════════════════════════════════════════════════ */
function openProbleme(id, code, name) {
    probLineId = id;
    document.getElementById('prob-info').textContent = code + ' — ' + name;
    document.getElementById('prob-notes').value = '';
    document.getElementById('prob-notes').classList.remove('is-invalid');
    new bootstrap.Modal(document.getElementById('probModal')).show();
    setTimeout(()=>document.getElementById('prob-notes').focus(), 400);
}
document.getElementById('prob-send').addEventListener('click', function() {
    const notes = document.getElementById('prob-notes').value.trim();
    if (!notes) { document.getElementById('prob-notes').classList.add('is-invalid'); return; }
    this.disabled=true; this.innerHTML='<i class="fas fa-spinner fa-spin me-1"></i>Envoi...';
    fetch('{{ route("chauffeur.probleme") }}', {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({ line_id:probLineId, notes:notes })
    })
    .then(r=>r.json())
    .then(d=>{
        if (d.success) {
            bootstrap.Modal.getInstance(document.getElementById('probModal')).hide();
            const card = document.getElementById('ac-' + probLineId);
            if (card) {
                card.classList.add('prob');
                const arow = document.getElementById('arow-' + probLineId);
                if (arow) arow.innerHTML = '<div class="res-prob"><i class="fas fa-exclamation-triangle me-1"></i>'+notes+'</div>';
            }
        }
    })
    .finally(()=>{ this.disabled=false; this.innerHTML='<i class="fas fa-paper-plane me-1"></i>Envoyer'; });
});

/* ══ AUTO-ASSIGNATION ══════════════════════════════════════════ */
function selfAssign(id, cardId, btnId) {
    const btn  = document.getElementById(btnId);
    const card = document.getElementById(cardId);
    if (btn) { btn.disabled=true; btn.innerHTML='<i class="fas fa-spinner fa-spin me-1"></i> Attribution...'; }
    fetch('{{ route("chauffeur.self_assign") }}', {
        method:'POST', credentials:'same-origin',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({ line_id:id, chauffeur_id:CID })
    })
    .then(r=>r.json())
    .then(d=>{
        if (d.success) {
            if (card) { card.style.opacity='0'; card.style.transition='opacity .3s'; }
            setTimeout(()=>location.reload(), 500);
        } else {
            if (btn) { btn.disabled=false; btn.innerHTML='<i class="fas fa-hand-point-right"></i> Je prends cette pièce'; }
        }
    });
}

/* ══ SCAN GLOBAL FAB ═══════════════════════════════════════════ */
function openGS() {
    document.getElementById('gsOverlay').classList.add('open');
    document.getElementById('gsFb').innerHTML='';
    setTimeout(()=>document.getElementById('gsInput').focus(), 100);
}
function closeGS() {
    document.getElementById('gsOverlay').classList.remove('open');
    document.getElementById('gsInput').value='';
    document.getElementById('gsFb').innerHTML='';
}
document.getElementById('gsInput').addEventListener('keydown', function(e) {
    if (e.key !== 'Enter') return;
    const bc = this.value.trim();
    const fb = document.getElementById('gsFb');
    if (!bc) return;
    fb.innerHTML='<span style="color:var(--amber);">⏳ Recherche...</span>';
    const els = document.querySelectorAll('[data-barcode]');
    let found = null;
    els.forEach(el=>{ if (el.dataset.barcode===bc && !el.closest('.ac').classList.contains('done')) found=el.dataset.lineId; });
    if (found) {
        closeGS(); switchTab('restant');
        toggleScan(found);
        const inp = document.getElementById('si-' + found);
        if (inp) inp.value = bc;
        submitScan(found);
        document.getElementById('ac-'+found)?.scrollIntoView({behavior:'smooth',block:'center'});
    } else {
        fb.innerHTML='<span style="color:var(--red);">❌ Code non trouvé dans votre tournée</span>';
        this.value='';
    }
});

/* ══ CLAVIER PANASONIC (auto-open global scan) ════════════════ */
document.addEventListener('keydown', function(e) {
    const ov = document.getElementById('gsOverlay');
    if (!ov.classList.contains('open') && e.key.length===1 && !e.ctrlKey && !e.altKey &&
        !['INPUT','TEXTAREA'].includes(document.activeElement.tagName)) {
        openGS();
        document.getElementById('gsInput').value = e.key;
    }
});

// Auto-refresh 60s
// setTimeout(()=>location.reload(), 60000);
</script>
</body>
</html>