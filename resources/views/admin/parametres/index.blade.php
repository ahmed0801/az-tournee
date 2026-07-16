@extends('admin.layout')
@section('title', '— Paramètres Tournée')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h4><i class="fas fa-sliders-h me-2"></i>Paramètres Tournée</h4>
        <small class="text-muted">Configurez les créneaux, jours actifs et exceptions par magasin</small>
    </div>
    <a href="{{ route('admin.parametres.exceptions.globales') }}" class="btn btn-outline-danger btn-sm">
        <i class="fas fa-calendar-times me-1"></i> Jours Fériés Globaux
    </a>
</div>

<div class="row g-3">
    @foreach($sites as $site)
    @php $p = $site->parametre; @endphp
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center py-3">
                <div class="d-flex align-items-center gap-2">
                    <span style="width:10px;height:10px;border-radius:50%;
                                 background:{{ ($p && $p->is_active) ? '#28a745' : '#dc3545' }};
                                 display:inline-block;"></span>
                    <strong>{{ $site->name }}</strong>
                </div>
                <a href="{{ route('admin.parametres.show', $site->id) }}"
                   class="btn btn-sm btn-primary">
                    <i class="fas fa-edit me-1"></i> Configurer
                </a>
            </div>
            <div class="card-body py-3" style="font-size:0.82rem;">
                @if($p)
                    <div class="mb-2">
                        <strong>Jours actifs :</strong>
                        @foreach($p->jours_actifs as $jour)
                            <span class="badge bg-success me-1">{{ ucfirst($jour) }}</span>
                        @endforeach
                    </div>
                    <div class="mb-2">
                        <strong>Horaires :</strong>
                        {{ $p->heure_debut }} → {{ $p->heure_fin }}
                    </div>
                    <div>
                        <strong>Créneaux :</strong>
                        @foreach($p->creneaux as $c)
                            <span class="badge bg-light text-dark border me-1">{{ $c['label'] }}</span>
                        @endforeach
                    </div>
                @else
                    <span class="text-muted">Pas encore configuré</span>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection