<?php

namespace Pepper;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Pepper\Console\HttpMakeCommand;
use Pepper\Console\PepperGrindCommand;

class PepperServiceProvider extends ServiceProvider
{
    /**
     * Boot package.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/pepper.php' => config_path('pepper.php'),
        ], 'config');

        $this->registerMiddleware('pepper', Middleware::class);
    }

    /**
     * Register package and its commands.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerPepper();

        if ($this->app->runningInConsole()) {
            $this->registerConsole();
        }
    }

    /**
     * Register Pepper.
     *
     * @return void
     */
    public function registerPepper(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/pepper.php', 'pepper');

        $this->app->register(\Tymon\JWTAuth\Providers\LaravelServiceProvider::class);
        $this->app->register(\Rebing\GraphQL\GraphQLServiceProvider::class);
    }

    /**
     * Register commands.
     *
     * @return void
     */
    public function registerConsole(): void
    {
        $this->commands(HttpMakeCommand::class);
        $this->commands(PepperGrindCommand::class);
    }

    protected function registerMiddleware($name, $middleware)
    {
        $kernel = $this->app[Router::class];
        $kernel->aliasMiddleware($name, $middleware);
    }
}
