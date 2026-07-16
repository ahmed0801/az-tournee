<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi Tournée — {{ $numdoc }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta http-equiv="refresh" content="30">
    <style>
        body { background:#f0f4f8; font-family:'Segoe UI',sans-serif; }
        .top-header { background:linear-gradient(135deg,#1a2b4a,#2d4a8a); color:white; padding:20px 24px; margin-bottom:24px; }
        .top-header h4 { margin:0; font-weight:700; font-size:1.2rem; }
        .numdoc { font-family:monospace; font-size:1.4rem; font-weight:900; color:#90c0ff; }
        .refresh-badge { background:rgba(255,255,255,0.15); border-radius:20px; padding:4px 12px; font-size:0.72rem; opacity:0.8; }
        .readonly-badge { background:rgba(255,255,255,0.2); border:1px solid rgba(255,255,255,0.3); border-radius:6px; padding:3px 10px; font-size:0.72rem; }
        .stat-card { background:white; border-radius:12px; padding:16px; text-align:center; box-shadow:0 2px 8px rgba(0,0,0,0.06); }
        .stat-card h3 { font-size:2rem; font-weight:700; margin:0; }
        .stat-card p  { font-size:0.72rem; color:#6c757d; margin:4px 0 0; }
        .progress-section { background:white; border-radius:12px; padding:16px 20px; margin-bottom:20px; box-shadow:0 2px 8px rgba(0,0,0,0.06); }
        .progress { height:12px; border-radius:6px; }
        .ligne-card { background:white; border-radius:12px; margin-bottom:10px; box-shadow:0 2px 8px rgba(0,0,0,0.06); overflow:hidden; border-left:5px solid #dee2e6; }
        .ligne-card.en_attente { border-left-color:#6c757d; }
        .ligne-card.assigné    { border-left-color:#0d6efd; }
        .ligne-card.en_route   { border-left-color:#17a2b8; }
        .ligne-card.recupere   { border-left-color:#28a745; }
        .ligne-card.au_magasin  { border-left-color:#343a40; }
        .ligne-card.livre_client { border-left-color:#6f42c1; }
        .ligne-card.probleme   { border-left-color:#dc3545; }
        .ligne-body { padding:14px 18px; }
        .article-code { font-family:'Courier New',monospace; font-weight:900; font-size:1.05rem; color:#0040c0; background:#eef4ff; padding:2px 8px; border-radius:4px; }
        .article-name { font-size:0.88rem; color:#555; margin-top:3px; }
        .meta-row { display:flex; flex-wrap:wrap; gap:6px; margin-top:8px; }
        .meta-badge { font-size:0.72rem; padding:3px 9px; border-radius:10px; font-weight:500; }
        .meta-fourn { background:#fff3cd; color:#856404; }
        .meta-site  { background:#e8f0fe; color:#1a2b4a; }
        .meta-slot  { background:#f0fff4; color:#166534; }
        .meta-qty   { background:#f0fdf4; color:#15803d; }
        .statut-badge { display:inline-flex; align-items:center; gap:6px; padding:5px 12px; border-radius:20px; font-size:0.8rem; font-weight:700; }
        .statut-badge.en_attente { background:#f1f3f5; color:#6c757d; }
        .statut-badge.assigné    { background:#e7f1ff; color:#0d6efd; }
        .statut-badge.en_route   { background:#e0f8ff; color:#0e7490; }
        .statut-badge.recupere   { background:#dcfce7; color:#166534; }
        .statut-badge.au_magasin { background:#f1f3f5; color:#343a40; }
        .statut-badge.probleme   { background:#fee2e2; color:#dc3545; }
        .chauffeur-info { font-size:0.78rem; color:#6c757d; margin-top:6px; }
        .probleme-note { background:#fee2e2; border-radius:6px; padding:6px 10px; font-size:0.78rem; color:#dc3545; margin-top:8px; }
        .slot-title { font-size:0.78rem; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#6c757d; margin:20px 0 8px; padding-bottom:6px; border-bottom:2px solid #e0e8f0; }
        .empty-state { text-align:center; padding:60px 20px; color:#9bacc4; }
        .empty-state i { font-size:3rem; margin-bottom:16px; display:block; }
    </style>
</head>
<body>

<div class="top-header">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <div style="font-size:0.75rem; opacity:0.7; margin-bottom:4px;">
                <i class="fas fa-route me-1"></i> Suivi de tournée
            </div>
            <span class="numdoc">{{ $numdoc }}</span>
            <h4 class="mt-1">{{ $stats['total'] }} pièce(s) planifiée(s)</h4>
        </div>
        <div class="d-flex flex-column align-items-end gap-2">
            <span class="readonly-badge"><i class="fas fa-eye me-1"></i> Lecture seule</span>
            <span class="refresh-badge"><i class="fas fa-sync me-1"></i> Actualisation auto 30s</span>
        </div>
    </div>
</div>

<div class="container" style="max-width:800px;">

    @if($lignes->isEmpty())
        <div class="empty-state">
            <i class="fas fa-clipboard-list"></i>
            Aucune pièce en tournée pour <strong>{{ $numdoc }}</strong>
        </div>
    @else

        {{-- Stats --}}
        <div class="row g-3 mb-3">
            <div class="col-6 col-md-3"><div class="stat-card"><h3 class="text-secondary">{{ $stats['total'] }}</h3><p>Total</p></div></div>
            <div class="col-6 col-md-3"><div class="stat-card"><h3 class="text-success">{{ $stats['recupere'] }}</h3><p>Récupérées</p></div></div>
            <div class="col-6 col-md-3"><div class="stat-card"><h3 style="color:#6f42c1;">{{ $stats['livre_client'] ?? 0 }}</h3><p>🚪 Livré client</p></div></div>
            <div class="col-6 col-md-3"><div class="stat-card"><h3 class="text-info">{{ $stats['en_cours'] }}</h3><p>En cours</p></div></div>
            <div class="col-6 col-md-3"><div class="stat-card"><h3 class="{{ $stats['probleme'] > 0 ? 'text-danger' : 'text-secondary' }}">{{ $stats['probleme'] }}</h3><p>Problèmes</p></div></div>
        </div>

        {{-- Progression --}}
        @php $livres = ($stats['recupere'] ?? 0) + ($stats['livre_client'] ?? 0); $pct = $stats['total'] > 0 ? round(($livres / $stats['total']) * 100) : 0; @endphp
        <div class="progress-section">
            <div class="d-flex justify-content-between mb-2" style="font-size:0.82rem;">
                <span class="fw-bold">Progression</span>
                <span class="text-success fw-bold">{{ $pct }}% récupéré</span>
            </div>
            <div class="progress">
                <div class="progress-bar bg-success" style="width:{{ $pct }}%;"></div>
            </div>
        </div>

        {{-- Matin --}}
        @php
            $allCreneaux = [
                '9h-11h'     => ['icon' => '🌅', 'label' => '9h – 11h'],
                '11h-12h'    => ['icon' => '🕚', 'label' => '11h – 12h'],
                '13h-14h'    => ['icon' => '🌞', 'label' => '13h – 14h'],
                '15h-16h'    => ['icon' => '🕒', 'label' => '15h – 16h'],
                '17h-18h'    => ['icon' => '🌇', 'label' => '17h – 18h'],
                'matin'      => ['icon' => '🌅', 'label' => 'Matin (8h-12h)'],
                'apres_midi' => ['icon' => '🌇', 'label' => 'Après-midi (13h-18h)'],
            ];
            // Exclure anciens slots si nouveaux présents
            $hasNewSlots = $lignes->whereIn('slot', ['9h-11h','11h-12h','13h-14h','15h-16h','17h-18h'])->count() > 0;
            if ($hasNewSlots) {
                unset($allCreneaux['matin'], $allCreneaux['apres_midi']);
            }
        @endphp

        @php $first = true; @endphp
        @foreach($allCreneaux as $slotKey => $slotInfo)
            @php $lignesCreneau = $lignes->where('slot', $slotKey); @endphp
            @if($lignesCreneau->count() > 0)
                <div class="slot-title" style="{{ $first ? '' : 'margin-top:24px;' }}">
                    {{ $slotInfo['icon'] }} {{ $slotInfo['label'] }}
                    <small style="font-weight:400;">({{ $lignesCreneau->count() }} pièce(s))</small>
                </div>
                @foreach($lignesCreneau as $ligne)
                    @include('planning.partials.suivi-ligne', ['ligne' => $ligne])
                @endforeach
                @php $first = false; @endphp
            @endif
        @endforeach
        @endif


    <div class="text-center mt-4 mb-5" style="font-size:0.75rem; color:#9bacc4;">
        <i class="fas fa-lock me-1"></i> Page lecture seule — {{ now()->format('H:i:s') }}
    </div>
</div>
</body>
</html>