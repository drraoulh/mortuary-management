<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuneralNotice extends Model
{
    protected $fillable = [

        'deceased_id',

        'announcement',

        'theme',

        'language',

        'pdf'

    ];

    public function deceased()
    {
        return $this->belongsTo(Deceased::class);
    }
}