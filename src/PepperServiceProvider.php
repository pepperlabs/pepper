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

        $queries = [];
        $namespace = 'App\GraphQL\Queries\Pepper';
        foreach (glob(app_path() . '/GraphQL/Queries/Pepper/*Query.php') as $path) {
            $class = $namespace . '\\' . str_replace(glob(app_path() . '/GraphQL/Queries/Pepper/'), '', $path);
            $class = preg_replace('/.php$/', '', $class);
            $queries[(new $class)->getAttributes()['name']] = $path;
        }
        app('config')->set('graphql.schemas.default.query', array_merge($queries, config('graphql.schemas.default.query')));
        // dd(config('graphql'));
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
    }
}
