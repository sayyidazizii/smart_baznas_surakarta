<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MustahikWorksheetRequisition extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $table        = 'mustahik_worksheet_requisition'; 
    protected $primaryKey   = 'worksheet_requisition_id';
    
    protected $guarded = [
        'worksheet_requisition_id',
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
