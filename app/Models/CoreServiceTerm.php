<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoreServiceTerm extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $table        = 'core_service_term'; 
    protected $primaryKey   = 'service_term_id';
    
    protected $guarded = [
        'service_term_id',
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
