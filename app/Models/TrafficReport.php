<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrafficReport extends Model
{
    protected $fillable = [
        'report_date',
        'page_views',
        'unique_visitors',
        'data_consumed_mb',
    ];

    protected $casts = [
        'report_date' => 'date',
    ];
}
