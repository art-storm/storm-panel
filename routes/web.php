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
})->name('main');

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

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'twofactor'])->group(function () {
    Route::get('/', 'Admin\AdminController@dashboardAdmin')->name('dashboard');

    // Menus
    Route::resource('menus', 'Admin\MenuController')->parameters(['menus' => 'menu_id'])->only([
        'index', 'edit', 'update',
    ]);

    // MenuItems
    Route::get('/menus/menuitems/create/{menu_id}', 'Admin\MenuItemController@create')->name('menuitems.create');
    Route::get('/menus/menuitems/{item_id}/destroy', 'Admin\MenuItemController@destroy')->name('menuitems.destroy');
    Route::resource('menus/menuitems', 'Admin\MenuItemController')->parameters(['menuitems' => 'item_id'])->only([
        'store', 'edit', 'update',
    ]);

    // Users
    Route::get('/users/{user_id}/destroy', 'Admin\UserController@destroy')->name('users.destroy');
    Route::resource('users', 'Admin\UserController')->parameters(['users' => 'user_id'])->only([
        'index', 'show', 'create', 'store', 'edit', 'update',
    ]);

    // Roles
    Route::get('/roles/{role_id}/destroy', 'Admin\RoleController@destroy')->name('roles.destroy');
    Route::resource('roles', 'Admin\RoleController')->parameters(['roles' => 'role_id'])->only([
        'index', 'show', 'create', 'store', 'edit', 'update',
    ]);

    // Settings
    Route::get('/settings', 'Admin\SettingController')->name('settings');
});
