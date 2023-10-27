<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoreServiceStatus extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $table        = 'core_service_status'; 
    protected $primaryKey   = 'service_status_id';
    
    protected $guarded = [
        'service_status_id',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
    ];

}
