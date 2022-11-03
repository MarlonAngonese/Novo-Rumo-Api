<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisitController;
use Illuminate\Http\Request;
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

Route::post('auth/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['apiJwt']], function () {
    // Users
    Route::get('users', [UserController::class, 'index']);
    Route::get('users/show/{id}', [UserController::class, 'show']);
    Route::post('users/add', [UserController::class, 'store']);
    Route::post('users/edit/{id}', [UserController::class, 'update']);
    Route::delete('users/delete/{id}', [UserController::class, 'destroy']);

    // Visits
    Route::get('visits', [VisitController::class, 'index']);
    Route::get('visits/show/{id}', [VisitController::class, 'show']);
    Route::post('visits/add', [VisitController::class, 'store']);
    Route::post('visits/edit/{id}', [VisitController::class, 'update']);
    Route::delete('visits/delete/{id}', [VisitController::class, 'destroy']);

    Route::any('sync/users', [SyncController::class, 'syncUsers']);
    Route::any('sync/owners', [SyncController::class, 'syncOwners']);
    Route::any('sync/property-types', [SyncController::class, 'syncPropertyTypes']);
    Route::any('sync/properties', [SyncController::class, 'syncProperties']);
});
