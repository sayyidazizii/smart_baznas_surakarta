<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MustahikWorksheetItem extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $table        = 'mustahik_worksheet_item'; 
    protected $primaryKey   = 'worksheet_item_id';
    
    protected $guarded = [
        'worksheet_item_id',
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
