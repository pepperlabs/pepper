<?php

namespace Pepper\Extra\Cache;

use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Pepper\Extra\Cache\Listeners\ResetCache;

class CacheEventServiceProvider extends ServiceProvider
{
    protected $listen = [
        MigrationsEnded::class => [
            ResetCache::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
