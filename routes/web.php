<?php

use Illuminate\Support\Facades\Auth;
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

Auth::routes();

Route::group(['namespace' => 'App\Http\Controllers'], function () {
    Route::get('/', 'ThreadsController@index')->name('home');
    Route::get("threads","ThreadsController@index");
    Route::get('threads/create','ThreadsController@create');
    Route::get('threads/{channel}/{thread}','ThreadsController@show');
    Route::post('threads','ThreadsController@store');
    Route::get("threads/{channel}","ThreadsController@index");
    Route::post('/threads/{channel}/{thread}/replies','RepliesController@store');
    Route::post("/replies/{reply}/favorites", "FavoritesController@store");

    Route::get("/profiles/{user}",'ProfilesController@show')->name('profile');
    Route::delete("threads/{channel}/{thread}","ThreadsController@destroy");
});

