<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionRole extends Model
{
    // Table name
    protected $table = 'permission_role';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'permission_id', 'role_id',
    ];

    /**
     * The attributes that should be visible in arrays or JSON.
     *
     * @var array
     */
    protected $visible = ['id', 'permission_id', 'role_id'];
}
