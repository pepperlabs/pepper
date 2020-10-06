<?php

namespace Pepper\Extra\Cache;

use Closure;
use Illuminate\Support\Facades\Cache as LaravelCache;

class Cache
{
    public static function get($key, Closure $func, $ttl = null)
    {
        if (config('pepper.base.extra.cache') && LaravelCache::has($key)) {
            return LaravelCache::get($key);
        } else {
            $value = $func();
            if (is_null($ttl)) {
                LaravelCache::forever($key, $value);
            } else {
                LaravelCache::put($key, $value, $ttl);
            }
            return $value;
        }
    }
}
