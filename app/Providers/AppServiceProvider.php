<?php

namespace App\Providers;

use Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('password_current', function ($attribute, $value, $parameters) {
            return Hash::check($value, Auth::user()->password);
        }, __('validation.password'));
    }
}
