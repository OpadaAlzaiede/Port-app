<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EnterPortRequestController;
use App\Http\Controllers\PayloadRequestController;
use App\Http\Controllers\PayloadTypeController;
use App\Http\Controllers\PierController;
use App\Http\Controllers\ProcessTypeController;
use App\Http\Controllers\TugboatController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\YardController;
use App\Models\User;
use Illuminate\Support\Facades\Config;
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


    Route::prefix('payload-requests')->group(function() {

        Route::get('/pendings', [PayloadRequestController::class, 'getPendings']);
        Route::get('/in-progress', [PayloadRequestController::class, 'getInProgress']);
        Route::get('/done', [PayloadRequestController::class, 'getDone']);
        Route::get('/canceled', [PayloadRequestController::class, 'getCanceled']);

        Route::middleware('role:'.Config::get('constants.roles.officer_role'))->group(function() {
            Route::post('/{id}/approve', [PayloadRequestController::class, 'approve'])->middleware('role');
            Route::post('/{id}/refuse', [PayloadRequestController::class, 'refuse']);
        });

        Route::middleware('role:'.Config::get('constants.roles.user_role'))->group(function() {
            Route::post('/{id}/cancel', [PayloadRequestController::class, 'cancel']);
        });

        Route::resource('', PayloadRequestController::class);
    });


    // Officer Only Routes
    Route::middleware('role:'.Config::get('constants.roles.officer_role'))->group(function() {
        Route::resource('/piers', PierController::class);
        Route::resource('/tugboats', TugboatController::class);
        Route::resource('/yards', YardController::class);
        Route::resource('/process-types', ProcessTypeController::class);
        Route::resource('/payload-types', PayloadTypeController::class);
    });

    // Admin only Routes
    Route::middleware('role:'.Config::get('constants.roles.admin_role'))->group(function() {
        Route::prefix('/admin')->group(function() {
            Route::get('/notifications', [AdminController::class, 'getNotifications']);
            Route::get('/get-stochastic', [AdminController::class, 'getStochastic']);
        });
    });


    Route::post('/logout', [AuthController::class, 'logout']);

    Route::resource('/enter-port-requests', EnterPortRequestController::class);

    Route::get('/get-stochastic', [AdminController::class, 'getStochastic']);
    Route::get('/get-audits', [AuditController::class, 'getAudits']);

});
