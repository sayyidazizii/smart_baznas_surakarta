<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransServiceZisItem extends Model
{
    protected $table        = 'trans_service_zis_item'; 
    protected $primaryKey   = 'service_zis_item_id';
    
    protected $guarded = [
        'service_zis_item_id',
        'created_at',
        'updated_at',
    ];
}
