<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RelationCustomerSatisfaction extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $connection   = 'mysql2';
    protected $table        = 'relation_customer_satisfaction'; 
    protected $primaryKey   = 'customer_satisfaction_id';
    
    protected $guarded = [
        'customer_satisfaction_id',
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
