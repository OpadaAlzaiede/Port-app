<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PayloadRequestController;
use App\Http\Controllers\PayloadTypeController;
use App\Http\Controllers\PierController;
use App\Http\Controllers\TugboatController;
use App\Http\Controllers\UserController;
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

    Route::resource('payload-requests', PayloadRequestController::class);

    Route::resource('/piers', PierController::class);
    Route::resource('/tugboats', TugboatController::class);
});
