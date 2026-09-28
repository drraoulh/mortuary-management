<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'deceased_id',
        'pickup_date',
        'pickup_time',
        'burial_date',
        'release_date',
        'location',
        'notes',
        'status',
    ];

    public function deceased()
    {
        return $this->belongsTo(Deceased::class);
    }
}
