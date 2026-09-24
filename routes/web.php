<?php

// ════════════════════════════════════════════════════════════════
// routes/web.php COMPLET — Projet tournee
// Remplace entièrement le fichier existant
// ════════════════════════════════════════════════════════════════

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlanningController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminParametresController;

// ── Racine ────────────────────────────────────────────────────
Route::get('/', function () {
    return redirect('/planning');
});

// ── DEBUG TEMPORAIRE — à supprimer après résolution ──────────
Route::post('/debug/scan', function (\Illuminate\Http\Request $request) {
    try {
        $session_ok  = session()->has('chauffeur_id');
        $chauffeurId = session('chauffeur_id');
        $lineId      = $request->input('line_id');
        $barcode     = $request->input('barcode');

        $line = $lineId ? \App\Models\TourneeLine::find($lineId) : null;

        return response()->json([
            'session_ok'    => $session_ok,
            'chauffeur_id'  => $chauffeurId,
            'line_id'       => $lineId,
            'barcode'       => $barcode,
            'line_found'    => $line ? true : false,
            'line_data'     => $line ? [
                'id'           => $line->id,
                'article_code' => $line->article_code,
                'barcode'      => $line->barcode,
                'statut'       => $line->statut,
            ] : null,
            'barcode_history_table' => \Illuminate\Support\Facades\Schema::hasTable('barcode_history'),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error'   => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'trace'   => substr($e->getTraceAsString(), 0, 500),
        ], 500);
    }
});

// ══════════════════════════════════════════════════════════════
// PLANNING DISPATCHER
// ══════════════════════════════════════════════════════════════
Route::get('/planning',          [PlanningController::class, 'index'])->name('planning.index');
Route::post('/planning/assign',  [PlanningController::class, 'assign'])->name('planning.assign');
Route::post('/planning/statut',  [PlanningController::class, 'updateStatut'])->name('planning.statut');
Route::get('/rapport',           [PlanningController::class, 'rapport'])->name('planning.rapport');
Route::get('/suivi/{numdoc}',    [PlanningController::class, 'suivi'])->name('planning.suivi');

// ══════════════════════════════════════════════════════════════
// INTERFACE CHAUFFEUR (mobile Panasonic)
// Note : scan/probleme/logout exclus du CSRF dans VerifyCsrfToken.php
// ══════════════════════════════════════════════════════════════
Route::get('/chauffeur',               [PlanningController::class, 'chauffeurLogin'])->name('chauffeur.login');
Route::post('/chauffeur/login',        [PlanningController::class, 'chauffeurLoginPost'])->name('chauffeur.login.post');
Route::get('/chauffeur/planning',      [PlanningController::class, 'chauffeurPlanning'])->name('chauffeur.planning');
Route::post('/chauffeur/scan',         [PlanningController::class, 'scan'])->name('chauffeur.scan');
Route::post('/chauffeur/scan/confirm', [PlanningController::class, 'scanConfirm'])->name('chauffeur.scan.confirm');
Route::post('/chauffeur/probleme',     [PlanningController::class, 'signalProbleme'])->name('chauffeur.probleme');
Route::post('/chauffeur/self-assign',  [PlanningController::class, 'selfAssign'])->name('chauffeur.self_assign');
Route::post('/chauffeur/switch-site',  [PlanningController::class, 'switchSite'])->name('chauffeur.switch_site');

// ── Vue lecture seule vendeur ─────────────────────────────────
Route::get('/suivi/{numdoc}', [PlanningController::class, 'suivi'])->name('planning.suivi');

Route::post('/chauffeur/logout', function () {
    session()->forget(['chauffeur_id', 'chauffeur_name']);
    return redirect('/chauffeur');
})->name('chauffeur.logout');



Route::get('/planning/creneaux/{siteId}', [PlanningController::class, 'getCreneauxForSite'])->name('planning.creneaux');
Route::post('/planning/update-slot', [PlanningController::class, 'updateSlot'])->name('planning.update_slot');
Route::post('/planning/update-date', [PlanningController::class, 'updateDate'])->name('planning.update_date');
Route::delete('/planning/delete-line/{id}', [PlanningController::class, 'deleteLine'])->name('planning.delete_line');


Route::post('/planning/basculer-retards', [PlanningController::class, 'basculerRetards'])->name('planning.basculer_retards');
Route::get('/planning/retards-detail', [PlanningController::class, 'retardsDetail'])->name('planning.retards_detail');
Route::post('/planning/basculer-une/{id}', [PlanningController::class, 'basculerUne'])->name('planning.basculer_une');


// ══════════════════════════════════════════════════════════════
// ADMINISTRATION
// ══════════════════════════════════════════════════════════════
Route::prefix('admin')->group(function () {

    // Dashboard
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');

    // ── Chauffeurs ────────────────────────────────────────────
    Route::get('/chauffeurs',
        [AdminController::class, 'chauffeurs'])->name('admin.chauffeurs');
    Route::post('/chauffeurs',
        [AdminController::class, 'chauffeurStore'])->name('admin.chauffeurs.store');
    Route::put('/chauffeurs/{id}',
        [AdminController::class, 'chauffeurUpdate'])->name('admin.chauffeurs.update');
    Route::delete('/chauffeurs/{id}',
        [AdminController::class, 'chauffeurDestroy'])->name('admin.chauffeurs.destroy');
    Route::post('/chauffeurs/{id}/reset-password',
        [AdminController::class, 'chauffeurResetPassword'])->name('admin.chauffeurs.reset');

    // ── Sites ─────────────────────────────────────────────────
    Route::get('/sites',
        [AdminController::class, 'sites'])->name('admin.sites');
    Route::post('/sites',
        [AdminController::class, 'siteStore'])->name('admin.sites.store');
    Route::put('/sites/{id}',
        [AdminController::class, 'siteUpdate'])->name('admin.sites.update');
    Route::delete('/sites/{id}',
        [AdminController::class, 'siteDestroy'])->name('admin.sites.destroy');
    Route::post('/sites/{id}/toggle',
        [AdminController::class, 'siteToggle'])->name('admin.sites.toggle');
    Route::get('/sites/{id}/test',
        [AdminController::class, 'testSite'])->name('admin.sites.test');

    // ── Synchronisation ───────────────────────────────────────
    Route::get('/sync',
        [AdminController::class, 'syncPage'])->name('admin.sync');
    Route::post('/sync/now',
        [AdminController::class, 'syncNow'])->name('admin.sync.now');

    // ── Nettoyage ─────────────────────────────────────────────
    Route::get('/cleanup',
        [AdminController::class, 'cleanupPage'])->name('admin.cleanup');
    Route::post('/cleanup',
        [AdminController::class, 'cleanupOld'])->name('admin.cleanup.old');

    // ── Paramètres tournée ────────────────────────────────────
    Route::get('/parametres',
        [AdminParametresController::class, 'index'])->name('admin.parametres.index');
    Route::get('/parametres/{siteId}',
        [AdminParametresController::class, 'show'])->name('admin.parametres.show');
    Route::put('/parametres/{siteId}',
        [AdminParametresController::class, 'update'])->name('admin.parametres.update');
    Route::post('/parametres/{siteId}/exceptions',
        [AdminParametresController::class, 'addException'])->name('admin.parametres.exceptions.add');
    Route::delete('/parametres/exceptions/{id}',
        [AdminParametresController::class, 'deleteException'])->name('admin.parametres.exceptions.delete');
    Route::get('/parametres/exceptions/globales',
        [AdminParametresController::class, 'exceptionsGlobales'])->name('admin.parametres.exceptions.globales');
    Route::post('/parametres/exceptions/globales',
        [AdminParametresController::class, 'addExceptionGlobale'])->name('admin.parametres.exceptions.globales.add');


        Route::post('/chauffeur/livre-client', [PlanningController::class, 'livreClient'])->name('chauffeur.livre_client');
});

// ══════════════════════════════════════════════════════════════
// API reçue des 4 sites (dans api.php normalement,
// mais si tu utilises web.php pour tout, décommente ici)
// ══════════════════════════════════════════════════════════════
// use App\Http\Controllers\Api\ApiTourneeController;
// Route::prefix('api/tournee')->group(function () {
//     Route::get('lines',              [ApiTourneeController::class, 'index']);
//     Route::post('lines',             [ApiTourneeController::class, 'store']);
//     Route::delete('lines/{id}',      [ApiTourneeController::class, 'destroy']);
//     Route::get('chauffeurs',         [ApiTourneeController::class, 'chauffeurs']);
//     Route::post('fournisseurs/sync', [ApiTourneeController::class, 'syncFournisseurs']);
// });