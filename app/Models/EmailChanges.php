<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailChanges extends Model
{

    // Table name
    protected $table = 'email_changes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'change_code', 'email_new'
    ];

    /**
     * The attributes that should be visible in arrays or JSON.
     *
     * @var array
     */
    protected $visible = ['email', 'change_code'];

    public static function boot()
    {
        parent::boot();

        EmailChanges::creating(function ($emailChanges) {
            $emailChanges->created_ip = ip2long(\Request::ip());
        });
    }
}
