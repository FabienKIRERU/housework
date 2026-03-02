<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\HouseworkerController;
use App\Http\Controllers\Admin\ReservationController as AdminReservationController;



// Authentification
Route::post('/login', [AuthController::class, 'login']);


Route::post('/reservations', [ReservationController::class, 'store']);
Route::post('/reservations/track', [ReservationController::class, 'track']);

/*
|--------------------------------------------------------------------------
| ROUTES PROTÉGÉES (Besoin d'un Token Bearer valide)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Auth & Profil
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Groupe ADMIN
    Route::prefix('admin')->group(function () {
        
        Route::get('/houseworkers/planning', [HouseworkerController::class, 'planning']);
        // pour créer automatiquement GET, POST, GET/{id}, PUT/{id}, DELETE/{id}
        Route::apiResource('houseworkers', HouseworkerController::class);

        Route::apiResource('services', ServiceController::class);


        // GESTION RÉSERVATIONS (Admin)
        Route::get('/reservations', [AdminReservationController::class, 'index']);
        Route::put('/reservations/{reservation}', [AdminReservationController::class, 'update']);
        Route::post('/reservations/{reservation}/assign', [AdminReservationController::class, 'assignTask']);

    });


});





Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
