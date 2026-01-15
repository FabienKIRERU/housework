<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\HouseworkerController;



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

    // Groupe ADMIN
    Route::prefix('admin')->group(function () {        
        // pour créer automatiquement GET, POST, GET/{id}, PUT/{id}, DELETE/{id}
        Route::apiResource('houseworkers', HouseworkerController::class);

    });


});





Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
