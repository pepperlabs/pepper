<?php

namespace Pepper;

use Illuminate\Support\ServiceProvider;
use Pepper\Console\InputMakeCommand;
use Pepper\Console\InputMutationMakeCommand;
use Pepper\Console\InputOrderMakeCommand;
use Pepper\Console\MutationDeleteByPkMakeCommand;
use Pepper\Console\MutationDeleteMakeCommand;
use Pepper\Console\MutationInsertMakeCommand;
use Pepper\Console\MutationInsertOneMakeCommand;
use Pepper\Console\MutationMakeCommand;
use Pepper\Console\MutationUpdateByPkMakeCommand;
use Pepper\Console\MutationUpdateMakeCommand;
use Pepper\Console\QueryAggregateMakeCommand;
use Pepper\Console\QueryByPkMakeCommand;
use Pepper\Console\QueryMakeCommand;
use Pepper\Console\TypeAggregateMakeCommand;
use Pepper\Console\TypeFieldAggregateMakeCommand;
use Pepper\Console\TypeFieldAggregateUnresolvableMakeCommand;
use Pepper\Console\TypeMakeCommand;
use Pepper\Console\TypeResultAggregateMakeCommand;

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

    /**
     * Register commands.
     *
     * @return void
     */
    public function registerConsole(): void
    {
        $this->commands(InputMakeCommand::class);
        $this->commands(InputMutationMakeCommand::class);
        $this->commands(InputOrderMakeCommand::class);
        $this->commands(MutationDeleteByPkMakeCommand::class);
        $this->commands(MutationDeleteMakeCommand::class);
        $this->commands(MutationInsertMakeCommand::class);
        $this->commands(MutationInsertOneMakeCommand::class);
        $this->commands(MutationMakeCommand::class);
        $this->commands(MutationUpdateByPkMakeCommand::class);
        $this->commands(MutationUpdateMakeCommand::class);
        $this->commands(QueryAggregateMakeCommand::class);
        $this->commands(QueryByPkMakeCommand::class);
        $this->commands(QueryMakeCommand::class);
        $this->commands(TypeAggregateMakeCommand::class);
        $this->commands(TypeFieldAggregateMakeCommand::class);
        $this->commands(TypeFieldAggregateUnresolvableMakeCommand::class);
        $this->commands(TypeMakeCommand::class);
        $this->commands(TypeResultAggregateMakeCommand::class);
    }
}
