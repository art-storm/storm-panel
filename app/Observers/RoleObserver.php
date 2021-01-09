<?php

namespace App\Observers;

use App\Models\PermissionRole;
use App\Models\Role;
use App\Models\UserRole;
use App\User;
use Illuminate\Http\Request;

class RoleObserver
{
    /**
     * Handle the role "creating" event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function creating(Role $role)
    {
        $role->created_ip = ip2long(\Request::ip());
        $role->updated_ip = ip2long(\Request::ip());
        $role->created_by = \Auth::user()->id;
        $role->updated_by = \Auth::user()->id;
    }

    /**
     * Handle the role "updating" event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function updating(Role $role)
    {
        $role->updated_ip = ip2long(\Request::ip());
        $role->updated_by = \Auth::user()->id;
    }

    /**
     * Handle the role "deleting" event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function deleting(Role $role)
    {
        // Set default role 'user_registered' for all users who had a role to delete
        User::where('role_id', $role->id)
            ->update(['role_id' => 2]);

        // Delete role for users who had an additional role to delete
        UserRole::where('role_id', $role->id)->delete();

        // Delete permissions for role
        PermissionRole::where('role_id', $role->id)->delete();
    }
}
