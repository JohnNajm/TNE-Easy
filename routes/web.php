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

Route::get('/home', function () {
    return view('welcome');
});

Route::get('/', function () {
    return view('welcome');
});
Auth::routes();

Route::get('/check', 'App\Http\Controllers\VacationsController@individual')->name('vacation.check');
Route::get('/apply', 'App\Http\Controllers\VacationsController@create')->name('vacation.create');
Route::post('/store', 'App\Http\Controllers\VacationsController@store')->name('vacation.store');
Route::get('/logout', '\App\Http\Controllers\Auth\LoginController@logout');



Route::get('/all', 'App\Http\Controllers\VacationsController@index')->middleware('adminMiddleware');
Route::get('/user/{id}', 'App\Http\Controllers\VacationsController@user')->middleware('adminMiddleware');
Route::patch('/request/{id}', 'App\Http\Controllers\VacationsController@update')->name('vacation.update')->middleware('adminMiddleware');

