<?php

namespace Pepper\Extra\Cache;

use Closure;
use Illuminate\Support\Facades\Cache as LaravelCache;
use Opis\Closure\SerializableClosure;

class Cache
{
    /**
     * put or get the cache value.
     *
     * @param  string  $key
     * @param  Closure  $func
     * @param  null|int  $ttl
     * @param  bool  $config
     * @param  bool  $response
     * @param  bool  $serialize
     * @return mixed
     */
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

    /**
     * Put new key into the cache, considering time to live, whether it should
     * cache config or response values and whether it the value is serilizable
     * or not.
     *
     * @param  string  $key
     * @param  Closure  $func
     * @param  null|int  $ttl
     * @param  bool  $config
     * @param  bool  $response
     * @param  bool  $serialize
     * @return mixed
     */
    protected static function put(
        string $key,
        Closure $func,
        $ttl,
        bool $config,
        bool $response,
        bool $serialize
    ) {
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

    /**
     * Get cache value considering if its serialized or not.
     *
     * @param  string  $key
     * @param  bool  $serialize
     * @return mixed
     */
    protected static function get(string $key, bool $serialize)
    {
        $cacheValue = LaravelCache::get($key);
        if ($serialize) {
            return $cacheValue->getClosure();
        } else {
            return $cacheValue;
        }
    }
}
