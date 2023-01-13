<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AvailableReservationDatetime extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'available_time',
        'available_date',
        'fee_id',
    ];
}
