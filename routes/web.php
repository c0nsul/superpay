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

Route::get('payments/new', 'App\Http\Controllers\PaymentsController@create')
    ->name('payments.create')
    ->middleware('auth');

Route::post('payments', 'App\Http\Controllers\PaymentsController@store')
    ->name('payments.store')
    ->middleware('auth');

Route::get('login', 'App\Http\Controllers\Auth\LoginController@showLoginForm')->name('login');

//Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
