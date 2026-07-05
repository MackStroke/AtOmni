<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    protected $fillable = [
        'name',
        'role',
        'photo_path',
        'bio',
        'order_column',
        'is_active',
        'is_founding_member',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_founding_member' => 'boolean',
    ];
}
