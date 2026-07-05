<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostMeta extends Model
{
    use HasFactory;

    protected $table = 'post_meta';

    protected $fillable = [
        'post_id',
        'meta_title',
        'meta_description',
        'og_image',
        'canonical_url',
        'schema_type',
    ];

    // ── Relationships ─────────────────────────────────────

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
