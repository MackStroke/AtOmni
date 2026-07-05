<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPosting extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'department',
        'location',
        'type',
        'description',
        'requirements',
        'benefits',
        'status',
        'closing_date',
    ];

    protected $casts = [
        'closing_date' => 'datetime',
    ];

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    public function getDisplayStatusAttribute()
    {
        if ($this->status === 'closed' || ($this->closing_date && $this->closing_date->isPast())) {
            return 'closed';
        }

        // Handle legacy or mapped status names where DB holds 'active' instead of 'draft' or vice-versa
        if ($this->status === 'active') {
            return 'active'; // fallback
        }

        return $this->status;
    }
}
