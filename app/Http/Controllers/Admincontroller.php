<?php

namespace App\Http\Controllers;

use App\Models\Chauffeur;
use App\Models\Site;
use App\Models\TourneeLine;
use App\Models\Fournisseur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    // ══════════════════════════════════════════════════════════
    // DASHBOARD
    // ══════════════════════════════════════════════════════════
    public function index()
    {
        $stats = [
            'chauffeurs'       => Chauffeur::where('is_active', true)->count(),
            'sites'            => Site::where('is_active', true)->count(),
            'fournisseurs'     => Fournisseur::count(),
            'lignes_today'     => TourneeLine::whereDate('date_tournee', today())->count(),
            'lignes_week'      => TourneeLine::whereBetween('date_tournee', [
                                    today()->startOfWeek()->format('Y-m-d'),
                                    today()->format('Y-m-d')
                                  ])->count(),
            'recuperees_today' => TourneeLine::whereDate('date_tournee', today())->where('statut', 'recupere')->count(),
            'problemes_today'  => TourneeLine::whereDate('date_tournee', today())->where('statut', 'probleme')->count(),
        ];

        $dernieresLignes = TourneeLine::with(['chauffeur', 'site', 'fournisseur'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.index', compact('stats', 'dernieresLignes'));
    }

    // ══════════════════════════════════════════════════════════
    // CHAUFFEURS
    // ══════════════════════════════════════════════════════════
    public function chauffeurs()
    {
        $chauffeurs = Chauffeur::orderBy('name')->get();
        return view('admin.chauffeurs', compact('chauffeurs'));
    }

    public function chauffeurStore(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100|unique:chauffeurs,name',
            'phone'    => 'nullable|string|max:20',
            'email'    => 'nullable|email|max:100',
            'password' => 'required|string|min:6|confirmed',
        ]);

        Chauffeur::create([
            'name'      => $request->name,
            'phone'     => $request->phone,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'is_active' => true,
        ]);

        return redirect()->route('admin.chauffeurs')
            ->with('success', 'Chauffeur ' . $request->name . ' créé.');
    }

    public function chauffeurUpdate(Request $request, $id)
    {
        $chauffeur = Chauffeur::findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:100|unique:chauffeurs,name,' . $id,
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
        ]);

        $data = [
            'name'      => $request->name,
            'phone'     => $request->phone,
            'email'     => $request->email,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ];

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6|confirmed']);
            $data['password'] = Hash::make($request->password);
        }

        $chauffeur->update($data);
        return redirect()->route('admin.chauffeurs')->with('success', 'Chauffeur mis à jour.');
    }

    public function chauffeurDestroy($id)
    {
        $chauffeur = Chauffeur::findOrFail($id);
        $lignes = TourneeLine::where('chauffeur_id', $id)
            ->whereNotIn('statut', ['recupere', 'au_magasin', 'probleme'])
            ->whereDate('date_tournee', '>=', today())
            ->count();

        if ($lignes > 0) {
            return redirect()->route('admin.chauffeurs')
                ->with('error', 'Impossible : ' . $chauffeur->name . ' a ' . $lignes . ' livraison(s) en cours.');
        }

        $chauffeur->delete();
        return redirect()->route('admin.chauffeurs')->with('success', 'Chauffeur supprimé.');
    }

    public function chauffeurResetPassword(Request $request, $id)
    {
        $request->validate(['password' => 'required|min:6|confirmed']);
        $chauffeur = Chauffeur::findOrFail($id);
        $chauffeur->update(['password' => Hash::make($request->password)]);
        return redirect()->route('admin.chauffeurs')
            ->with('success', 'Mot de passe de ' . $chauffeur->name . ' réinitialisé.');
    }

    // ══════════════════════════════════════════════════════════
    // SITES
    // ══════════════════════════════════════════════════════════
    public function sites()
    {
        $sites = Site::withCount('tourneeLines')->orderBy('name')->get();
        return view('admin.sites', compact('sites'));
    }

    public function siteStore(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:100',
            'slug'    => 'required|string|max:50|unique:sites,slug',
            'url'     => 'required|url',
            'api_key' => 'required|string|min:6|unique:sites,api_key',
            'city'    => 'nullable|string|max:100',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:200',
        ]);

        Site::create([
            'name'      => $request->name,
            'slug'      => $request->slug,
            'url'       => rtrim($request->url, '/'),
            'api_key'   => $request->api_key,
            'city'      => $request->city,
            'phone'     => $request->phone,
            'address'   => $request->address,
            'is_active' => true,
        ]);

        return redirect()->route('admin.sites')->with('success', 'Site créé.');
    }

    public function siteUpdate(Request $request, $id)
    {
        $site = Site::findOrFail($id);

        $request->validate([
            'name'    => 'required|string|max:100',
            'slug'    => 'required|string|max:50|unique:sites,slug,' . $id,
            'url'     => 'required|url',
            'api_key' => 'required|string|min:6|unique:sites,api_key,' . $id,
            'city'    => 'nullable|string|max:100',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:200',
        ]);

        $site->update([
            'name'    => $request->name,
            'slug'    => $request->slug,
            'url'     => rtrim($request->url, '/'),
            'api_key' => $request->api_key,
            'city'    => $request->city,
            'phone'   => $request->phone,
            'address' => $request->address,
        ]);

        return redirect()->route('admin.sites')->with('success', 'Site ' . $site->name . ' mis à jour.');
    }

    public function siteDestroy($id)
    {
        $site  = Site::findOrFail($id);
        $count = TourneeLine::where('site_id', $id)->count();

        if ($count > 0) {
            return redirect()->route('admin.sites')
                ->with('error', 'Impossible : ' . $count . ' ligne(s) liée(s) à ce site.');
        }

        Fournisseur::where('site_id', $id)->delete();
        $site->delete();
        return redirect()->route('admin.sites')->with('success', 'Site supprimé.');
    }

    public function siteToggle($id)
    {
        $site = Site::findOrFail($id);
        $site->update(['is_active' => $site->is_active ? 0 : 1]);
        $etat = $site->is_active ? 'désactivé' : 'activé';
        return redirect()->route('admin.sites')->with('success', 'Site ' . $etat . '.');
    }

    public function testSite($id)
    {
        $site = Site::findOrFail($id);
        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'X-API-KEY' => $site->api_key,
                    'Accept'    => 'application/json',
                ])
                ->get($site->url . '/api/tournee/chauffeurs');

            return response()->json([
                'success' => $response->successful(),
                'status'  => $response->status(),
                'message' => $response->successful()
                    ? '✅ Connexion OK — ' . count($response->json()) . ' chauffeur(s)'
                    : '❌ HTTP ' . $response->status(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ ' . $e->getMessage(),
            ]);
        }
    }

    // ══════════════════════════════════════════════════════════
    // SYNCHRONISATION
    // ══════════════════════════════════════════════════════════
    public function syncPage()
    {
        $sites        = Site::where('is_active', true)->get();
        $fournisseurs = Fournisseur::with('site')->orderBy('name')->paginate(50);
        $lastSync     = cache('last_sync_at');
        return view('admin.sync', compact('sites', 'fournisseurs', 'lastSync'));
    }

    public function syncNow(Request $request)
    {
        $results = [];
        $sites   = Site::where('is_active', true)->get();

        foreach ($sites as $site) {
            try {
                $response = Http::timeout(15)
                    ->withHeaders([
                        'X-API-KEY' => $site->api_key,
                        'Accept'    => 'application/json',
                    ])
                    ->get($site->url . '/api/tournee/fournisseurs-list');

                if ($response->successful()) {
                    $fournisseurs = $response->json();
                    $count = 0;
                    foreach ($fournisseurs as $f) {
                        Fournisseur::updateOrCreate(
                            ['site_id' => $site->id, 'remote_id' => $f['id']],
                            [
                                'name'    => $f['name'],
                                'address' => isset($f['address']) ? $f['address'] : null,
                                'city'    => isset($f['city'])    ? $f['city']    : null,
                                'phone'   => isset($f['phone'])   ? $f['phone']   : null,
                            ]
                        );
                        $count++;
                    }
                    $results[$site->name] = ['success' => true, 'count' => $count];
                } else {
                    $results[$site->name] = ['success' => false, 'error' => 'HTTP ' . $response->status()];
                }
            } catch (\Exception $e) {
                $results[$site->name] = ['success' => false, 'error' => $e->getMessage()];
            }
        }

        cache(['last_sync_at' => now()->format('d/m/Y à H:i')], 3600);
        return redirect()->route('admin.sync')->with('sync_results', $results);
    }

    // ══════════════════════════════════════════════════════════
    // NETTOYAGE
    // ══════════════════════════════════════════════════════════
    public function cleanupPage()
    {
        $old = TourneeLine::where('date_tournee', '<', today()->subDays(30))->count();
        return view('admin.cleanup', compact('old'));
    }

    public function cleanupOld(Request $request)
    {
        $days  = (int) $request->input('days', 30);
        $count = TourneeLine::where('date_tournee', '<', today()->subDays($days))->count();
        TourneeLine::where('date_tournee', '<', today()->subDays($days))->delete();
        return redirect()->route('admin.cleanup')
            ->with('success', $count . ' ligne(s) supprimée(s) (plus de ' . $days . ' jours).');
    }
}