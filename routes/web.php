<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['middleware'=>['auth']] , function() {
   Route::resource('platform' , \App\Http\Controllers\PlatformController::class)
        ->except(['view','destroy']);

   Route::resource('app' , \App\Http\Controllers\AppController::class)
        ->except(['view','destroy']);

   Route::get('runs' , [\App\Http\Controllers\RunController::class , 'index'])
       ->name('run.index');

   Route::get('app/{app}/subscriptions' , [\App\Http\Controllers\SubscriptionController::class , 'appIndex'])
       ->name('subscription.app.index');
   Route::get('runs/{run}/subscriptions' , [\App\Http\Controllers\SubscriptionController::class , 'runIndex'])
       ->name('subscription.run.index');
});
