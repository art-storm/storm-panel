<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    // Table name
    protected $table = 'permissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key', 'name', 'parent_id', 'order_id',
    ];

    /**
     * The attributes that should be visible in arrays or JSON.
     *
     * @var array
     */
    protected $visible = ['id', 'key', 'name', 'parent_id', 'order_id'];

    public function parent()
    {
        return $this->belongsTo('App\Models\Permission', 'parent_id');
    }

    public function children()
    {
        return $this->hasMany('App\Models\Permission', 'parent_id');
    }
}
