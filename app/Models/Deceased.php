<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Deceased extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'gender',
        'date_of_death',
        'cause_of_death',
        'admission_date',
        'security key',
        'longitude',
        'latitude',
        'location_address',

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



