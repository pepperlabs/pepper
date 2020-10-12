<?php

namespace Pepper\Extra\Cache;

use Closure;
use Illuminate\Support\Facades\Cache as LaravelCache;
use Opis\Closure\SerializableClosure;

class Cache
{
    public static function get($key, Closure $func, $ttl = null)
    {
        if (! config('pepper.extra.cache.disabled') && LaravelCache::has($key)) {
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

    public static function serialize($key, Closure $func, $ttl = null)
    {
        if (! config('pepper.extra.cache.disabled') && LaravelCache::has($key)) {
            return LaravelCache::get($key)->getClosure();
        } else {
            $wrapper = new SerializableClosure($func);
            if (is_null($ttl)) {
                LaravelCache::forever($key, $wrapper);
            } else {
                LaravelCache::put($key, $wrapper, $ttl);
            }

            return $wrapper->getClosure();
        }
    }
}
