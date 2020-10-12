<?php

namespace Pepper\Extra\Cache;

use Closure;
use Illuminate\Support\Facades\Cache as LaravelCache;
use Opis\Closure\SerializableClosure;

class Cache
{
    public static function putOrGet(
        $key,
        Closure $func,
        $ttl = null,
        $config = true,
        $response = false,
        $serialize = false
    ) {
        if (! config('pepper.cache.disabled') && LaravelCache::has($key)) {
            return self::get($key, $serialize);
        } else {
            return self::put($key, $func, $ttl, $config, $response, $serialize);
        }
    }

    protected static function put($key, $func, $ttl, $config, $response, $serialize)
    {
        // If the value set not to be cached.
        if (
            ! config('pepper.cache.config') && $config ||
            ! config('pepper.cache.response') && $response
        ) {
            return $func();
        }

        $value = $serialize ? new SerializableClosure($func) : $func();
        if (is_null($ttl)) {
            LaravelCache::forever($key, $value);
        } else {
            LaravelCache::put($key, $value, $ttl);
        }

        return $serialize ? $value->getClosure() : $value;
    }

    protected static function get($key, $serialize)
    {
        $cacheValue = LaravelCache::get($key);
        if ($serialize) {
            return $cacheValue->getClosure();
        } else {
            return $cacheValue;
        }
    }
}
