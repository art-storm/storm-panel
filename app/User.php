<?php

namespace App;

use App\Traits\UserAuthorize;
use App\Traits\UserTwoFactor;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Pagination\Paginator;

class User extends Authenticatable
{
    use Notifiable;
    use UserAuthorize;
    use UserTwoFactor;

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
        'role_id',
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

    /**
     * Default items per page
     * @var int
     */
    protected $perPage = 50;

    /**
     * Get the role record associated with the user.
     */
    public function role()
    {
        return $this->hasOne('App\Models\Role', 'id', 'role_id');
    }

    /**
     * Get additional roles record associated with the user.
     */
    public function additionalRoles()
    {
        return $this->belongsToMany('App\Models\Role', 'user_role');
    }

    /**
     * Scope a query filtering users by email.
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param string $emailSearch
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEmailFilter($query, $emailSearch)
    {
        return $query->where('email', 'like', $emailSearch);
    }

    /**
     * Scope a query filtering users by role.
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param int $role_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRoleFilter($query, $role_id)
    {
        $query = $query->where('role_id', $role_id);
        $query = $query->orWhereHas('additionalRoles', function ($q) use ($role_id) {
            $q->where('role_id', $role_id);
        });

        return $query;
    }
}
