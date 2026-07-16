<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\TourneeParametre;
use App\Models\TourneeException;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminParametresController extends Controller
{
    // ── GET /admin/parametres ─────────────────────────────────
    public function index()
    {
        $sites = Site::with('parametre')->where('is_active', true)->orderBy('name')->get();
        return view('admin.parametres.index', compact('sites'));
    }

    // ── GET /admin/parametres/{siteId} ────────────────────────
    public function show($siteId)
    {
        $site      = Site::findOrFail($siteId);
        $parametre = TourneeParametre::firstOrCreate(
            ['site_id' => $siteId],
            [
                'jours_actifs'     => ['lundi','mardi','mercredi','jeudi','vendredi','samedi'],
                'heure_debut'      => '08:00',
                'heure_fin'        => '18:00',
                'creneaux'         => [
                    ['label' => '9h-11h',  'debut' => '09:00', 'fin' => '11:00'],
                    ['label' => '11h-12h', 'debut' => '11:00', 'fin' => '12:00'],
                    ['label' => '13h-14h', 'debut' => '13:00', 'fin' => '14:00'],
                    ['label' => '15h-16h', 'debut' => '15:00', 'fin' => '16:00'],
                    ['label' => '17h-18h', 'debut' => '17:00', 'fin' => '18:00'],
                ],
                'delai_min_heures' => 1,
                'is_active'        => true,
            ]
        );

        // Exceptions du site + globales, triées par date
        $exceptions = TourneeException::where(function ($q) use ($siteId) {
                $q->where('site_id', $siteId)->orWhereNull('site_id');
            })
            ->orderBy('date')
            ->get();

        // Mois à afficher dans le calendrier des exceptions
        $moisActuel = Carbon::now()->startOfMonth();

        return view('admin.parametres.show', compact('site', 'parametre', 'exceptions', 'moisActuel'));
    }

    // ── PUT /admin/parametres/{siteId} ────────────────────────
    public function update(Request $request, $siteId)
{
    $site = Site::findOrFail($siteId);

    \Log::info('CRENEAUX', ['creneaux' => $request->creneaux, 'jours' => $request->jours_actifs]);

    $validated = $request->validate([
        'jours_actifs'     => 'required|array|min:1',
        'heure_debut'      => 'required',
        'heure_fin'        => 'required',
        'delai_min_heures' => 'required|integer|min:0|max:24',
        'creneaux'         => 'required|array|min:1',
    ]);

    \Log::info('VALIDATED OK');

    $heureDebut = substr($request->heure_debut, 0, 5);
    $heureFin   = substr($request->heure_fin, 0, 5);

    $creneaux = array_values(array_map(function ($c) {
        return [
            'label' => trim($c['label']),
            'debut' => substr($c['debut'], 0, 5),
            'fin'   => substr($c['fin'], 0, 5),
        ];
    }, $request->creneaux));

    TourneeParametre::updateOrCreate(
        ['site_id' => $siteId],
        [
            'jours_actifs'     => $request->jours_actifs,
            'heure_debut'      => $heureDebut,
            'heure_fin'        => $heureFin,
            'delai_min_heures' => $request->delai_min_heures,
            'creneaux'         => $creneaux,
            'is_active'        => $request->has('is_active') ? 1 : 0,
            'notes'            => $request->notes,
        ]
    );

    return redirect()->route('admin.parametres.show', $siteId)
        ->with('success', 'Paramètres de ' . $site->name . ' mis à jour.');
}

    // ── POST /admin/parametres/{siteId}/exceptions ────────────
    public function addException(Request $request, $siteId)
    {
        $request->validate([
            'date'      => 'required|date',
            'label'     => 'required|string|max:100',
            'is_closed' => 'nullable|boolean',
            'is_global' => 'nullable|boolean',
            'notes'     => 'nullable|string|max:200',
        ]);

        $isGlobal = $request->boolean('is_global');

        TourneeException::updateOrCreate(
            [
                'site_id' => $isGlobal ? null : $siteId,
                'date'    => $request->date,
            ],
            [
                'label'     => $request->label,
                'is_closed' => $request->boolean('is_closed', true),
                'notes'     => $request->notes,
            ]
        );

        return redirect()->route('admin.parametres.show', $siteId)
            ->with('success', 'Exception ajoutée : ' . $request->label . ' le ' . Carbon::parse($request->date)->format('d/m/Y'));
    }

    // ── DELETE /admin/parametres/exceptions/{id} ──────────────
    public function deleteException($id)
    {
        $exception = TourneeException::findOrFail($id);
        $siteId    = $exception->site_id;
        $exception->delete();

        return redirect()->back()->with('success', 'Exception supprimée.');
    }

    // ── GET /admin/parametres/exceptions/globales ─────────────
    public function exceptionsGlobales()
    {
        $exceptions = TourneeException::whereNull('site_id')->orderBy('date')->get();
        $sites      = Site::where('is_active', true)->orderBy('name')->get();
        return view('admin.parametres.exceptions-globales', compact('exceptions', 'sites'));
    }

    // ── POST /admin/parametres/exceptions/globales ────────────
    public function addExceptionGlobale(Request $request)
    {
        $request->validate([
            'date'  => 'required|date',
            'label' => 'required|string|max:100',
            'notes' => 'nullable|string|max:200',
        ]);

        TourneeException::updateOrCreate(
            ['site_id' => null, 'date' => $request->date],
            [
                'label'     => $request->label,
                'is_closed' => true,
                'notes'     => $request->notes,
            ]
        );

        return redirect()->route('admin.parametres.exceptions.globales')
            ->with('success', 'Exception globale ajoutée.');
    }
}