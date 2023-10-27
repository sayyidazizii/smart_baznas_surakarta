<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransServiceZis extends Model
{
    protected $table        = 'trans_service_zis'; 
    protected $primaryKey   = 'service_zis_id';
    
    protected $guarded = [
        'service_zis_id',
        'created_at',
        'updated_at',
    ];

}
