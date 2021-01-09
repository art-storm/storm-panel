<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    // Table name
    protected $table = 'roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_name', 'role_display',
    ];

    /**
     * The attributes that should be visible in arrays or JSON.
     *
     * @var array
     */
    protected $visible = ['id', 'role_name', 'role_display'];

    /**
     * Get additional roles record associated with the user.
     */
    public function permissions()
    {
        return $this->belongsToMany('App\Models\Permission', 'permission_role');
    }
}
