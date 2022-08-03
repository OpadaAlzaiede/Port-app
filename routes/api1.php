<?php

use App\Http\Controllers\EnterPortPierController;
use App\Http\Controllers\EnterPortRequestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['middleware' => 'auth:sanctum'], function () {


    Route::post('/port-piers/detach', [EnterPortPierController::class, 'detachEnterPortPier']);
    Route::get('/port-piers/index', [EnterPortPierController::class, 'index']);

    Route::prefix('enter-port-requests')->group(function () {

        Route::post('/{id}/approve', [EnterPortRequestController::class, 'approve']);
        Route::post('/{id}/refuse', [EnterPortRequestController::class, 'refuse']);
        Route::post('/{id}/cancel', [EnterPortRequestController::class, 'cancel']);

        Route::get('/pending', [EnterPortRequestController::class, 'getPending']);
        Route::get('/in-progress', [EnterPortRequestController::class, 'getInProgress']);
        Route::get('/done', [EnterPortRequestController::class, 'getDone']);
        Route::get('/canceled', [EnterPortRequestController::class, 'getCanceled']);


    });

    Route::resource('/enter-port-requests', EnterPortRequestController::class);
});
