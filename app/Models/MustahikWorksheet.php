<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MustahikWorksheet extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $table        = 'mustahik_worksheet'; 
    protected $primaryKey   = 'worksheet_id';
    
    protected $guarded = [
        'worksheet_id',
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
