<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransServiceRequisition extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $table        = 'trans_service_requisition'; 
    protected $primaryKey   = 'service_requisition_id';
    
    protected $guarded = [
        'service_requisition_id',
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
