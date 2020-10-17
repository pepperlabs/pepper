<?php

declare(strict_types=1);

namespace Tests;

use Pepper\PepperServiceProvider;
use Rebing\GraphQL\GraphQLServiceProvider;
use Tests\Support\Traits\SqlAssertionTrait;
use Tymon\JWTAuth\Providers\LaravelServiceProvider as JWTServiceProvider;

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

        if (file_exists(config_path('pepper.php'))) {
            $base = new \Illuminate\Config\Repository(include config_path('pepper.php'));
            config(['pepper' => $base->all()]);
            config(['pepper.namespace.models' => 'Tests\Support\Models']);
        }
    }

    protected function getPackageProviders($app): array
    {
        $providers = [
            GraphQLServiceProvider::class,
            JWTServiceProvider::class,
            PepperServiceProvider::class,
        ];

        return $providers;
    }

    protected function getPackageAliases($app): array
    {
        return [
            'GraphQL' => GraphQL::class,
        ];
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
    }
}
