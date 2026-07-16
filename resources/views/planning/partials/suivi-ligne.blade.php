{{-- resources/views/planning/partials/suivi-ligne.blade.php --}}
@php
    $icons = [
        'en_attente' => 'clock',
        'assigné'    => 'user-check',
        'en_route'   => 'car',
        'recupere'   => 'check-circle',
        'au_magasin' => 'store',
        'livre_client'=> 'door-open',
        'probleme'   => 'exclamation-triangle',
    ];
    $labels = [
        'en_attente' => 'En attente',
        'assigné'    => 'Assigné',
        'en_route'   => 'En route',
        'recupere'   => 'Récupéré',
        'au_magasin' => 'Au magasin',
        'livre_client'=> 'Livré au client',
        'probleme'   => 'Problème',
    ];
    $icon = $icons[$ligne->statut] ?? 'circle';
    $label = $labels[$ligne->statut] ?? $ligne->statut;
@endphp

<div class="ligne-card {{ $ligne->statut }}">
    <div class="ligne-body">
        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">

            {{-- Info article --}}
            <div class="flex-fill">
                <span class="article-code">{{ $ligne->article_code }}</span>
                <div class="article-name">{{ $ligne->article_name }}</div>

                <div class="meta-row">
                    @if($ligne->fournisseur || $ligne->fournisseur_name)
                        <span class="meta-badge meta-fourn">
                            <i class="fas fa-industry me-1"></i>
                            {{ optional($ligne->fournisseur)->name ?? $ligne->fournisseur_name }}
                        </span>
                    @endif
                    @if($ligne->site)
                        <span class="meta-badge meta-site">
                            <i class="fas fa-store me-1"></i>{{ optional($ligne->site)->name }}
                        </span>
                    @endif
                    <span class="meta-badge meta-qty">×{{ number_format($ligne->quantity, 0) }}</span>
                    <span class="meta-badge meta-slot">
                        {{ $ligne->date_tournee ? $ligne->date_tournee->format('d/m/Y') : '-' }}
                    </span>
                </div>

                {{-- Chauffeur --}}
                @if($ligne->chauffeur)
                    <div class="chauffeur-info">
                        <i class="fas fa-user me-1"></i>
                        Chauffeur : <strong>{{ optional($ligne->chauffeur)->name }}</strong>
                        @if($ligne->scanned_at)
                            — scanné à {{ $ligne->scanned_at->format('H:i') }}
                        @endif
                    </div>
                @else
                    <div class="chauffeur-info">
                        <i class="fas fa-user-clock me-1 text-warning"></i>
                        <span class="text-warning">⏳ Chauffeur non encore assigné</span>
                    </div>
                @endif

                {{-- Note --}}
                @if($ligne->notes)
                    <div style="font-size:0.75rem; color:#888; margin-top:4px;">
                        <i class="fas fa-sticky-note me-1"></i>{{ $ligne->notes }}
                    </div>
                @endif

                {{-- Problème --}}
                @if($ligne->statut === 'probleme' && $ligne->probleme_notes)
                    <div class="probleme-note">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        {{ $ligne->probleme_notes }}
                    </div>
                @endif
            </div>

            {{-- Statut badge --}}
            <div>
                <span class="statut-badge {{ $ligne->statut }}">
                    <i class="fas fa-{{ $icon }}"></i>
                    {{ $label }}
                </span>
            </div>

        </div>
    </div>
</div>