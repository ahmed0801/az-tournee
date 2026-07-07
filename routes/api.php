<?php
// ════════════════════════════════════════════════════════════════
// routes/api.php  — REMPLACER le contenu existant par ceci
// ════════════════════════════════════════════════════════════════

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ApiTourneeController;
 
Route::prefix('tournee')->group(function () {
    Route::get('lines',              [ApiTourneeController::class, 'index']);
    Route::post('lines',             [ApiTourneeController::class, 'store']);
    Route::delete('lines/{id}',      [ApiTourneeController::class, 'destroy']);
    Route::get('chauffeurs',         [ApiTourneeController::class, 'chauffeurs']);
    Route::post('fournisseurs/sync', [ApiTourneeController::class, 'syncFournisseurs']);
});

