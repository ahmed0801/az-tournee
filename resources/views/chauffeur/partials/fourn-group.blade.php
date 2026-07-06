{{-- resources/views/chauffeur/partials/fourn-group.blade.php --}}

<div class="fourn-group">
    {{-- ── En-tête fournisseur ────────────────────── --}}
    <div class="fourn-header" onclick="toggleGroup('{{ $groupId }}')">
        <div>
            <div class="fourn-name">
                <i class="fas fa-industry me-2" style="color:#ffa030; font-size:0.82rem;"></i>
                {{ $fournisseurName ?: 'Fournisseur non défini' }}
            </div>
            @php $adresse = optional($lignes->first()->fournisseur)->address; @endphp
            @if($adresse)
                <div class="fourn-address">
                    <i class="fas fa-map-marker-alt me-1"></i>{{ $adresse }}
                </div>
            @endif
        </div>
        <div class="d-flex align-items-center gap-2">
            @php
                $recuperees = $lignes->where('statut', 'recupere')->count();
                $total      = $lignes->count();
            @endphp
            <span class="fourn-count {{ $recuperees === $total ? 'done' : '' }}">
                {{ $recuperees }}/{{ $total }} ✓
            </span>
            <i id="fourn-icon-{{ $groupId }}" class="fas fa-chevron-down"
               style="color:#4a6080; transition:transform 0.2s;"></i>
        </div>
    </div>

    {{-- ── Lignes articles ─────────────────────────── --}}
    <div id="fourn-body-{{ $groupId }}">
        @foreach($lignes as $ligne)
        <div class="article-card {{ $ligne->statut }}" id="article-card-{{ $ligne->id }}">

            {{-- En-tête de la ligne --}}
            <div class="d-flex align-items-start justify-content-between gap-2">
                <div class="flex-fill">
                    <div class="d-flex align-items-center gap-2">
                        <span class="status-dot {{ $ligne->statut }}"></span>
                        <span class="article-code">{{ $ligne->article_code }}</span>
                    </div>
                    <div class="article-name">{{ $ligne->article_name }}</div>
                    <div class="article-meta">
                        <span class="meta-badge meta-site">{{ optional($ligne->site)->name }}</span>
                        <span class="meta-badge meta-doc">📄 {{ $ligne->source_numdoc }}</span>
                        <span class="meta-badge meta-qty">×{{ number_format($ligne->quantity, 0) }}</span>
                        @if($ligne->notes)
                            <span class="meta-badge meta-note">📝 {{ Str::limit($ligne->notes, 28) }}</span>
                        @endif
                        @if($ligne->created_by_name)
                            <span class="meta-badge meta-vendeur">👤 {{ $ligne->created_by_name }}</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ── Actions selon statut ─────────────── --}}
            @if(!in_array($ligne->statut, ['recupere', 'au_magasin']))

                <div class="action-btns">
                    {{-- Bouton scan --}}
                    <button class="btn-scan" id="btn-scan-{{ $ligne->id }}"
                            onclick="toggleScan({{ $ligne->id }})"
                            data-line-id="{{ $ligne->id }}"
                            data-barcode="{{ $ligne->barcode ?? '' }}">
                        <i class="fas fa-barcode me-1"></i> Scanner
                    </button>

                    {{-- Marquer sans scan --}}
                    <button class="btn-done" onclick="markDone({{ $ligne->id }})" title="Marquer récupérée sans scan">
                        <i class="fas fa-check"></i>
                    </button>

                    {{-- Signaler problème --}}
                    <button class="btn-probleme"
                            onclick="openProbleme({{ $ligne->id }}, '{{ $ligne->article_code }}', '{{ addslashes($ligne->article_name) }}')"
                            title="Signaler un problème">
                        <i class="fas fa-exclamation"></i>
                    </button>
                </div>

                {{-- Zone de scan ──────────────────────── --}}
                <div class="scan-zone" id="scan-zone-{{ $ligne->id }}">
                    <div class="scan-info">
                        @if($ligne->barcode)
                            Code attendu : <code>{{ $ligne->barcode }}</code>
                        @else
                            <span style="color:#cc8030;">Pas de code-barres enregistré — vous pouvez en associer un</span>
                        @endif
                    </div>
                    <input type="text"
                           class="scan-input"
                           id="scan-input-{{ $ligne->id }}"
                           placeholder="Scanner le code-barres..."
                           autocomplete="off"
                           onkeydown="if(event.key==='Enter'){ event.preventDefault(); submitScan({{ $ligne->id }}); }">
                    <button class="btn-validate-scan" onclick="submitScan({{ $ligne->id }})">
                        <i class="fas fa-check me-1"></i>Valider
                    </button>
                    <div class="scan-feedback" id="scan-feedback-{{ $ligne->id }}"></div>
                </div>

            @elseif($ligne->statut === 'recupere')
                <div class="result-recupere">
                    <i class="fas fa-check-circle"></i>
                    Récupérée
                    @if($ligne->scanned_at)
                        — {{ $ligne->scanned_at->format('H:i') }}
                    @endif
                    @if($ligne->scanned_barcode)
                        <code style="font-size:0.68rem; color:#4a8060;">{{ $ligne->scanned_barcode }}</code>
                    @endif
                </div>

            @elseif($ligne->statut === 'probleme')
                <div class="result-probleme">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    {{ $ligne->probleme_notes }}
                </div>
            @endif

        </div>
        @endforeach
    </div>
</div>