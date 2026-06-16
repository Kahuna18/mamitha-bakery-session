<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = ['name', 'rating', 'body', 'avatar', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'rating' => 'integer',
    ];
}
