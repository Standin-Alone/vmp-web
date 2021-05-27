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
Route::post('/api/get_voucher_info','MobileController@get_voucher_info');


// Home Screen
Route::post('/api/get-scanned-vouchers','MobileController@get_scanned_vouchers');

// Claim Voucher Screen
Route::post('/api/submit-voucher','MobileController@submit_voucher');

//OTP Screen
Route::post('/api/resend-otp','MobileController@resendOTP');

//  
Route::resource('api','MobileController');
Route::get('/otp','MobileController@otp');


