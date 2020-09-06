<?php

namespace Pepper;

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
}
