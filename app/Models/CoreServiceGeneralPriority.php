<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoreServiceGeneralPriority extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $table        = 'core_service_general_priority'; 
    protected $primaryKey   = 'service_general_priority_id';
    
    protected $guarded = [
        'service_general_priority_id',
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
