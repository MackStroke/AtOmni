<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_posting_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'portfolio_url',
        'cover_letter',
        'resume_path',
        'status',
    ];

    public function jobPosting()
    {
        return $this->belongsTo(JobPosting::class);
    }
}
