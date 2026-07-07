@extends('admin.layout')
@section('title', '— Synchronisation')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h4><i class="fas fa-sync me-2"></i>Synchronisation</h4>
        <small class="text-muted">
            Dernière sync : {{ $lastSync ?? 'Jamais' }}
        </small>
    </div>
    <form method="POST" action="{{ route('admin.sync.now') }}">
        @csrf
        <button type="submit" class="btn btn-success px-4">
            <i class="fas fa-sync me-1"></i> Synchroniser maintenant
        </button>
    </form>
</div>

{{-- Résultats de la dernière sync --}}
@if(session('sync_results'))
<div class="card mb-4">
    <div class="card-header py-3">
        <i class="fas fa-list me-2"></i>Résultats de la synchronisation
    </div>
    <div class="card-body">
        @foreach(session('sync_results') as $siteName => $result)
        <div class="d-flex align-items-center gap-3 mb-2 p-2 rounded
                    {{ $result['success'] ? 'bg-success bg-opacity-10' : 'bg-danger bg-opacity-10' }}">
            <i class="fas fa-{{ $result['success'] ? 'check-circle text-success' : 'times-circle text-danger' }} fa-lg"></i>
            <div>
                <strong>{{ $siteName }}</strong>
                @if($result['success'])
                    <span class="text-success ms-2">{{ $result['count'] }} fournisseurs synchronisés</span>
                @else
                    <span class="text-danger ms-2">{{ $result['error'] }}</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Sites --}}
<div class="card mb-4">
    <div class="card-header py-3">
        <i class="fas fa-store me-2"></i>Sites configurés ({{ $sites->count() }})
    </div>
    <div class="card-body">
        <div class="row g-3">
            @foreach($sites as $site)
            <div class="col-md-6">
                <div class="border rounded p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <strong>{{ $site->name }}</strong>
                            <small class="text-muted d-block">{{ $site->url }}</small>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary btn-test-site"
                                data-site-id="{{ $site->id }}"
                                data-site-name="{{ $site->name }}">
                            <i class="fas fa-plug me-1"></i>Tester
                        </button>
                    </div>
                    <div id="test-result-{{ $site->id }}" style="font-size:0.8rem;"></div>
                    <small class="text-muted">
                        Clé API : <code>{{ substr($site->api_key, 0, 8) }}...</code>
                    </small>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Fournisseurs --}}
<div class="card">
    <div class="card-header py-3 d-flex justify-content-between">
        <span><i class="fas fa-industry me-2"></i>Fournisseurs synchronisés ({{ $fournisseurs->total() }})</span>
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nom</th>
                    <th>Site</th>
                    <th>Ville</th>
                    <th>Téléphone</th>
                </tr>
            </thead>
            <tbody>
                @forelse($fournisseurs as $f)
                <tr>
                    <td style="font-size:0.85rem; font-weight:600;">{{ $f->name }}</td>
                    <td><span class="badge bg-primary" style="font-size:0.68rem;">{{ optional($f->site)->name }}</span></td>
                    <td style="font-size:0.82rem;">{{ $f->city ?? '—' }}</td>
                    <td style="font-size:0.82rem;">{{ $f->phone ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-3">Aucun fournisseur synchronisé</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $fournisseurs->links() }}
    </div>
</div>

@endsection

@section('scripts')
<script>
document.querySelectorAll('.btn-test-site').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var siteId   = this.getAttribute('data-site-id');
        var siteName = this.getAttribute('data-site-name');
        var result   = document.getElementById('test-result-' + siteId);
        var csrf     = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Test...';
        result.innerHTML = '';

        fetch('/admin/sites/' + siteId + '/test', {
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
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
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-plug me-1"></i>Tester';
        });
    });
});
</script>
@endsection