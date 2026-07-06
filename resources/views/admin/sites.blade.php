@extends('admin.layout')
@section('title', '— Sites / Magasins')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h4><i class="fas fa-store me-2"></i>Gestion des Sites / Magasins</h4>
        <small class="text-muted">{{ $sites->count() }} site(s) configuré(s)</small>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAjoutSite">
        <i class="fas fa-plus me-1"></i> Nouveau Site
    </button>
</div>

<div class="row g-3">
    @forelse($sites as $site)
    <div class="col-md-6">
        <div class="card h-100 {{ !$site->is_active ? 'opacity-60' : '' }}">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <span style="width:10px;height:10px;border-radius:50%;
                                 background:{{ $site->is_active ? '#28a745' : '#dc3545' }};
                                 display:inline-block;"></span>
                    <strong>{{ $site->name }}</strong>
                    <span class="badge bg-light text-dark border" style="font-size:0.68rem;">{{ $site->slug }}</span>
                </div>
                <div class="d-flex gap-1">
                    {{-- Tester --}}
                    <button class="btn btn-xs btn-outline-secondary btn-test-site"
                            data-site-id="{{ $site->id }}"
                            style="font-size:0.72rem; padding:2px 8px;">
                        <i class="fas fa-plug"></i> Tester
                    </button>
                    {{-- Activer/Désactiver --}}
                    <form method="POST" action="{{ route('admin.sites.toggle', $site->id) }}">
                        @csrf
                        <button class="btn btn-xs {{ $site->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                style="font-size:0.72rem; padding:2px 8px;">
                            {{ $site->is_active ? 'Désactiver' : 'Activer' }}
                        </button>
                    </form>
                    {{-- Modifier --}}
                    <button class="btn btn-xs btn-outline-primary"
                            style="font-size:0.72rem; padding:2px 8px;"
                            data-bs-toggle="modal"
                            data-bs-target="#modalEditSite{{ $site->id }}">
                        <i class="fas fa-edit"></i>
                    </button>
                    {{-- Supprimer --}}
                    <form method="POST" action="{{ route('admin.sites.destroy', $site->id) }}"
                          onsubmit="return confirm('Supprimer {{ $site->name }} ?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-xs btn-outline-danger"
                                style="font-size:0.72rem; padding:2px 8px;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body py-3">
                <div class="row g-2" style="font-size:0.82rem;">
                    <div class="col-12">
                        <i class="fas fa-link me-1 text-primary"></i>
                        <a href="{{ $site->url }}" target="_blank">{{ $site->url }}</a>
                    </div>
                    @if($site->city)
                    <div class="col-6">
                        <i class="fas fa-map-marker-alt me-1 text-danger"></i> {{ $site->city }}
                    </div>
                    @endif
                    @if($site->phone)
                    <div class="col-6">
                        <i class="fas fa-phone me-1 text-success"></i> {{ $site->phone }}
                    </div>
                    @endif
                    <div class="col-12">
                        <i class="fas fa-key me-1 text-warning"></i>
                        <code style="font-size:0.75rem;">{{ $site->api_key }}</code>
                    </div>
                    <div class="col-12 mt-1">
                        <span class="badge bg-light text-dark border">
                            <i class="fas fa-industry me-1"></i>
                            {{ $site->tournee_lines_count }} ligne(s) total
                        </span>
                    </div>
                </div>

                {{-- Résultat test connexion --}}
                <div id="test-result-{{ $site->id }}" class="mt-2" style="font-size:0.78rem; min-height:20px;"></div>
            </div>
        </div>
    </div>

    {{-- ── Modal Modifier Site ──────────────────────────────── --}}
    <div class="modal fade" id="modalEditSite{{ $site->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.sites.update', $site->id) }}">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Modifier — {{ $site->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nom <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control form-control-sm"
                                       value="{{ $site->name }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Slug <span class="text-danger">*</span></label>
                                <input type="text" name="slug" class="form-control form-control-sm"
                                       value="{{ $site->slug }}" required>
                                <small class="text-muted">Ex: conflans, epinay...</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">
                                    URL du site <span class="text-danger">*</span>
                                </label>
                                <input type="url" name="url" class="form-control form-control-sm"
                                       value="{{ $site->url }}" required
                                       placeholder="https://conflans.destockpa.fr">
                                <small class="text-muted">URL exacte du projet Laravel aznegoce (sans / final)</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">
                                    Clé API <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="api_key" class="form-control form-control-sm"
                                       value="{{ $site->api_key }}" required>
                                <small class="text-muted">
                                    Doit correspondre à <code>TOURNEE_API_KEY</code> dans le .env du site
                                </small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ville</label>
                                <input type="text" name="city" class="form-control form-control-sm"
                                       value="{{ $site->city }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Téléphone</label>
                                <input type="text" name="phone" class="form-control form-control-sm"
                                       value="{{ $site->phone }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Adresse</label>
                                <input type="text" name="address" class="form-control form-control-sm"
                                       value="{{ $site->address }}">
                            </div>
                        </div>

                        <div class="alert alert-info mt-3 py-2" style="font-size:0.78rem;">
                            <i class="fas fa-info-circle me-1"></i>
                            Dans le <code>.env</code> du site <strong>{{ $site->name }}</strong>, vérifiez :
                            <br><code>TOURNEE_API_KEY={{ $site->api_key }}</code>
                            <br><code>TOURNEE_API_URL=http://localhost:8001</code> (ou l'URL de prod du hub)
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary btn-sm px-4">
                            <i class="fas fa-save me-1"></i>Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5 text-muted">
                <i class="fas fa-store fa-3x mb-3 d-block"></i>
                Aucun site configuré.
            </div>
        </div>
    </div>
    @endforelse
</div>

{{-- ── Modal Ajout Site ──────────────────────────────────────── --}}
<div class="modal fade" id="modalAjoutSite" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.sites.store') }}">
                @csrf
                <div class="modal-header" style="background:linear-gradient(135deg,#1a2b4a,#2d4a8a); color:white;">
                    <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Nouveau Site</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-sm"
                                   placeholder="Ex: Conflans" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Slug <span class="text-danger">*</span></label>
                            <input type="text" name="slug" class="form-control form-control-sm"
                                   placeholder="Ex: conflans" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">URL <span class="text-danger">*</span></label>
                            <input type="url" name="url" class="form-control form-control-sm"
                                   placeholder="https://conflans.destockpa.fr" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Clé API <span class="text-danger">*</span></label>
                            <input type="text" name="api_key" class="form-control form-control-sm"
                                   placeholder="Ex: conflans_key_2025" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ville</label>
                            <input type="text" name="city" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Téléphone</label>
                            <input type="text" name="phone" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="fas fa-save me-1"></i>Créer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.querySelectorAll('.btn-test-site').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var siteId = this.getAttribute('data-site-id');
        var result = document.getElementById('test-result-' + siteId);
        var me     = this;

        me.disabled = true;
        me.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        result.innerHTML = '<span class="text-muted">Test en cours...</span>';

        fetch('/admin/sites/' + siteId + '/test', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(function(r) { return r.json(); })
        .then(function(d) {
            result.innerHTML = '<span class="' + (d.success ? 'text-success' : 'text-danger') + '">'
                + d.message + '</span>';
        })
        .catch(function() {
            result.innerHTML = '<span class="text-danger">❌ Erreur réseau</span>';
        })
        .finally(function() {
            me.disabled = false;
            me.innerHTML = '<i class="fas fa-plug"></i> Tester';
        });
    });
});
</script>
@endsection