<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
       
    'user_id',
    'deceased_id',
    'amount',
    'balance',
    'payment_date',
    'receipt_number',
    'payment_method',
    'mobile_operator',
    'phone_number',
    'payer_name',
    'status',
    'confirmed',
    'confirmed_at',
    'campay_reference',
    'campay_operator_reference',
    'campay_status',
    'receipt_pdf',
    'qr_code',

];
    

    protected $casts = [
        'payment_date' => 'date',
        'confirmed_at' => 'datetime',
        'confirmed' => 'boolean',
        'amount' => 'decimal:2',
        'balance' => 'decimal:2',
       
];
    

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deceased()
    {
        return $this->belongsTo(Deceased::class);
    }

    /**
     * Payments that brought in money: confirmed by an admin/manager or reported successful by CamPay.
     */
    public function scopePaid($query)
    {
        return $query->whereIn('status', ['confirmed', 'successful']);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}