<?php

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
    return 'Laravel';
});

Route::get('/concerts/{id}', 'ConcertsController@show');

Route::post('/concerts/{id}/orders', 'ConcertOrdersController@store');

Route::get('/orders/{confirmationNumber}', 'OrdersController@show');

Route::get('/login', 'Auth\LoginController@loginForm');

Route::post('/auth', 'Auth\LoginController@login');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/backstage/concerts/new', 'Backstage\ConcertsController@create');
});