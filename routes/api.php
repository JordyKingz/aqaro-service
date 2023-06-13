<?php

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
Route::group(['prefix' => 'ethereum'], function () {
    Route::get('/signature/{walletAddress}', [\App\Http\Controllers\Web3AuthController::class, 'signature'])
        ->name('metamask.signature');
    Route::post('/authenticate', [\App\Http\Controllers\Web3AuthController::class, 'authenticate'])
        ->name('metamask.authenticate');
});

Route::post('/subscribe', [App\Http\Controllers\SubscriptionController::class,'store']);

Route::middleware(['auth:sanctum', 'abilities:is-web3-auth'])->group(function () {
    Route::group(['prefix' => 'property'], function () {
        Route::post('create', [App\Http\Controllers\PropertyController::class, 'create']);
        Route::post('update', [App\Http\Controllers\PropertyController::class, 'update']);
        Route::get('get-all/{page}/{limit}', [App\Http\Controllers\PropertyController::class, 'getAll']);
        Route::get('get-by-id/{id}', [App\Http\Controllers\PropertyController::class, 'getById']);
    });
});
