<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\ServerAPIController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('server')->group(function () {
    Route::get('/', [ServerAPIController::class, 'status'])->name('server.status');

    Route::get('/account/{id}', [ServerAPIController::class, 'getAccount'])->name('server.getAccount');
    Route::get('/accounts', [ServerAPIController::class, 'getAccounts'])->name('server.getAccounts');

    Route::get('/player/{id}', [ServerAPIController::class, 'getPlayer'])->name('server.getPlayer');
    Route::get('/players', [ServerAPIController::class, 'getPlayers'])->name('server.getPlayers');

    Route::get('/online', [ServerAPIController::class, 'getOnline'])->name('server.getOnline');
    Route::get('/territory', [ServerAPIController::class, 'getTerritory'])->name('server.getTerritory');
    Route::get('/faction/{id}', [ServerAPIController::class, 'getFaction'])->name('server.getFaction');

    
    
})->middleware(['auth', 'verified']);

