<?php

namespace App\Providers;

use App\Models\Menu;
use App\Models\Role;
use App\Policies\Admin\MenuPolicy;
use App\Policies\Admin\RolePolicy;
use App\Policies\Admin\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Menu::class => MenuPolicy::class,
        Role::class => RolePolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        $this->registerAdminPolicies();
    }

    /**
     * Gates for admins area
     */
    public function registerAdminPolicies()
    {
        Gate::before(function ($user) {
            // Access for role admin_super
            $user_roles = $user->getRolesAll()->pluck('role_name')->toArray();
            if (in_array('admin_super', $user_roles)) {
                return true;
            }
        });

        // Login to admin area
        Gate::define('admin_login', function ($user) {
            return $user->hasPermission('admin_login');
        });
    }
}
