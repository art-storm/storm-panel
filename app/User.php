<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    // Table name
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'is_activate', 'activation_code',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        User::creating(function ($user) {
            $user->created_ip = ip2long(\Request::ip());
            $user->updated_ip = ip2long(\Request::ip());
            $user->created_by = (\Auth::check()) ? \Auth::user()->id : null;
            $user->updated_by = (\Auth::check()) ? \Auth::user()->id : null;
        });

        User::updating(function ($user) {
            $user->updated_ip = ip2long(\Request::ip());
            $user->updated_by = (\Auth::check()) ? \Auth::user()->id : null;
        });
    }
}
