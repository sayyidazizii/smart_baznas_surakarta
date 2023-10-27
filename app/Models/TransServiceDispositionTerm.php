<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransServiceDispositionTerm extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $table        = 'trans_service_disposition_term'; 
    protected $primaryKey   = 'service_disposition_term_id';
    
    protected $guarded = [
        'service_disposition_term_id',
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
