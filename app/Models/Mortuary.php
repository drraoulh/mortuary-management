<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mortuary extends Model
{
    protected $fillable = [
        'name',
        'city',
        'quarter',
        'address',
        'latitude',
        'longitude',
        'description',
        'phone',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'is_active' => 'boolean',
    ];
}