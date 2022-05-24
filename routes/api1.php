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


    Route::resource('/enter-port-requests', EnterPortRequestController::class);
    Route::post('/enter-port-requests/{id}/approve', [EnterPortRequestController::class, 'approve']);
    Route::post('/enter-port-requests/{id}/refuse', [EnterPortRequestController::class, 'refuse']);
    Route::post('/enter-port-requests/{id}/cancel', [EnterPortRequestController::class, 'cancel']);
    Route::post('/port-piers/detach', [EnterPortPierController::class, 'detachEnterPortPier']);
    Route::get('/port-piers/index', [EnterPortPierController::class, 'index']);
});
