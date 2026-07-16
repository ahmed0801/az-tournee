@extends('admin.layout')
@section('title', '— Paramètres ' . $site->name)

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <a href="{{ route('admin.parametres.index') }}" class="btn btn-sm btn-outline-secondary me-2">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h4 class="d-inline"><i class="fas fa-sliders-h me-2"></i>{{ $site->name }} — Paramètres Tournée</h4>
    </div>
</div>

<div class="row g-4">

    {{-- ── CONFIGURATION PRINCIPALE ──────────────────────────── --}}
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><i class="fas fa-cog me-2"></i>Configuration</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.parametres.update', $site->id) }}">
                    @csrf @method('PUT')

                    {{-- Actif --}}
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                               {{ $parametre->is_active ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="is_active">
                            Tournée active pour ce site
                        </label>
                    </div>

                    {{-- Jours actifs --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="fas fa-calendar-week me-1 text-primary"></i> Jours actifs
                        </label>
                        <div class="d-flex flex-wrap gap-2 mt-1">
                            @foreach(['lundi','mardi','mercredi','jeudi','vendredi','samedi','dimanche'] as $jour)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox"
                                       name="jours_actifs[]" value="{{ $jour }}"
                                       id="jour-{{ $jour }}"
                                       {{ in_array($jour, $parametre->jours_actifs ?? []) ? 'checked' : '' }}>
                                <label class="form-check-label" for="jour-{{ $jour }}">
                                    {{ ucfirst($jour) }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Horaires --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Heure d'ouverture</label>
                            <input type="time" name="heure_debut" class="form-control form-control-sm"
                                   value="{{ $parametre->heure_debut }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Heure de fermeture</label>
                            <input type="time" name="heure_fin" class="form-control form-control-sm"
                                   value="{{ $parametre->heure_fin }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Délai min. (heures)</label>
                            <input type="number" name="delai_min_heures" class="form-control form-control-sm"
                                   min="0" max="24" value="{{ $parametre->delai_min_heures }}">
                            <small class="text-muted">Avant le créneau</small>
                        </div>
                    </div>

                    {{-- Créneaux --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="fas fa-clock me-1 text-success"></i> Créneaux
                        </label>
                        <div id="creneaux-list">
                            @foreach($parametre->creneaux as $i => $c)
                            <div class="creneau-row d-flex align-items-center gap-2 mb-2" data-index="{{ $i }}">
                                <input type="text" name="creneaux[{{ $i }}][label]"
                                       class="form-control form-control-sm" style="width:110px;"
                                       value="{{ $c['label'] }}" placeholder="Ex: 9h-11h" required>
                                <input type="time" name="creneaux[{{ $i }}][debut]"
                                       class="form-control form-control-sm" style="width:110px;"
                                       value="{{ $c['debut'] }}" required>
                                <span class="text-muted">→</span>
                                <input type="time" name="creneaux[{{ $i }}][fin]"
                                       class="form-control form-control-sm" style="width:110px;"
                                       value="{{ $c['fin'] }}" required>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-creneau">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" id="btn-add-creneau" class="btn btn-sm btn-outline-success mt-1">
                            <i class="fas fa-plus me-1"></i> Ajouter un créneau
                        </button>
                    </div>

                    {{-- Notes --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Notes internes</label>
                        <textarea name="notes" class="form-control form-control-sm" rows="2">{{ $parametre->notes }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Enregistrer
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ── EXCEPTIONS ───────────────────────────────────────── --}}
    <div class="col-lg-5">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-calendar-times me-2"></i>Exceptions & Jours Fériés</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.parametres.exceptions.add', $site->id) }}">
                    @csrf
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Date</label>
                            <input type="date" name="date" class="form-control form-control-sm" required
                                   min="{{ today()->format('Y-m-d') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Label</label>
                            <input type="text" name="label" class="form-control form-control-sm"
                                   placeholder="Ex: Noël, Fermeture..." required>
                        </div>
                        <div class="col-12 d-flex gap-3 align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_closed"
                                       id="is_closed" value="1" checked>
                                <label class="form-check-label small" for="is_closed">Fermé ce jour</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_global"
                                       id="is_global" value="1">
                                <label class="form-check-label small" for="is_global">Tous les sites</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <input type="text" name="notes" class="form-control form-control-sm"
                                   placeholder="Note (optionnel)">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-sm btn-danger w-100">
                        <i class="fas fa-plus me-1"></i> Ajouter exception
                    </button>
                </form>
            </div>
        </div>

        {{-- Liste exceptions --}}
        <div class="card">
            <div class="card-header py-2">
                <small class="fw-bold">Exceptions configurées</small>
            </div>
            <div class="card-body p-0">
                @forelse($exceptions as $ex)
                <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                    <div>
                        <strong style="font-size:0.82rem;">
                            {{ $ex->date->format('d/m/Y') }}
                        </strong>
                        <span class="badge {{ $ex->is_closed ? 'bg-danger' : 'bg-success' }} ms-1"
                              style="font-size:0.65rem;">
                            {{ $ex->is_closed ? 'Fermé' : 'Ouvert' }}
                        </span>
                        @if(!$ex->site_id)
                            <span class="badge bg-warning text-dark ms-1" style="font-size:0.65rem;">Global</span>
                        @endif
                        <div style="font-size:0.75rem; color:#6c757d;">{{ $ex->label }}</div>
                    </div>
                    <form method="POST"
                          action="{{ route('admin.parametres.exceptions.delete', $ex->id) }}"
                          onsubmit="return confirm('Supprimer cette exception ?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-xs btn-outline-danger" style="padding:2px 7px; font-size:0.7rem;">
                            <i class="fas fa-times"></i>
                        </button>
                    </form>
                </div>
                @empty
                <div class="text-center py-4 text-muted" style="font-size:0.82rem;">
                    <i class="fas fa-check-circle text-success me-1"></i>
                    Aucune exception configurée
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
var creneauCount = {{ count($parametre->creneaux) }};

document.getElementById('btn-add-creneau').addEventListener('click', function() {
    var i   = creneauCount++;
    var row = document.createElement('div');
    row.className = 'creneau-row d-flex align-items-center gap-2 mb-2';
    row.innerHTML =
        '<input type="text" name="creneaux[' + i + '][label]" class="form-control form-control-sm" style="width:110px;" placeholder="Ex: 9h-11h" required>' +
        '<input type="time" name="creneaux[' + i + '][debut]" class="form-control form-control-sm" style="width:110px;" required>' +
        '<span class="text-muted">→</span>' +
        '<input type="time" name="creneaux[' + i + '][fin]" class="form-control form-control-sm" style="width:110px;" required>' +
        '<button type="button" class="btn btn-sm btn-outline-danger btn-remove-creneau"><i class="fas fa-times"></i></button>';
    document.getElementById('creneaux-list').appendChild(row);
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.btn-remove-creneau')) {
        var rows = document.querySelectorAll('.creneau-row');
        if (rows.length > 1) {
            e.target.closest('.creneau-row').remove();
        }
    }
});
</script>
@endsection