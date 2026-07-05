<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'bio',
        'profile_image',
        'team_member_id',
        'two_factor_secret',
        'two_factor_enabled',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_enabled' => 'boolean',
        ];
    }

    // ── Relationships ─────────────────────────────────────

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    public function teamMember(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TeamMember::class);
    }

    // ── Helpers ────────────────────────────────────────────

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isEditor(): bool
    {
        return $this->role === 'editor';
    }

    public function isAuthor(): bool
    {
        return $this->role === 'author';
    }

    public function isContributor(): bool
    {
        return $this->role === 'contributor';
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    /** Returns the human-readable label for a role slug. */
    public static function roleLabel(string $role): string
    {
        return match($role) {
            'super_admin'  => 'Super Admin',
            'editor'       => 'Editor',
            'author'       => 'Author',
            'contributor'  => 'Contributor',
            default        => ucfirst($role),
        };
    }

    /**
     * Always return "Atomni Editorial Desk" instead of "Admin" for author name.
     */
    public function getNameAttribute($value): string
    {
        return $value === 'Admin' ? 'Atomni Editorial Desk' : $value;
    }
}
