<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorageRoom extends Model
{
    protected $fillable = [
        'room_number',
        'capacity',
        'status',
    ];

    /**
     * Deceased whose "room" field matches this room's number.
     */
    public function deceaseds()
    {
        return $this->hasMany(Deceased::class, 'room_name', 'room_number');
    }
}
