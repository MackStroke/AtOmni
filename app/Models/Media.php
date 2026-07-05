<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::deleting(function ($media) {
            if (\Illuminate\Support\Str::contains($media->file_path, 'placeholder')) {
                return false; // Prevent deleting system placeholders
            }

            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($media->file_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($media->file_path);
            }
            if ($media->webp_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($media->webp_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($media->webp_path);
            }
        });
    }

    protected $fillable = [
        'user_id',
        'file_path',
        'file_name',
        'alt_text',
        'mime_type',
        'size_kb',
        'width',
        'height',
        'webp_path',
    ];

    protected $appends = ['url'];

    protected function casts(): array
    {
        return [
            'size_kb' => 'integer',
        ];
    }

    // ── Relationships ─────────────────────────────────────

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ── Helpers ────────────────────────────────────────────

    public function hasWebp(): bool
    {
        return !is_null($this->webp_path);
    }

    /**
     * Returns the WebP path if available, otherwise the original file path.
     */
    public function optimizedPath(): string
    {
        return $this->webp_path ?? $this->file_path;
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->optimizedPath());
    }
}
