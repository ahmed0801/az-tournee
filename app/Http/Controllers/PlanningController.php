<?php

namespace App\Http\Controllers;

use App\Models\TourneeLine;
use App\Models\Chauffeur;
use App\Models\Site;
use App\Models\BarcodeHistory;
use Illuminate\Http\Request;

class PlanningController extends Controller
{
    // ── GET /planning ─────────────────────────────────────────
    public function index(Request $request)
    {
        $date       = $request->date ?? today()->format('Y-m-d');
        $chauffeurs = Chauffeur::where('is_active', true)->orderBy('name')->get();
        $sites      = Site::where('is_active', true)->orderBy('name')->get();

        $query = TourneeLine::with(['chauffeur', 'fournisseur', 'site'])
            ->whereDate('date_tournee', $date);

        if ($request->filled('chauffeur_id'))
            $query->where('chauffeur_id', $request->chauffeur_id);
        if ($request->filled('site_id'))
            $query->where('site_id', $request->site_id);
        if ($request->filled('statut'))
            $query->where('statut', $request->statut);
        // Recherche par numéro de facture (utilisé par les vendeurs)
        if ($request->filled('search'))
            $query->where('source_numdoc', 'like', '%' . $request->search . '%');

        // Grouper par créneau puis par fournisseur
        $allSlots = ['9h-11h', '11h-12h', '13h-14h', '15h-16h', '17h-18h', 'matin', 'apres_midi'];
        $lignesParCreneau = collect();

        foreach ($allSlots as $slot) {
            $lignes = (clone $query)
                ->where('slot', $slot)
                ->orderBy('fournisseur_name')
                ->get()
                ->groupBy('fournisseur_name');
            if ($lignes->isNotEmpty()) {
                $lignesParCreneau[$slot] = $lignes;
            }
        }

        // Compatibilité ancienne vue
        $lignesMatin     = $lignesParCreneau->only(['matin', '9h-11h', '11h-12h'])->collapse();
        $lignesApresMidi = $lignesParCreneau->only(['apres_midi', '13h-14h', '15h-16h', '17h-18h'])->collapse();

        $stats = [
            'total'      => TourneeLine::whereDate('date_tournee', $date)->count(),
            'en_attente' => TourneeLine::whereDate('date_tournee', $date)->where('statut', 'en_attente')->count(),
            'assigné'    => TourneeLine::whereDate('date_tournee', $date)->where('statut', 'assigné')->count(),
            'en_route'   => TourneeLine::whereDate('date_tournee', $date)->where('statut', 'en_route')->count(),
            'recupere'   => TourneeLine::whereDate('date_tournee', $date)->where('statut', 'recupere')->count(),
            'au_magasin' => TourneeLine::whereDate('date_tournee', $date)->where('statut', 'au_magasin')->count(),
            'probleme'   => TourneeLine::whereDate('date_tournee', $date)->where('statut', 'probleme')->count(),
            'non_assignees' => TourneeLine::whereDate('date_tournee', $date)->whereNull('chauffeur_id')->whereNotIn('statut', ['recupere', 'au_magasin'])->count(),
        ];

        return view('planning.index', compact(
            'lignesMatin', 'lignesApresMidi', 'lignesParCreneau',
            'chauffeurs', 'sites', 'date', 'stats'
        ));
    }

    // ── POST /planning/assign ──────────────────────────────────
    public function assign(Request $request)
    {
        $request->validate([
            'line_id'      => 'required|exists:tournee_lines,id',
            'chauffeur_id' => 'required|exists:chauffeurs,id',
        ]);

        TourneeLine::findOrFail($request->line_id)->update([
            'chauffeur_id' => $request->chauffeur_id,
            'statut'       => 'assigné',
        ]);

        return response()->json(['success' => true]);
    }

    // ── POST /planning/statut ──────────────────────────────────
    public function updateStatut(Request $request)
    {
        $request->validate([
            'line_id' => 'required|exists:tournee_lines,id',
            'statut'  => 'required|in:en_attente,assigné,en_route,recupere,au_magasin,probleme',
            'notes'   => 'nullable|string',
        ]);

        $line = TourneeLine::findOrFail($request->line_id);
        $line->update([
            'statut'         => $request->statut,
            'probleme_notes' => $request->statut === 'probleme'
                ? $request->notes
                : $line->probleme_notes,
        ]);

        return response()->json(['success' => true, 'statut' => $request->statut]);
    }

    // ── GET /chauffeur ─────────────────────────────────────────
    public function chauffeurLogin()
    {
        if (session('chauffeur_id')) {
            return redirect()->route('chauffeur.planning');
        }
        return view('chauffeur.login');
    }

    // ── POST /chauffeur/login ──────────────────────────────────
    public function chauffeurLoginPost(Request $request)
    {
        $request->validate([
            'name'     => 'required|string',
            'password' => 'required|string',
        ]);

        $chauffeur = Chauffeur::where('name', $request->name)
            ->where('is_active', true)
            ->first();

        if (!$chauffeur || !password_verify($request->password, $chauffeur->password)) {
            return back()->withErrors(['login' => 'Identifiants incorrects.']);
        }

        session([
            'chauffeur_id'   => $chauffeur->id,
            'chauffeur_name' => $chauffeur->name,
        ]);

        return redirect()->route('chauffeur.planning');
    }

    // ── GET /chauffeur/planning ────────────────────────────────
    public function chauffeurPlanning()
{
    $chauffeurId = session('chauffeur_id');
    if (!$chauffeurId) return redirect()->route('chauffeur.login');

    $chauffeur = Chauffeur::findOrFail($chauffeurId);

    // Filtre site : site du chauffeur par défaut, ou site sélectionné en session, ou tous
    $chauffeurSiteId = $chauffeur->site_id; // site par défaut du chauffeur
    $sessionSiteId   = session('chauffeur_site_filter'); // bascule manuelle
    $filteredSiteId  = $sessionSiteId ?? $chauffeurSiteId; // priorité à la session

    // Une seule requête principale
    $query = TourneeLine::with(['site', 'fournisseur'])
        ->whereDate('date_tournee', today())
        ->where('chauffeur_id', $chauffeurId)
        ->orderBy('slot')
        ->orderBy('fournisseur_name')
        ->orderBy('article_code');

    if ($filteredSiteId) {
        $query->where('site_id', $filteredSiteId);
    }

    $toutesLignes = $query->get();

    // Sites disponibles pour le chauffeur (pour le bouton bascule)
    $sites = \App\Models\Site::where('is_active', true)->orderBy('name')->get();
    $currentSiteId = $filteredSiteId;

    // Grouper par créneau puis par fournisseur
    $lignesParCreneau = $toutesLignes->groupBy('slot')
        ->map(fn($group) => $group->groupBy('fournisseur_name'));

    // Stats
    $stats = [
        'total'    => $toutesLignes->count(),
        'recupere' => $toutesLignes->whereIn('statut', ['recupere', 'livre_client'])->count(),
        'restant'  => $toutesLignes->whereNotIn('statut', ['recupere', 'au_magasin', 'livre_client'])->count(),
    ];

    // Pièces non assignées (pour la section "à prendre")
    $nonAssignees = TourneeLine::with(['site', 'fournisseur'])
        ->whereDate('date_tournee', today())
        ->whereNull('chauffeur_id')
        ->whereNotIn('statut', ['recupere', 'au_magasin', 'livre_client'])
        ->orderBy('fournisseur_name')
        ->orderBy('article_code')
        ->get();

    return view('chauffeur.planning', compact(
        'chauffeur', 
        'lignesParCreneau', 
        'nonAssignees', 
        'nonAssignees', 
        'stats',
        'sites',
        'currentSiteId'
    ));
}

    // ── POST /chauffeur/scan ───────────────────────────────────
    public function scan(Request $request)
    {
        try {
            // Priorité : session → puis body (fallback si cookie non transmis)
            $chauffeurId = session('chauffeur_id');
            if (!$chauffeurId) {
                $chauffeurId = $request->input('chauffeur_id');
            }
            if (!$chauffeurId) {
                return response()->json(['error' => 'Non connecté — rechargez et reconnectez-vous'], 401);
            }

            $lineId  = $request->input('line_id');
            $barcode = $request->input('barcode');

            if (!$lineId || !$barcode) {
                return response()->json(['error' => 'line_id et barcode requis'], 422);
            }

            $line = TourneeLine::find($lineId);
            if (!$line) {
                return response()->json(['error' => 'Ligne introuvable (id: ' . $lineId . ')'], 404);
            }

            $matched = ($line->barcode && trim($line->barcode) === trim($barcode));

            // Enregistrer dans l'historique
            BarcodeHistory::create([
                'tournee_line_id' => $line->id,
                'barcode_scanned' => $barcode,
                'article_code'    => $line->article_code,
                'matched'         => $matched ? 1 : 0,
                'chauffeur_id'    => $chauffeurId,
            ]);

            if ($matched) {
                $line->update([
                    'statut'          => 'recupere',
                    'scanned_barcode' => $barcode,
                    'scanned_at'      => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'matched' => true,
                    'message' => '✅ ' . $line->article_code . ' — ' . $line->article_name,
                    'statut'  => 'recupere',
                ]);
            }

            // Code inconnu — proposer l'association
            return response()->json([
                'success'         => true,
                'matched'         => false,
                'barcode_scanned' => $barcode,
                'article_code'    => $line->article_code,
                'article_name'    => $line->article_name,
                'line_id'         => $line->id,
                'message'         => '⚠️ Code non reconnu. Associer à ' . $line->article_code . ' ?',
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Scan error: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'error'   => 'Erreur serveur: ' . $e->getMessage(),
                'success' => false,
            ], 500);
        }
    }

    // ── POST /chauffeur/scan/confirm ───────────────────────────
    public function scanConfirm(Request $request)
    {
        $chauffeurId = session('chauffeur_id');
        if (!$chauffeurId) return response()->json(['error' => 'Non connecté'], 401);

        $request->validate([
            'line_id' => 'required|exists:tournee_lines,id',
            'barcode' => 'required|string',
        ]);

        $line = TourneeLine::findOrFail($request->line_id);

        $line->update([
            'statut'          => 'recupere',
            'scanned_barcode' => $request->barcode,
            'scanned_at'      => now(),
        ]);

        BarcodeHistory::create([
            'tournee_line_id' => $line->id,
            'barcode_scanned' => $request->barcode,
            'article_code'    => $line->article_code,
            'matched'         => true,
            'chauffeur_id'    => $chauffeurId,
        ]);

        // Notifier le site source pour mettre à jour le barcode
        // (en prod uniquement, désactivé si QUEUE_CONNECTION=sync et site inaccessible)
        try {
            \App\Jobs\UpdateArticleBarcodeOnSite::dispatch(
                $line->site,
                $line->article_code,
                $request->barcode
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('UpdateBarcode job failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => '✅ Code-barres associé et pièce récupérée !',
            'statut'  => 'recupere',
        ]);
    }

    // ── POST /chauffeur/probleme ───────────────────────────────
    public function signalProbleme(Request $request)
    {
        $chauffeurId = session('chauffeur_id');
        if (!$chauffeurId) return response()->json(['error' => 'Non connecté'], 401);

        $request->validate([
            'line_id' => 'required|exists:tournee_lines,id',
            'notes'   => 'required|string|max:500',
        ]);

        TourneeLine::findOrFail($request->line_id)->update([
            'statut'         => 'probleme',
            'probleme_notes' => $request->notes,
        ]);

        return response()->json(['success' => true]);
    }




    // ── POST /chauffeur/livre-client ──────────────────────────────
// ── POST /chauffeur/livre-client ──────────────────────────────
public function livreClient(Request $request)
{
    try {
        $chauffeurId = session('chauffeur_id') ?? $request->input('chauffeur_id');
        if (!$chauffeurId) {
            return response()->json(['error' => 'Non connecté'], 401);
        }

        $line = TourneeLine::find($request->input('line_id'));
        if (!$line) {
            return response()->json(['error' => 'Ligne introuvable'], 404);
        }

        $line->update([
            'statut'       => 'livre_client',
            'scanned_at'   => now(),
            'scanned_barcode' => $line->scanned_barcode ?? 'LIVRE_CLIENT',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pièce marquée comme livrée directement au client',
            'statut'  => 'livre_client',
        ]);

    } catch (\Exception $e) {
        \Log::error('livreClient error: ' . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
}






    // ── POST /chauffeur/self-assign ──────────────────────────────
    // Le chauffeur s'auto-assigne une piece non assignee
    public function selfAssign(Request $request)
    {
        try {
            $chauffeurId = session('chauffeur_id');
            if (!$chauffeurId) {
                $chauffeurId = $request->input('chauffeur_id');
            }
            if (!$chauffeurId) {
                return response()->json(['error' => 'Non connecte'], 401);
            }

            $lineId = $request->input('line_id');
            $line   = TourneeLine::find($lineId);

            if (!$line) {
                return response()->json(['error' => 'Ligne introuvable'], 404);
            }

            if ($line->chauffeur_id && $line->chauffeur_id != $chauffeurId) {
                $autreNom = optional($line->chauffeur)->name ?? 'un autre chauffeur';
                return response()->json([
                    'error' => 'Cette piece est deja assignee a ' . $autreNom,
                ], 409);
            }

            $line->update([
                'chauffeur_id' => $chauffeurId,
                'statut'       => 'assigne',
            ]);

            return response()->json([
                'success'      => true,
                'message'      => 'Piece assignee — elle apparait maintenant dans votre liste',
                'chauffeur_id' => $chauffeurId,
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('selfAssign error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ── GET /suivi/{numdoc} — Vue lecture seule vendeur ─────────
    public function suivi($numdoc)
    {
        $lignes = TourneeLine::with(['chauffeur', 'fournisseur', 'site'])
            ->where('source_numdoc', $numdoc)
            ->orderBy('date_tournee')
            ->orderBy('slot')
            ->get();

        $stats = [
    'total'        => $lignes->count(),
    'recupere'     => $lignes->where('statut', 'recupere')->count(),
    'livre_client' => $lignes->where('statut', 'livre_client')->count(),
    'en_cours'     => $lignes->whereIn('statut', ['assigné', 'en_route'])->count(),
    'probleme'     => $lignes->where('statut', 'probleme')->count(),
];

        return view('planning.suivi', compact('lignes', 'numdoc', 'stats'));
    }

    // ── GET /rapport ───────────────────────────────────────────
    // ── GET /suivi/{numdoc} — Vue lecture seule pour vendeurs ───


    public function rapport(Request $request)
    {
        $dateFrom = $request->date_from ?? today()->startOfWeek()->format('Y-m-d');
        $dateTo   = $request->date_to   ?? today()->format('Y-m-d');

        $lines = TourneeLine::with(['chauffeur', 'site', 'fournisseur'])
            ->whereBetween('date_tournee', [$dateFrom, $dateTo])
            ->orderBy('date_tournee', 'desc')
            ->get();

        // PHP 7.4 : pas de fn() — utiliser function()
        $statsByDay = $lines->groupBy(function ($l) {
            return $l->date_tournee->format('Y-m-d');
        })->map(function ($dl) {
            return [
                'total'      => $dl->count(),
                'recupere'   => $dl->where('statut', 'recupere')->count(),
                'probleme'   => $dl->where('statut', 'probleme')->count(),
                'en_attente' => $dl->whereIn('statut', ['en_attente', 'assigné'])->count(),
            ];
        });

        $statsByChauffeur = $lines->groupBy('chauffeur_id')
            ->map(function ($cl) {
                return [
                    'name'     => optional($cl->first()->chauffeur)->name ?? 'Non assigné',
                    'total'    => $cl->count(),
                    'recupere' => $cl->where('statut', 'recupere')->count(),
                ];
            });

        $statsBySite = $lines->groupBy('site_id')
            ->map(function ($sl) {
                return [
                    'name'     => optional($sl->first()->site)->name ?? '?',
                    'total'    => $sl->count(),
                    'recupere' => $sl->where('statut', 'recupere')->count(),
                ];
            });

        return view('planning.rapport', compact(
            'lines', 'statsByDay', 'statsByChauffeur',
            'statsBySite', 'dateFrom', 'dateTo'
        ));
    }

    public function switchSite(Request $request)
    {
        $siteId = $request->input('site_id');
        if ($siteId) {
            session(['chauffeur_site_filter' => $siteId]);
        } else {
            session()->forget('chauffeur_site_filter');
        }
        return redirect()->route('chauffeur.planning');
    }
}

