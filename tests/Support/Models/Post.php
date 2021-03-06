<?php

namespace Tests\Support\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property int $id
 * @property string $title
 * @property string|null $body
 * @property int|null $user_id
 * @property array|null $properties
 * @property bool $flag
 * @property Illuminate\Support\Carbon|null $published_at
 * @property bool $is_published
 * @property-read \Illuminate\Database\Eloquent\Collection|Comment[] $comments
 * @property-read \Illuminate\Database\Eloquent\Collection|Like[] $likes
 */
class Post extends Model
{
    /** @var string[] */
    protected $dates = [
        'published_at',
    ];

    /** @var array<string,string> */
    protected $casts = [
        'properties' => 'array',
    ];

    protected $fillable = [
        'title',
        'cover',
        'content',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->orderBy('comments.id');
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likable');
    }

    public function getIsPublishedAttribute(): bool
    {
        $publishedAt = $this->published_at;

        return $publishedAt !== null;
    }

    public function setCoverAttribute($file)
    {
        $this->attributes['cover'] = $file->store('posts');
    }
}
