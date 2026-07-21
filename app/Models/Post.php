<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'author_id',
        'category_id',
        'title',
        'slug',
        'content',
        'excerpt',
        'tldr',
        'faqs',
        'featured_image',
        'status',
        'is_featured',
        'featured_until',
        'is_sponsored',
        'views_count',
        'trending_score',
        'seo_score',
        'aeo_score',
        'geo_score',
        'reading_time',
        'published_at',
        'kill_switch',
        'redirect_url',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_sponsored' => 'boolean',
            'views_count' => 'integer',
            'trending_score' => 'decimal:4',
            'seo_score' => 'integer',
            'aeo_score' => 'integer',
            'geo_score' => 'integer',
            'reading_time' => 'integer',
            'published_at' => 'datetime',
            'featured_until' => 'datetime',
            'kill_switch' => 'boolean',
            'faqs' => 'array',
        ];
    }

    // ── Relationships ─────────────────────────────────────

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function meta(): HasOne
    {
        return $this->hasOne(PostMeta::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function approvedComments(): HasMany
    {
        return $this->hasMany(Comment::class)->where('is_approved', true);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tag')->withTimestamps();
    }

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'location_post')->withTimestamps();
    }

    public function views(): HasMany
    {
        return $this->hasMany(PostView::class);
    }

    // ── Accessors ─────────────────────────────────────────

    /**
     * Get the excerpt cleaned of internal tags like [RSS: ...].
     */
    public function getCleanExcerptAttribute(): string
    {
        if (empty($this->excerpt)) return '';
        return trim(preg_replace('/\[RSS:[^\]]+\]/i', '', $this->excerpt));
    }

    // ── Scopes ────────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query->where(function ($q) {
                $q->where('status', 'published')
                  ->orWhere('status', 'scheduled');
            })
            ->where('published_at', '<=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
            ->where(function ($q) {
                $q->whereNull('featured_until')
                  ->orWhere('featured_until', '>', now());
            });
    }

    public function scopeTrending($query)
    {
        return $query->orderByDesc('trending_score');
    }

    public function scopeSponsored($query)
    {
        return $query->where('is_sponsored', true);
    }

    // ── Helpers ────────────────────────────────────────────

    /**
     * Get the featured image URL, or a placeholder if none set.
     */
    public function featuredImageUrl(): string
    {
        if ($this->featured_image) {
            if (str_starts_with($this->featured_image, 'http')) {
                return $this->featured_image;
            }
            
            // Clean up the path
            $path = ltrim($this->featured_image, '/');
            if (str_starts_with($path, 'storage/')) {
                $path = substr($path, 8);
            } elseif (str_starts_with($path, 'public/')) {
                $path = substr($path, 7);
            }

            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                return asset('storage/' . $path);
            }
            
            // Fallback: assume it's a relative public path if it doesn't exist in Storage
            return asset($this->featured_image);
        }
        return asset('images/atomni-placeholder.svg');
    }

    /**
     * Calculate estimated reading time in minutes.
     * Average reading speed: ~200 words per minute.
     */
    public static function calculateReadingTime(string $content): int
    {
        $wordCount = str_word_count(strip_tags($content));
        return max(1, (int) ceil($wordCount / 200));
    }

    /**
     * Calculate a dynamic SEO score (0-100) based on content heuristics.
     */
    public function getSeoScoreAttribute(): int
    {
        $score = 0;
        
        $titleLen = strlen($this->title);
        if ($titleLen >= 40 && $titleLen <= 60) $score += 20;
        elseif ($titleLen > 10 && $titleLen < 90) $score += 10;
        
        $wordCount = str_word_count(strip_tags($this->content));
        if ($wordCount >= 800) $score += 30;
        elseif ($wordCount >= 300) $score += 15;
        
        if (!empty($this->excerpt)) $score += 15;
        if (!empty($this->featured_image)) $score += 10;
        if (!empty($this->category_id)) $score += 10;
        if (preg_match('/<h[2-3][^>]*>/i', $this->content)) $score += 15;
        
        return min(100, $score);
    }

    /**
     * Calculate a dynamic AEO (Answer Engine Optimization) score (0-100).
     */
    public function getAeoScoreAttribute(): int
    {
        $score = 0;
        
        if (preg_match('/<h[2-4][^>]*>.*?\?.*?<\/h[2-4]>/i', $this->content)) $score += 30;
        if (preg_match('/<(ul|ol)[^>]*>/i', $this->content)) $score += 20;
        
        $paragraphs = explode('</p>', $this->content);
        $shortParagraphs = 0;
        foreach ($paragraphs as $p) {
            $words = str_word_count(strip_tags($p));
            if ($words > 10 && $words < 50) $shortParagraphs++;
        }
        if ($shortParagraphs >= 3) $score += 20;
        
        if (preg_match('/<p[^>]*>\s*(Yes|No|The answer is|Here is how|In short|Briefly).*?<\/p>/i', $this->content)) $score += 15;
        if (preg_match('/<table[^>]*>/i', $this->content)) $score += 15;
        
        $wordCount = str_word_count(strip_tags($this->content));
        if ($wordCount >= 300 && $wordCount <= 1200) $score += 10;

        return min(100, $score);
    }
}
