<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SportsFixture extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'event_id',
        'team_a_name', 'team_a_logo', 'team_a_score', 'team_a_abbrev',
        'team_b_name', 'team_b_logo', 'team_b_score', 'team_b_abbrev',
        'match_status', 'match_time', 'link'
    ];
    
    protected $casts = [
        'match_time' => 'datetime'
    ];
}
