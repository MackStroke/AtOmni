<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomepageSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'layout_type',
        'category_id',
        'tag_id',
        'post_limit',
        'order',
        'is_active',
        'filters',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'filters' => 'array',
    ];

    // ── Relationships ─────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }

    // ── Helpers ────────────────────────────────────────────

    /**
     * Get the posts for this section based on its configuration.
     */
    public function getPosts()
    {
        $query = Post::published()->latest('published_at');

        if ($this->category_id) {
            $query->where('category_id', $this->category_id);
        }

        if ($this->tag_id) {
            $query->whereHas('tags', function ($q) {
                $q->where('tags.id', $this->tag_id);
            });
        }

        if (!empty($this->filters['tag_ids'])) {
            $query->whereHas('tags', function ($q) {
                $q->whereIn('tags.id', $this->filters['tag_ids']);
            });
        }

        // Apply advanced filters if they exist
        if (is_array($this->filters)) {
            if (!empty($this->filters['is_featured'])) {
                $query->featured();
            }
            if (!empty($this->filters['is_sponsored'])) {
                $query->sponsored();
            }
            if (!empty($this->filters['trending_only'])) {
                $query->trending();
            }
        }

        return $query->take($this->post_limit ?? 6)->get();
    }
}
