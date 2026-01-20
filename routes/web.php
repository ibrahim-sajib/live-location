<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/map', [MapController::class, 'index']);

// API routes for map functionality
Route::prefix('api')->group(function () {
    Route::get('/locations', [MapController::class, 'getLocations']);
    Route::post('/locations', [MapController::class, 'storeLocation']);
    Route::post('/live-location', [MapController::class, 'updateLiveLocation']);
    Route::get('/live-locations', [MapController::class, 'getLiveLocations']);
    Route::delete('/locations/{id}', [MapController::class, 'deleteLocation']);
});