{{-- ═══════════════════════════════════════════════════════════
     resources/views/admin/cleanup.blade.php
═══════════════════════════════════════════════════════════ --}}
@extends('admin.layout')
@section('title', '— Nettoyage')

@section('content')
<div class="page-header">
    <h4><i class="fas fa-trash-alt me-2"></i>Nettoyage de la base</h4>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header py-3">
                <i class="fas fa-calendar-times me-2 text-danger"></i>Supprimer les anciennes lignes
            </div>
            <div class="card-body">
                <p class="text-muted" style="font-size:0.88rem;">
                    Supprime les lignes de tournée plus anciennes que le nombre de jours choisi.
                    <br>
                    <strong class="text-danger">{{ $old }}</strong> ligne(s) de plus de 30 jours actuellement.
                </p>
                <form method="POST" action="{{ route('admin.cleanup.old') }}"
                      onsubmit="return confirm('Supprimer les lignes anciennes ?')">
                    @csrf
                    <div class="d-flex gap-2 align-items-center">
                        <input type="number" name="days" class="form-control" value="30"
                               min="7" max="365" style="width:100px;">
                        <span class="text-muted">jours</span>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i>Supprimer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-warning">
            <div class="card-header py-3 bg-warning bg-opacity-10">
                <i class="fas fa-exclamation-triangle me-2 text-warning"></i>Zone dangereuse
            </div>
            <div class="card-body">
                <p class="text-muted" style="font-size:0.88rem;">
                    Vider complètement toutes les tables (à utiliser uniquement en développement).
                </p>
                <form method="POST" action="{{ route('admin.cleanup.old') }}"
                      onsubmit="return confirm('⚠️ Supprimer TOUTES les lignes ? Cette action est irréversible.')">
                    @csrf
                    <input type="hidden" name="days" value="0">
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="fas fa-bomb me-1"></i>Vider toutes les lignes
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


{{-- ═══════════════════════════════════════════════════════════
     resources/views/admin/sites.blade.php
═══════════════════════════════════════════════════════════ --}}