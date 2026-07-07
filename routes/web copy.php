<?php

// ════════════════════════════════════════════════════════════════
// routes/web.php COMPLET — Projet tournee
// Remplace entièrement le fichier existant
// ════════════════════════════════════════════════════════════════

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlanningController;
use App\Http\Controllers\AdminController;

// ── Racine ────────────────────────────────────────────────────
Route::get('/', function () {
    return redirect('/planning');
});

// ══════════════════════════════════════════════════════════════
// PLANNING DISPATCHER
// ══════════════════════════════════════════════════════════════
Route::get('/planning',          [PlanningController::class, 'index'])->name('planning.index');
Route::post('/planning/assign',  [PlanningController::class, 'assign'])->name('planning.assign');
Route::post('/planning/statut',  [PlanningController::class, 'updateStatut'])->name('planning.statut');
Route::get('/rapport',           [PlanningController::class, 'rapport'])->name('planning.rapport');

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
Route::post('/chauffeur/logout', function () {
    session()->forget(['chauffeur_id', 'chauffeur_name']);
    return redirect('/chauffeur');
})->name('chauffeur.logout');

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