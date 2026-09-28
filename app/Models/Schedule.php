<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{protected $fillable = [
    'deceased_id',
    'pickup_date',
    'pickup_time',
    'notes',
];
}