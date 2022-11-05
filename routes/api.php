<?php

use App\Http\Controllers\AgriculturalMachineController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PropertyTypeController;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
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

    // Property Types
    Route::get('property-types', [PropertyTypeController::class, 'index']);
    Route::get('property-types/show/{id}', [PropertyTypeController::class, 'show']);
    Route::post('property-types/add', [PropertyTypeController::class, 'store']);
    Route::post('property-types/edit/{id}', [PropertyTypeController::class, 'update']);
    Route::delete('property-types/delete/{id}', [PropertyTypeController::class, 'destroy']);

    // Owners
    Route::get('owners', [OwnerController::class, 'index']);
    Route::get('owners/show/{id}', [OwnerController::class, 'show']);
    Route::post('owners/add', [OwnerController::class, 'store']);
    Route::post('owners/edit/{id}', [OwnerController::class, 'update']);
    Route::delete('owners/delete/{id}', [OwnerController::class, 'destroy']);

    // Vehicles
    Route::get('vehicles', [VehicleController::class, 'index']);
    Route::get('vehicles/show/{id}', [VehicleController::class, 'show']);
    Route::post('vehicles/add', [VehicleController::class, 'store']);
    Route::post('vehicles/edit/{id}', [VehicleController::class, 'update']);
    Route::delete('vehicles/delete/{id}', [VehicleController::class, 'destroy']);

    // Agricultural Machines
    Route::get('agricultural-machines', [AgriculturalMachineController::class, 'index']);
    Route::get('agricultural-machines/show/{id}', [AgriculturalMachineController::class, 'show']);
    Route::post('agricultural-machines/add', [AgriculturalMachineController::class, 'store']);
    Route::post('agricultural-machines/edit/{id}', [AgriculturalMachineController::class, 'update']);
    Route::delete('agricultural-machines/delete/{id}', [AgriculturalMachineController::class, 'destroy']);

    // Properties
    Route::get('properties', [PropertyController::class, 'index']);
    Route::get('properties/show/{id}', [PropertyController::class, 'show']);
    Route::post('properties/add', [PropertyController::class, 'store']);
    Route::post('properties/edit/{id}', [PropertyController::class, 'update']);
    Route::delete('properties/delete/{id}', [PropertyController::class, 'destroy']);

    // Sync Routes
    Route::any('sync/users', [SyncController::class, 'syncUsers']);
    Route::any('sync/owners', [SyncController::class, 'syncOwners']);
    Route::any('sync/property-types', [SyncController::class, 'syncPropertyTypes']);
    Route::any('sync/properties', [SyncController::class, 'syncProperties']);
});
