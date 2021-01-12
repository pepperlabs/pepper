<?php

declare(strict_types=1);

namespace Tests\Support\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property int $id
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection|Post[] $posts
 * @property-read \Illuminate\Database\Eloquent\Collection|Like[] $likes
 */
class User extends Authenticatable implements JWTSubject
{
    protected $fillable = ['name'];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class)->orderBy('posts.id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
