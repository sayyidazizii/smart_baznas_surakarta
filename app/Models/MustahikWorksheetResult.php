<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MustahikWorksheetResult extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $table        = 'mustahik_worksheet_result'; 
    protected $primaryKey   = 'worksheet_result_id';
    
    protected $guarded = [
        'worksheet_result_id',
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
