<?php

namespace Pepper;

use Illuminate\Support\ServiceProvider;
use Pepper\Commands\TypeCommand;
use Pepper\Commands\QueryCommand;
use Pepper\Commands\InputCommand;
use Pepper\Commands\OrderCommand;
use Pepper\Commands\MutationCommand;
use Pepper\Commands\AddCommand;

use Pepper\Console\OrderMakeCommand;

class PepperServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/pepper.php' => config_path('pepper.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../resources/lang' => "{$this->app['path.lang']}/vendor/pepper",
        ]);

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang/', 'pepper');
    }

    public function register()
    {
        $this->registerPepper();

        if ($this->app->runningInConsole()) {
            $this->registerConsole();
        }
    }

    public function registerPepper(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/pepper.php', 'pepper');

        $this->app->register(\Rebing\GraphQL\GraphQLServiceProvider::class);
    }

    public function registerConsole(): void
    {
        $this->commands(OrderMakeCommand::class);
    }
}
