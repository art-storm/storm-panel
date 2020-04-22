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
    return view('main');
});

Auth::routes();
Route::get('/register_verify', 'Auth\RegisterController@registerVerify')->name('register_verify');
Route::get('/user_activate/{code}', 'Auth\RegisterController@activateUser')->name('activate.user');

// Users routes
Route::group(['prefix' => 'users'], function () {
    Route::get('/welcome', 'UserController@welcomePage')->name('users_welcome');
    Route::get('/profile', 'UserController@profile')->name('users_profile');
    Route::post('/profile', 'UserController@profileUpdate')->name('users_profile_update');
    Route::get('/password/change', 'Auth\ChangePasswordController@passwordChangeForm')->name('password_changeForm');
    Route::post('/password/change', 'Auth\ChangePasswordController@passwordChange')->name('password_change');
    Route::get('/email/change', 'Auth\ChangeEmailController@emailChangeForm')->name('email_changeForm');
    Route::post('/email/change', 'Auth\ChangeEmailController@sendEmailChangeLink')->name('email_change');
    Route::get('/email/change/notify', 'Auth\ChangeEmailController@notifyEmailChangeLink')->name('email_change_notify');
    Route::get('/email/change/confirm/{token}', 'Auth\ChangeEmailController@confirmEmailChange')->name('email_change_confirm');
});
