<?php

declare(strict_types=1);

namespace Tests;

use Tests\Support\Traits\SqlAssertionTrait;

abstract class TestCaseDatabase extends TestCase
{
    use SqlAssertionTrait;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__.'/Support/database/migrations');
        $this->withFactories(__DIR__.'/Support/database/factories');

        // This takes care of refreshing the database between tests
        // as we are using the in-memory SQLite db we do not need RefreshDatabase
        $this->artisan('migrate');

        $this->artisan('pepper:grind', [
            '--all' => true,
        ]);

        $this->clearCache();

        if (file_exists(config_path('graphql.php'))) {
            $config = new \Illuminate\Config\Repository(include config_path('graphql.php'));
            config(['graphql' => $config->all()]);
        }

        if (file_exists(config_path('pepper/base.php'))) {
            $base = new \Illuminate\Config\Repository(include config_path('pepper/base.php'));
            config(['pepper.base' => $base->all()]);
            $auth = new \Illuminate\Config\Repository(include config_path('pepper/auth.php'));
            config(['pepper.auth' => $auth->all()]);
            $cache = new \Illuminate\Config\Repository(include config_path('pepper/cache.php'));
            config(['pepper.cache' => $cache->all()]);
            config(['pepper.base.namespace.models' => 'Tests\Support\Models']);
        }
    }

    protected function setUpTraits()
    {
        $uses = parent::setUpTraits();

        if (isset($uses[SqlAssertionTrait::class])) {
            $this->setupSqlAssertionTrait();
        }

        return $uses;
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $app['config']->set('graphql.schemas.default.middleware', 'pepper');

        // $this->artisan('vendor:publish', ['--provider' => 'Rebing\GraphQL\GraphQLServiceProvider']);
        // $this->artisan('vendor:publish', ['--provider' => 'Pepper\PepperServiceProvider']);
    }
}
