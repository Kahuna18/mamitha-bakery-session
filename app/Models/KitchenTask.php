<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitchenTask extends Model
{
    protected $fillable = ['order_id', 'user_id', 'status', 'notes', 'started_at', 'completed_at'];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
