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
Route::get('/concerts/{id}', 'ConcertsController@show')->name('concerts.show');

Route::post('/concerts/{id}/orders', 'ConcertOrdersController@store');

Route::get('/orders/{confirmationNumber}', 'OrdersController@show');

Route::get('/login', 'Auth\LoginController@loginForm');

Route::post('/auth', 'Auth\LoginController@login');

Route::group(['middleware' => 'auth', 'prefix' => 'backstage', 'namespace' => 'Backstage'], function () {
    Route::get('/concerts/new', 'ConcertsController@create');

    Route::get('/concerts', 'ConcertsController@index');
    Route::post('/concerts', 'ConcertsController@store');
});

Route::patch('/backstage/concerts/{id}', 'Backstage\ConcertsController@update')->name('backstage.concerts.update');