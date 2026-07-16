<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiTourneeController;

/*
|--------------------------------------------------------------------------
| API Routes — Projet Tournée
| Protégées par X-API-KEY (vérification dans chaque controller)
|--------------------------------------------------------------------------
*/

// ── Lignes tournée ────────────────────────────────────────────
Route::get('/tournee/lines',         [ApiTourneeController::class, 'index']);
Route::post('/tournee/lines',        [ApiTourneeController::class, 'store']);
Route::delete('/tournee/lines/{id}', [ApiTourneeController::class, 'destroy']);

// ── Chauffeurs ────────────────────────────────────────────────
Route::get('/tournee/chauffeurs',    [ApiTourneeController::class, 'chauffeurs']);

// ── Fournisseurs ──────────────────────────────────────────────
Route::post('/tournee/fournisseurs/sync', [ApiTourneeController::class, 'syncFournisseurs']);

// ── Paramètres tournée (créneaux, jours actifs, exceptions) ──
// Appelée par aznegoce au clic sur le bouton 🚚
// GET /api/tournee/parametres?date=2025-07-07
Route::get('/tournee/parametres',    [ApiTourneeController::class, 'parametres']);