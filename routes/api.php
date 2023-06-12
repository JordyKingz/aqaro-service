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
