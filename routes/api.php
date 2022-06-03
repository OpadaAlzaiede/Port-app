<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EnterPortRequestController;
use App\Http\Controllers\PayloadRequestController;
use App\Http\Controllers\PayloadTypeController;
use App\Http\Controllers\PierController;
use App\Http\Controllers\PierYardController;
use App\Http\Controllers\ProcessTypeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TugboatController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\YardController;
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
Route::get('/roles', [RoleController::class, 'getRoles']);


Route::group(['middleware' => 'auth:sanctum'], function () {


    Route::get('/notifications', [UserController::class, 'getNotifications']);

    Route::prefix('payload-requests')->group(function () {

        Route::get('/pendings', [PayloadRequestController::class, 'getPendings']);
        Route::get('/in-progress', [PayloadRequestController::class, 'getInProgress']);
        Route::get('/done', [PayloadRequestController::class, 'getDone']);
        Route::get('/canceled', [PayloadRequestController::class, 'getCanceled']);

        Route::middleware('role:' . Config::get('constants.roles.pier_officer_role'))->group(function () {
            Route::post('/{id}/approve', [PayloadRequestController::class, 'approve']);
            Route::post('/{id}/refuse', [PayloadRequestController::class, 'refuse']);
        });

        Route::middleware('role:' . Config::get('constants.roles.merchant_role'))->group(function () {
            Route::post('/{id}/cancel', [PayloadRequestController::class, 'cancel']);
            Route::put('/{requestObject}', [PayloadRequestController::class, 'update'])->middleware('update');
            Route::post('', [PayloadRequestController::class, 'store']);
        });
        Route::get('', [PayloadRequestController::class, 'index']);
        Route::get('/{id}', [PayloadRequestController::class, 'show']);
        Route::delete('/{id}', [PayloadRequestController::class, 'destroy']);
    });

    Route::resource('/process-types', ProcessTypeController::class)->only(['index', 'show']);
    Route::resource('/payload-types', PayloadTypeController::class)->only(['index', 'show']);

    // Officer Only Routes

    Route::middleware('role:' . Config::get('constants.roles.yard_officer_role'))->group(function () {
        Route::resource('/yards', YardController::class);
    });
    Route::middleware('role:' . Config::get('constants.roles.tugboat_officer_role'))->group(function () {
        Route::resource('/tugboats', TugboatController::class);
    });

    Route::middleware('role:' . Config::get('constants.roles.pier_officer_role'))->group(function () {
        Route::resource('/piers', PierController::class);
        Route::post('/pier/distance-from-yards', [PierController::class, 'addDistanceBetweenPierAndYards']);
        Route::post('/yard/distance-from-piers', [YardController::class, 'addDistanceBetweenYardsAndPier']);
        Route::resource('/process-types', ProcessTypeController::class)->except(['index', 'show']);
        Route::resource('/payload-types', PayloadTypeController::class)->except(['index', 'show']);
        Route::resource('/pier-yard', PierYardController::class)->except(['store', 'show']);
    });

    // Admin only Routes
    Route::middleware('role:' . Config::get('constants.roles.admin_role') . '|'
        . Config::get('constants.roles.pier_officer_role')
        . '|' . Config::get('constants.roles.tugboat_officer_role')
        . '|' . Config::get('constants.roles.yard_officer_role'))
        ->group(function () {
            Route::prefix('/admin')->group(function () {
                Route::get('/notifications', [AdminController::class, 'getNotifications']);
                Route::get('/get-stochastic', [AdminController::class, 'getStochastic']);
                Route::get('/get-audits', [AuditController::class, 'getAudits']);
            });
        });


    Route::post('/logout', [AuthController::class, 'logout']);

    Route::resource('/enter-port-requests', EnterPortRequestController::class);
});
