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
        'name',
        'email',
        'password',
        'is_activate',
        'activation_code',
        'two_factor_state',
        'two_factor_method',
        'two_factor_code',
        'two_factor_expires_at'
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
        'two_factor_expires_at' => 'datetime',
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

    /**
     * Generete 2FA code
     */
    public function generateTwoFactorCode()
    {
        $this->timestamps = false;
        $this->two_factor_code = rand(100000, 999999);
        $this->two_factor_expires_at = now()->addMinutes(10);
        $this->unsetEventDispatcher();
        $this->save();
    }

    /**
     * Reset 2FA code
     */
    public function resetTwoFactorCode()
    {
        $this->timestamps = false;
        $this->two_factor_code = null;
        $this->two_factor_expires_at = null;
        $this->unsetEventDispatcher();
        $this->save();
    }
}
