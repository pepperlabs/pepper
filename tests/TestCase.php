<?php

namespace Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function getEnvironmentSetUp($app)
    {
        if (env('TESTS_ENABLE_LAZYLOAD_TYPES') === '1') {
            $app['config']->set('graphql.lazyload_types', true);
        }

        $app['config']->set('graphql.schemas.default', [
            'query' => [],
            'mutation' => [],
        ]);

        $app['config']->set('graphql.types', []);

        $app['config']->set('app.debug', true);
    }

    /**
     * Clears Laravel Cache.
     */
    protected function clearCache()
    {
        $commands = ['clear-compiled', 'cache:clear', 'view:clear', 'config:clear', 'route:clear'];
        foreach ($commands as $command) {
            \Illuminate\Support\Facades\Artisan::call($command);
        }
    }
}
