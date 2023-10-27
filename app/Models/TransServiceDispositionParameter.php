<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransServiceDispositionParameter extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $table        = 'trans_service_disposition_parameter'; 
    protected $primaryKey   = 'service_disposition_parameter_id';
    
    protected $guarded = [
        'service_disposition_parameter_id',
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
