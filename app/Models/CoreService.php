<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoreService extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $table        = 'core_service'; 
    protected $primaryKey   = 'service_id';
    
    protected $guarded = [
        'service_id',
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
