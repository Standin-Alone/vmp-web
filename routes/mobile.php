<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mobile Routes
|--------------------------------------------------------------------------
|
| Here is where you can register mobile routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "mobile" middleware group. Now create something great!
|
*/

Route::group(['middleware' => 'mobile'],function(){

    Route::post('/api/sign_in','MobileController@sign_in');
    Route::resource('api','MobileController');
});

