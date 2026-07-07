<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TourneeLine;
use App\Models\Fournisseur;
use App\Models\Chauffeur;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiTourneeController extends Controller
{
    // ── Auth par clé API ──────────────────────────────────────
    private function getSite(Request $request)
    {
        $apiKey = $request->header('X-API-KEY') ?? $request->input('api_key');
        return Site::where('api_key', $apiKey)->where('is_active', true)->first();
    }

    // ── GET /api/tournee/lines ────────────────────────────────
    public function index(Request $request)
    {
        $site = $this->getSite($request);
        if (!$site) return response()->json(['error' => 'Clé API invalide'], 401);

        $query = TourneeLine::with(['chauffeur', 'fournisseur'])
            ->where('site_id', $site->id);

        if ($request->filled('source_id'))
            $query->where('source_id', $request->source_id);
        if ($request->filled('source_type'))
            $query->where('source_type', $request->source_type);

        $lines = $query->get()->map(function ($l) {
            return [
                'id'             => $l->id,
                'source_line_id' => $l->source_line_id,
                'article_code'   => $l->article_code,
                'article_name'   => $l->article_name,
                'quantity'       => $l->quantity,
                'statut'         => $l->statut,
                'statut_label'   => $l->statut_label,
                'statut_color'   => $l->statut_color,
                'slot'           => $l->slot,
                'slot_label'     => $l->slot_label,
                'date_tournee'   => $l->date_tournee ? $l->date_tournee->format('d/m/Y') : null,
                'chauffeur'      => optional($l->chauffeur)->name,
                'fournisseur'    => optional($l->fournisseur)->name ?? $l->fournisseur_name,
            ];
        });

        return response()->json($lines);
    }

    // ── POST /api/tournee/lines ───────────────────────────────
    public function store(Request $request)
    {
        $site = $this->getSite($request);
        if (!$site) return response()->json(['error' => 'Clé API invalide'], 401);

        $validated = $request->validate([
            'source_type'           => 'required|in:facture_vente,commande_achat,bl',
            'source_id'             => 'required|integer',
            'source_numdoc'         => 'required|string',
            'source_line_id'        => 'nullable|integer',
            'article_code'          => 'required|string',
            'article_name'          => 'required|string',
            'quantity'              => 'required|numeric|min:0.01',
            'barcode'               => 'nullable|string',
            'fournisseur_remote_id' => 'nullable|integer',
            'fournisseur_name'      => 'nullable|string',
            'chauffeur_id'          => 'nullable|integer|exists:chauffeurs,id',
            'date_tournee'          => 'required|date',
            'slot'                  => 'required|in:matin,apres_midi',
            'notes'                 => 'nullable|string',
            'created_by_name'       => 'nullable|string',
        ]);

        $fournisseurId   = null;
        $fournisseurName = isset($validated['fournisseur_name']) ? $validated['fournisseur_name'] : null;

        if (!empty($validated['fournisseur_remote_id'])) {
            $fourn = Fournisseur::firstOrCreate(
                ['site_id' => $site->id, 'remote_id' => $validated['fournisseur_remote_id']],
                ['name' => $fournisseurName ?? 'Inconnu']
            );
            if ($fournisseurName && $fourn->name !== $fournisseurName) {
                $fourn->update(['name' => $fournisseurName]);
            }
            $fournisseurId   = $fourn->id;
            $fournisseurName = $fourn->name;
        }

        $line = TourneeLine::create([
            'site_id'          => $site->id,
            'source_type'      => $validated['source_type'],
            'source_id'        => $validated['source_id'],
            'source_numdoc'    => $validated['source_numdoc'],
            'source_line_id'   => isset($validated['source_line_id']) ? $validated['source_line_id'] : null,
            'article_code'     => $validated['article_code'],
            'article_name'     => $validated['article_name'],
            'quantity'         => $validated['quantity'],
            'barcode'          => isset($validated['barcode']) ? $validated['barcode'] : null,
            'fournisseur_id'   => $fournisseurId,
            'fournisseur_name' => $fournisseurName,
            'chauffeur_id'     => isset($validated['chauffeur_id']) ? $validated['chauffeur_id'] : null,
            'date_tournee'     => $validated['date_tournee'],
            'slot'             => $validated['slot'],
            'notes'            => isset($validated['notes']) ? $validated['notes'] : null,
            'created_by_name'  => isset($validated['created_by_name']) ? $validated['created_by_name'] : null,
            'statut'           => 'en_attente',
        ]);

        return response()->json([
            'success' => true,
            'line_id' => $line->id,
            'message' => 'Ligne ajoutée à la tournée',
        ], 201);
    }

    // ── DELETE /api/tournee/lines/{id} ────────────────────────
    public function destroy(Request $request, $id)
    {
        $site = $this->getSite($request);
        if (!$site) return response()->json(['error' => 'Clé API invalide'], 401);

        $line = TourneeLine::where('id', $id)
            ->where('site_id', $site->id)
            ->where('statut', 'en_attente')
            ->first();

        if (!$line) {
            return response()->json(['error' => 'Ligne introuvable ou déjà en cours'], 404);
        }

        $line->delete();
        return response()->json(['success' => true]);
    }

    // ── GET /api/tournee/chauffeurs ───────────────────────────
    public function chauffeurs(Request $request)
    {
        $site = $this->getSite($request);
        if (!$site) return response()->json(['error' => 'Clé API invalide'], 401);

        $chauffeurs = Chauffeur::where('is_active', true)
            ->select('id', 'name', 'phone')
            ->orderBy('name')
            ->get();

        return response()->json($chauffeurs);
    }

    // ── POST /api/tournee/fournisseurs/sync ───────────────────
    public function syncFournisseurs(Request $request)
    {
        $site = $this->getSite($request);
        if (!$site) return response()->json(['error' => 'Clé API invalide'], 401);

        $data = $request->validate([
            'fournisseurs'           => 'required|array',
            'fournisseurs.*.id'      => 'required|integer',
            'fournisseurs.*.name'    => 'required|string',
            'fournisseurs.*.address' => 'nullable|string',
            'fournisseurs.*.city'    => 'nullable|string',
            'fournisseurs.*.phone'   => 'nullable|string',
        ]);

        foreach ($data['fournisseurs'] as $f) {
            Fournisseur::updateOrCreate(
                ['site_id' => $site->id, 'remote_id' => $f['id']],
                [
                    'name'    => $f['name'],
                    'address' => isset($f['address']) ? $f['address'] : null,
                    'city'    => isset($f['city']) ? $f['city'] : null,
                    'phone'   => isset($f['phone']) ? $f['phone'] : null,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'synced'  => count($data['fournisseurs']),
        ]);
    }
}