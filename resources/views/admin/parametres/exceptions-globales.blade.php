@extends('admin.layout')
@section('title', '— Jours Fériés Globaux')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <a href="{{ route('admin.parametres.index') }}" class="btn btn-sm btn-outline-secondary me-2">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h4 class="d-inline"><i class="fas fa-calendar-times me-2"></i>Jours Fériés Globaux</h4>
        <small class="text-muted d-block mt-1 ms-5">S'appliquent à tous les magasins</small>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header"><i class="fas fa-plus me-2"></i>Ajouter un jour férié</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.parametres.exceptions.globales.add') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Date</label>
                        <input type="date" name="date" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Label</label>
                        <input type="text" name="label" class="form-control form-control-sm"
                               placeholder="Ex: 1er Janvier, Noël..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Note</label>
                        <input type="text" name="notes" class="form-control form-control-sm"
                               placeholder="Optionnel">
                    </div>
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="fas fa-plus me-1"></i> Ajouter
                    </button>
                </form>

                {{-- Raccourcis jours fériés France --}}
                <hr>
                <small class="fw-bold text-muted">Jours fériés France {{ date('Y') + 1 }} :</small>
                <div class="mt-2 d-flex flex-wrap gap-1">
                    @php
                        $year = date('Y') + 1;
                        $feries = [
                            ['date' => "$year-01-01", 'label' => '1er Janvier'],
                            ['date' => "$year-05-01", 'label' => 'Fête du Travail'],
                            ['date' => "$year-05-08", 'label' => 'Victoire 1945'],
                            ['date' => "$year-07-14", 'label' => 'Fête Nationale'],
                            ['date' => "$year-08-15", 'label' => 'Assomption'],
                            ['date' => "$year-11-01", 'label' => 'Toussaint'],
                            ['date' => "$year-11-11", 'label' => 'Armistice'],
                            ['date' => "$year-12-25", 'label' => 'Noël'],
                        ];
                    @endphp
                    @foreach($feries as $f)
                    <form method="POST" action="{{ route('admin.parametres.exceptions.globales.add') }}" class="d-inline">
                        @csrf
                        <input type="hidden" name="date" value="{{ $f['date'] }}">
                        <input type="hidden" name="label" value="{{ $f['label'] }}">
                        <button type="submit" class="btn btn-xs btn-outline-secondary" style="font-size:0.7rem; padding:2px 8px;">
                            + {{ $f['label'] }}
                        </button>
                    </form>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header"><i class="fas fa-list me-2"></i>Exceptions globales ({{ $exceptions->count() }})</div>
            <div class="card-body p-0">
                @forelse($exceptions->sortBy('date') as $ex)
                <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                    <div>
                        <strong style="font-size:0.88rem;">{{ $ex->date->format('d/m/Y') }}</strong>
                        <span class="text-muted ms-2" style="font-size:0.8rem;">{{ $ex->label }}</span>
                        @if($ex->notes)
                            <div style="font-size:0.72rem; color:#9ca3af;">{{ $ex->notes }}</div>
                        @endif
                    </div>
                    <form method="POST"
                          action="{{ route('admin.parametres.exceptions.delete', $ex->id) }}"
                          onsubmit="return confirm('Supprimer ?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-xs btn-outline-danger" style="padding:2px 8px; font-size:0.7rem;">
                            <i class="fas fa-times"></i>
                        </button>
                    </form>
                </div>
                @empty
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-calendar-check fa-2x mb-2 d-block"></i>
                    Aucun jour férié configuré
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection