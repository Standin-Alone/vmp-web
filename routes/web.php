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


// Login Screen
Route::post('/api/sign_in','MobileController@sign_in');

// QRCode Screen
Route::get('/api/get_voucher_info','MobileController@get_voucher_info');


//  
Route::resource('api','MobileController');
Route::get('/otp','MobileController@otp');


