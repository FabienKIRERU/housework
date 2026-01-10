<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;



// Authentification
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| ROUTES PROTÉGÉES (Besoin d'un Token Bearer valide)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Auth & Profil
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // --- ZONE ADMIN ---
    // On pourrait ajouter un middleware supplémentaire 'can:isAdmin' plus tard
    // Route::prefix('admin')->group(function () {
        
    //     // Réservations
    //     Route::get('/reservations', [ReservationController::class, 'indexAdmin']);
    //     Route::put('/reservations/{reservation}', [ReservationController::class, 'update']);

    //     // Services
    //     Route::post('/services', [ServiceController::class, 'store']);
        
    //     // Staff
    //     Route::get('/houseworkers', [ReservationController::class, 'listHouseworkers']);
    // });
});





Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
