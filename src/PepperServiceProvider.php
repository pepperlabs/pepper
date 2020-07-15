<?php

namespace Amirmasoud\Pepper;

use Illuminate\Support\ServiceProvider;
use Amirmasoud\Pepper\Commands\MetadataCommand;

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
        $this->mergeConfigFrom(__DIR__ . '/../config/pepper.php', 'pepper');

        $this->app->register(\Rebing\GraphQL\GraphQLServiceProvider::class);

        $this->app->bind('command.pepper:queries', MetadataCommand::class);

        $this->commands([
            'command.pepper:queries',
        ]);

        $this->app->singleton(ConsoleOutput::class);

        $fl = new FileLoader(config('graphql'));
        dd($fl->save('graphql.php'));

        $this->app->call(function () {
            RegisterGraphQLQueries::init();
        });
    }
}
