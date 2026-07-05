<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    protected $fillable = [
        'name',
        'amount',
        'message',
        'social_link',
        'image_path',
        'is_active',
        'sort_order',
        'donated_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'donated_at' => 'datetime',
        'amount' => 'decimal:2',
    ];
}
