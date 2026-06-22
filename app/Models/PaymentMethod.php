<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'customer_id',
        'type',
        'provider',
        'account_number',
        'account_name',
        'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
