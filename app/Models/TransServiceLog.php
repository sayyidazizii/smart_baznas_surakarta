<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransServiceLog extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $table        = 'trans_service_log'; 
    protected $primaryKey   = 'service_log_id';
    
    protected $guarded = [
        'service_log_id',
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
