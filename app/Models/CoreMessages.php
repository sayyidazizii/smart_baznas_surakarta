<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoreMessages extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $table        = 'core_messages'; 
    protected $primaryKey   = 'messages_id';
    
    protected $guarded = [
        'messages_id',
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
