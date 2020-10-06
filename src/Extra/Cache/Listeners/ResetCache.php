<?php

namespace Pepper\Extra\Cache\Listeners;

use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Support\Facades\Artisan;

class ResetCache
{
    public function handle(MigrationsEnded $event): void
    {
        Artisan::call('cache:clear');
    }
}
