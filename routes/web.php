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
Route::get('/register_verify', 'Auth\RegisterController@registerVerify')->name('register.verify');
Route::get('/register_activate/{code}', 'Auth\RegisterController@activateUser')->name('register.activate');

Route::get('2fa/form', 'Auth\TwoFactorController@form')->name('2fa.form');
Route::post('2fa/check', 'Auth\TwoFactorController@check')->name('2fa.check');
Route::get('2fa/resend', 'Auth\TwoFactorController@resend')->name('2fa.resend');

// Users routes
Route::prefix('users')->group(function () {
    Route::get('/welcome', 'UserController@welcomePage')->name('users.welcome');
    Route::get('/profile', 'UserController@profile')->name('users.profile');
    Route::post('/profile', 'UserController@profileUpdate')->name('users.profile_update');
    Route::get('/password/change', 'Auth\ChangePasswordController@passwordChangeForm')->name('password.change.form');
    Route::post('/password/change', 'Auth\ChangePasswordController@passwordChange')->name('password.change');
    Route::get('/email/change', 'Auth\ChangeEmailController@emailChangeForm')->name('email.change.form');
    Route::post('/email/change', 'Auth\ChangeEmailController@sendEmailChangeLink')->name('email.change');
    Route::get('/email/change/notify', 'Auth\ChangeEmailController@notifyEmailChangeLink')->name('email.change.notify');
    Route::get('/email/change/confirm/{token}', 'Auth\ChangeEmailController@confirmEmailChange')
        ->name('email.change.confirm');
});
