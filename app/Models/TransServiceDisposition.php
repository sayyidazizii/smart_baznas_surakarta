<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransServiceDisposition extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $table        = 'trans_service_disposition'; 
    protected $primaryKey   = 'service_disposition_id';
    
    protected $guarded = [
        'service_disposition_id',
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
