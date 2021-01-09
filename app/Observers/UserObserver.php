<?php

namespace App\Observers;

use App\Models\UserRole;
use App\User;
use Illuminate\Http\Request;

class UserObserver
{
    /**
     * Handle the user "creating" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function creating(User $user)
    {
        $user->created_ip = ip2long(\Request::ip());
        $user->updated_ip = ip2long(\Request::ip());
        $user->created_by = (\Auth::check()) ? \Auth::user()->id : null;
        $user->updated_by = (\Auth::check()) ? \Auth::user()->id : null;
    }

    /**
     * Handle the user "updating" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function updating(User $user)
    {
        $user->updated_ip = ip2long(\Request::ip());
        $user->updated_by = (\Auth::check()) ? \Auth::user()->id : null;
    }

    /**
     * Handle the user "deleting" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function deleting(User $user)
    {
        // Delete additional roles for user to delete
        UserRole::where('user_id', $user->id)->delete();
    }
}
