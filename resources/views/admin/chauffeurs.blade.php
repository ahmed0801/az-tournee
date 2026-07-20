@extends('admin.layout')
@section('title', '— Chauffeurs')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h4><i class="fas fa-users me-2"></i>Gestion des Chauffeurs</h4>
        <small class="text-muted">{{ $chauffeurs->count() }} chauffeur(s) enregistré(s)</small>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAjout">
        <i class="fas fa-user-plus me-1"></i> Nouveau Chauffeur
    </button>
</div>

{{-- Liste chauffeurs --}}
<div class="row g-3">
    @forelse($chauffeurs as $chauffeur)
    <div class="col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width:46px;height:46px;border-radius:50%;
                                background:{{ $chauffeur->is_active ? 'linear-gradient(135deg,#1a2b4a,#2d4a8a)' : '#dee2e6' }};
                                display:flex;align-items:center;justify-content:center;
                                color:white;font-weight:700;font-size:1.1rem;flex-shrink:0;">
                        {{ strtoupper(substr($chauffeur->name, 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-weight:700; font-size:0.95rem;">{{ $chauffeur->name }}</div>
                        <div style="font-size:0.78rem; color:#6c757d;">
                            @if($chauffeur->phone)
                                <i class="fas fa-phone me-1"></i>{{ $chauffeur->phone }}
                            @endif
                            @if($chauffeur->email)
                                <br><i class="fas fa-envelope me-1"></i>{{ $chauffeur->email }}
                            @endif
                        </div>
                    </div>
                    <div class="ms-auto">
                        <span class="badge {{ $chauffeur->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $chauffeur->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </div>
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn btn-sm btn-outline-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#modalEdit{{ $chauffeur->id }}">
                        <i class="fas fa-edit me-1"></i>Modifier
                    </button>
                    <button class="btn btn-sm btn-outline-warning"
                            data-bs-toggle="modal"
                            data-bs-target="#modalPwd{{ $chauffeur->id }}">
                        <i class="fas fa-key me-1"></i>Mot de passe
                    </button>
                    <form method="POST" action="{{ route('admin.chauffeurs.destroy', $chauffeur->id) }}"
                          onsubmit="return confirm('Supprimer {{ $chauffeur->name }} ?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Modifier --}}
    <div class="modal fade" id="modalEdit{{ $chauffeur->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.chauffeurs.update', $chauffeur->id) }}">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Modifier {{ $chauffeur->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $chauffeur->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Téléphone</label>
                            <input type="text" name="phone" class="form-control" value="{{ $chauffeur->phone }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $chauffeur->email }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-store me-1 text-primary"></i>
                                Société par défaut
                                <small class="text-muted">(vide = accès à tous les sites)</small>
                            </label>
                            <select name="site_id" class="form-control">
                                <option value="">-- Tous les sites --</option>
                                @foreach(App\Models\Site::where('is_active', true)->orderBy('name')->get() as $site)
                                    <option value="{{ $site->id }}" {{ $chauffeur->site_id == $site->id ? 'selected' : '' }}>
                                        {{ $site->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="active{{ $chauffeur->id }}"
                                   {{ $chauffeur->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="active{{ $chauffeur->id }}">Actif</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Mot de passe --}}
    <div class="modal fade" id="modalPwd{{ $chauffeur->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.chauffeurs.reset', $chauffeur->id) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-key me-2"></i>Mot de passe — {{ $chauffeur->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nouveau mot de passe <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required minlength="6">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Confirmer <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        <div class="alert alert-info py-2" style="font-size:0.82rem;">
                            <i class="fas fa-info-circle me-1"></i>
                            Le chauffeur utilisera ce mot de passe pour se connecter sur l'interface mobile.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-warning">Changer le mot de passe</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5 text-muted">
                <i class="fas fa-users fa-3x mb-3 d-block"></i>
                Aucun chauffeur. Cliquez sur "Nouveau Chauffeur" pour commencer.
            </div>
        </div>
    </div>
    @endforelse
</div>

{{-- Modal Ajout --}}
<div class="modal fade" id="modalAjout" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.chauffeurs.store') }}">
                @csrf
                <div class="modal-header" style="background:linear-gradient(135deg,#1a2b4a,#2d4a8a); color:white;">
                    <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Nouveau Chauffeur</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nom complet <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Ex: Mohamed Benali" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Téléphone</label>
                        <input type="text" name="phone" class="form-control" placeholder="06 XX XX XX XX">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="email@exemple.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="fas fa-store me-1 text-primary"></i>
                            Société par défaut
                            <small class="text-muted">(vide = accès à tous les sites)</small>
                        </label>
                        <select name="site_id" class="form-control">
                            <option value="">-- Tous les sites --</option>
                            @foreach(App\Models\Site::where('is_active', true)->orderBy('name')->get() as $site)
                                <option value="{{ $site->id }}">{{ $site->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <hr>
                    <p class="fw-bold mb-2"><i class="fas fa-lock me-1"></i>Accès interface mobile</p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mot de passe <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required minlength="6"
                               placeholder="Minimum 6 caractères">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Confirmer <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <div class="alert alert-light border py-2" style="font-size:0.8rem;">
                        <i class="fas fa-info-circle me-1 text-primary"></i>
                        Le chauffeur se connecte sur <strong>tournee.destockpa.fr/chauffeur</strong>
                        avec son nom et ce mot de passe.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i>Créer le chauffeur
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection