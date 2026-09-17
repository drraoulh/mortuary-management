<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deceased extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'gender',
        'date_of_birth',
        'date_of_death',
        'cause_of_death',
        'admission_date',
        'release_date',
        'room_name',
        'room_type',
        'price',
        'security_key',
        'identifier',
        'longitude',
        'latitude',
        'location_address',
        'photo',
        'grave_location',
        'qr_code',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function schedule()
    {
        return $this->hasOne(Schedule::class);
    }

    public function funeralNotices()
    {
        return $this->hasMany(FuneralNotice::class);
    }
}
