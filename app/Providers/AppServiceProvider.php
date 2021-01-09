<?php

namespace App\Providers;

use App\Models\MenuItem;
use App\Models\Role;
use App\Observers\MenuItemObserver;
use App\Observers\RoleObserver;
use App\Observers\UserObserver;
use App\User;
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
        $this->loadHelpers();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Custom validator
        Validator::extend('password_current', function ($attribute, $value, $parameters) {
            return Hash::check($value, Auth::user()->password);
        }, __('validation.password'));

        // Observers
        MenuItem::observe(MenuItemObserver::class);
        Role::observe(RoleObserver::class);
        User::observe(UserObserver::class);

        // Facades
        $this->app->bind('menu', 'App\Services\MenuService');
    }

    protected function loadHelpers()
    {
        require_once app_path() . '/Helpers/menu.php';
    }
}
