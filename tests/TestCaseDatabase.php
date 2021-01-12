<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Pepper\PepperServiceProvider;
use Rebing\GraphQL\GraphQLServiceProvider;
use Tymon\JWTAuth\Providers\LaravelServiceProvider as JWTServiceProvider;

abstract class TestCaseDatabase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__.'/Support/database/migrations');
        $this->withFactories(__DIR__.'/Support/database/factories');

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
            config(['pepper.auth.disabled' => false]);
            config(['pepper.auth.model' => 'Tests\Support\Models\User']);
        }

        $this->copyPepperClass('Post');
        $this->copyModel('User');
    }

    protected function copyPepperClass($model) {
        // Cheesy 😬
        $content = file_get_contents(__DIR__."/Support/GraphQL/{$model}.php");
        $content = str_replace('Tests\Support\GraphQL', 'App\Http\Pepper', $content);
        $handle = fopen(__DIR__."/../vendor/orchestra/testbench-core/laravel/app/Http/Pepper/{$model}.php", 'r+');
        fwrite($handle, $content);
    }

    protected function copyModel($model) {
        // Cheesy 😬
        $content = file_get_contents(__DIR__."/Support/Models/{$model}.php");
        $content = str_replace('Tests\Support\Models', 'App\Models', $content);
        $handle = fopen(__DIR__."/../vendor/orchestra/testbench-core/laravel/app/Models/{$model}.php", 'r+');
        fwrite($handle, $content);
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
        return parent::setUpTraits();
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);
        $driver = env('DB_DRIVER', 'sqlite');

        if ($driver == 'sqlite') {
            $app['config']->set('database.default', $driver);
            $app['config']->set('database.connections.sqlite', [
                'driver' => $driver,
                'database' => env('DB_DATABASE', ':memory:'),
                'prefix' => env('DB_PREFIX', ''),
            ]);
        }

        if ($driver == 'mysql') {
            $app['config']->set('database.default', $driver);
            $app['config']->set('database.connections.mysql', [
                'driver' => $driver,
                'host' => env('DB_HOST', '127.0.0.1'),
                'database' => env('DB_DATABASE', 'pepper'),
                'prefix' => env('DB_PREFIX', ''),
                'port' => env('DB_PORT', '3306'),
                'username' => env('DB_USERNAME', 'root'),
                'password' => env('DB_PASSWORD', ''),
            ]);
        }

        if ($driver == 'pgsql') {
            $app['config']->set('database.default', $driver);
            $app['config']->set('database.connections.pgsql', [
                'driver' => $driver,
                'host' => env('DB_HOST', '127.0.0.1'),
                'database' => env('DB_DATABASE', 'pepper'),
                'prefix' => env('DB_PREFIX', ''),
                'port' => env('DB_PORT', '5432'),
                'username' => env('DB_USERNAME', 'root'),
                'password' => env('DB_PASSWORD', ''),
            ]);
        }

        $app['config']->set('graphql.schemas.default.middleware', 'pepper');

        $this->clearCache();
    }
}
