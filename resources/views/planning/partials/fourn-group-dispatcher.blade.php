{{-- resources/views/planning/partials/fourn-group-dispatcher.blade.php --}}
@php $groupId = 'disp-' . Str::slug($fournisseurName) . '-' . $loop->index; @endphp

<div class="fourn-card">
    <div class="fourn-header d-flex justify-content-between align-items-center"
         onclick="toggleGroup('{{ $groupId }}')">
        <span>
            <i class="fas fa-industry me-2 text-warning"></i>
            {{ $fournisseurName ?: 'Fournisseur non défini' }}
            @php
                $premiereLigne = $lignes->first();
                $fournisseurObj = $premiereLigne ? $premiereLigne->fournisseur : null;
                $adresse = $fournisseurObj ? $fournisseurObj->address : null;
            @endphp
            @if($adresse)
                <small class="text-muted ms-2"><i class="fas fa-map-marker-alt"></i> {{ $adresse }}</small>
            @endif

            @if(isset($modeHistorique) && $modeHistorique)
    <span class="badge bg-warning text-dark ms-2" style="font-size:0.68rem;">
        📅 {{ optional($lignes->first()->date_tournee)->format('d/m/Y') }}
        &nbsp;·&nbsp; {{ $lignes->first()->slot }}
    </span>
@endif


        </span>
        <div class="d-flex align-items-center gap-2">
            @php
                $recuperes = $lignes->whereIn('statut', ['recupere', 'au_magasin', 'livre_client'])->count();
                $total     = $lignes->count();
            @endphp
            <span class="badge {{ $recuperes === $total ? 'bg-success' : 'bg-secondary' }}">
                {{ $recuperes }}/{{ $total }}
            </span>
            <i class="fas fa-chevron-down text-muted"></i>
        </div>
    </div>

    <div id="group-{{ $groupId }}">
        @foreach($lignes as $ligne)
        <div class="line-row" id="line-row-{{ $ligne->id }}"
     data-search-code="{{ strtolower($ligne->article_code) }}"
     data-search-name="{{ strtolower($ligne->article_name) }}"
     data-search-vendeur="{{ strtolower($ligne->created_by_name ?? '') }}">
            {{-- Code article --}}
            <span class="article-code">{{ $ligne->article_code }}</span>

            {{-- Désignation + meta --}}
            <div class="flex-fill">
                <div style="font-size:0.82rem; color:#333;">{{ $ligne->article_name }}</div>
                <div class="d-flex gap-1 mt-1 flex-wrap">
                    @php $siteObj = $ligne->site; @endphp
                    <span class="site-badge">{{ $siteObj ? $siteObj->name : '-' }}</span>
                    <span class="badge bg-light text-dark border" style="font-size:0.68rem;">
                        📄 {{ $ligne->source_numdoc }}
                    </span>
                    <span class="badge bg-light text-success border" style="font-size:0.68rem;">
                        ×{{ number_format($ligne->quantity, 0) }}
                    </span>
                    @if($ligne->notes)
                    <span class="badge bg-light text-muted border" style="font-size:0.68rem;">
                        📝 {{ Str::limit($ligne->notes, 25) }}
                    </span>
                    @endif
                    @if($ligne->created_by_name)
                    <span class="badge bg-light text-muted border" style="font-size:0.68rem;">
                        👤 {{ $ligne->created_by_name }}
                    </span>
                    @endif
                </div>
            </div>

            {{-- Chauffeur --}}
            <select class="chauffeur-select"
                    onchange="assignChauffeur({{ $ligne->id }}, this.value)">
                <option value="">— Chauffeur —</option>
                @foreach($chauffeurs as $c)
                    <option value="{{ $c->id }}" {{ $ligne->chauffeur_id == $c->id ? 'selected' : '' }}>
                        {{ $c->name }}
                    </option>
                @endforeach
            </select>




            {{-- Statut --}}
            @php
                $bgColor = '#f3f4f6';
                if ($ligne->statut === 'recupere')     $bgColor = '#d1fae5';
                elseif ($ligne->statut === 'livre_client') $bgColor = '#ede9fe';
                elseif ($ligne->statut === 'probleme') $bgColor = '#fee2e2';
                elseif ($ligne->statut === 'en_route') $bgColor = '#dbeafe';
                elseif ($ligne->statut === 'assigné')  $bgColor = '#e0f2fe';
            @endphp
            <select class="statut-select"
                    onchange="updateStatut({{ $ligne->id }}, this.value)"
                    style="background:{{ $bgColor }}">
                <option value="en_attente" {{ $ligne->statut === 'en_attente' ? 'selected' : '' }}>⏳ En attente</option>
                <option value="assigné"    {{ $ligne->statut === 'assigné'    ? 'selected' : '' }}>👤 Assigné</option>
                <option value="en_route"   {{ $ligne->statut === 'en_route'   ? 'selected' : '' }}>🚗 En route</option>
                <option value="recupere"   {{ $ligne->statut === 'recupere'   ? 'selected' : '' }}>✅ Récupéré</option>
                <option value="au_magasin"   {{ $ligne->statut === 'au_magasin'   ? 'selected' : '' }}>🏪 Au magasin</option>
                <option value="livre_client" {{ $ligne->statut === 'livre_client' ? 'selected' : '' }}>🚪 Livré client</option>
                <option value="probleme"   {{ $ligne->statut === 'probleme'   ? 'selected' : '' }}>⚠️ Problème</option>
            </select>





            {{-- Changer le créneau --}}
{{-- Changer le créneau --}}
<select class="form-select form-select-sm slot-select"
        style="width:110px; font-size:0.72rem;"
        data-line-id="{{ $ligne->id }}"
        data-site-id="{{ $ligne->site_id }}"
        onchange="updateSlot({{ $ligne->id }}, this.value)">
    <option value="{{ $ligne->slot }}" selected>{{ $ligne->slot }}</option>
</select>

{{-- Changer la date --}}
<input type="date"
       class="form-control form-control-sm"
       style="width:130px; font-size:0.72rem;"
       value="{{ $ligne->date_tournee->format('Y-m-d') }}"
       onchange="updateDate({{ $ligne->id }}, this.value)">

{{-- Supprimer --}}
<button onclick="deleteLine({{ $ligne->id }}, this)"
        style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;
               border-radius:6px;font-size:0.7rem;font-weight:600;
               padding:3px 8px;cursor:pointer;">
    🗑 retirer
</button>







            {{-- Heure scan --}}
            @if($ligne->scanned_at)
                <small class="text-success">
                    <i class="fas fa-check-circle"></i> {{ $ligne->scanned_at->format('H:i') }}
                </small>
            @endif
            @if($ligne->probleme_notes)
                <small class="text-danger">
                    <i class="fas fa-exclamation"></i> {{ Str::limit($ligne->probleme_notes, 30) }}
                </small>
            @endif
        </div>
        @endforeach
    </div>
</div>