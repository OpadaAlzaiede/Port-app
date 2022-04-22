<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EnterPortRequestController;
use App\Http\Controllers\PayloadRequestController;
use App\Http\Controllers\PayloadTypeController;
use App\Http\Controllers\PierController;
use App\Http\Controllers\ProcessTypeController;
use App\Http\Controllers\TugboatController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\YardController;
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

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('reset-password', [UserController::class, 'resetPassword']);


Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/admin/notifications', [AdminController::class, 'getNotifications']);

    Route::resource('/payload-types', PayloadTypeController::class);

    Route::post('/payload-requests/{id}/approve', [PayloadRequestController::class, 'approve']);
    Route::post('/payload-requests/{id}/refuse', [PayloadRequestController::class, 'refuse']);
    Route::post('/payload-requests/{id}/cancel', [PayloadRequestController::class, 'cancel']);
    Route::resource('payload-requests', PayloadRequestController::class);

    Route::resource('/piers', PierController::class);
    Route::resource('/tugboats', TugboatController::class);
    Route::resource('/yards', YardController::class);

    Route::resource('/process-types', ProcessTypeController::class);
    Route::resource('/enter-port-requests', EnterPortRequestController::class);
    Route::get('/get-stochastic', [AdminController::class, 'getStochastic']);
});
