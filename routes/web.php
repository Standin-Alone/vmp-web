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
Route::get('/api/get-scanned-vouchers/{supplier_id}','MobileController@get_scanned_vouchers');

// Attachment Screen Claim Voucher (RRP)
Route::post('/api/submit-voucher-rrp','MobileController@submit_voucher_rrp');

//OTP Screen
Route::post('/api/resend-otp','MobileController@resendOTP');

Route::get('/api/get-items','MobileController@getProgramItems');
//  
Route::resource('api','MobileController');
Route::get('/otp','MobileController@otp');



