<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id', 'ip_address', 'user_agent', 'referrer', 
        'channel', 'city', 'is_new_visitor', 'started_at', 
        'last_activity_at', 'page_views'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'is_new_visitor' => 'boolean',
    ];
}
