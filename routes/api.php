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

Route::group([ 'prefix' =>'rest' ,  'as' => 'api.'] , function (){
    Route::get('platforms' , [ \App\Http\Controllers\Api\IndexController::class , 'platforms'])
        ->name('platforms');
    Route::get('platforms/{platform}/apps' , [ \App\Http\Controllers\Api\IndexController::class , 'apps'])
        ->name('apps');
    Route::get('platforms/{platform}/app/{app}/subscriptions' , [ \App\Http\Controllers\Api\IndexController::class , 'appSubscription'])
        ->name('apps.subscriptions');
    Route::get('rounds' , [ \App\Http\Controllers\Api\IndexController::class , 'rounds'])
        ->name('rounds');
    Route::get('rounds/last' , [ \App\Http\Controllers\Api\IndexController::class , 'lastRound'])
        ->name('rounds.last');
    Route::get('rounds/{run}/subscriptions' , [ \App\Http\Controllers\Api\IndexController::class , 'runSubscription'])
        ->name('rounds.subscriptions');
});
