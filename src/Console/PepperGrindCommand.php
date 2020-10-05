<?php

namespace Pepper\Console;

use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class PepperGrindCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pepper:grind
                            {--A|all : Include all models without asking}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Pepper GraphQL classes';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $models = $this->getModels();

        if (! $this->hasOption('all') || ! $this->option('all')) {
            $selected = $this->choice(
                'Select models:',
                array_merge(['-- select all --'], $models),
                null,
                null,
                true
            );
        } else {
            $selected = [0];
        }

        $this->createHttp($models, $selected);
    }

    /**
     * Get a list of models in the supplied namesapce.
     *
     * @return array
     */
    private function getModels(): array
    {
        $classes = [];
        $models = App::runningUnitTests()
            ? 'Tests\Support\Models'
            : config('pepper.base.namespace.models');

        foreach (ClassFinder::getClassesInNamespace($models) as $class) {
            $classes[] = $class;
        }

        return $classes;
    }

    /**
     * Create required classes for the given model.
     *
     * @param  array $models
     * @param  array $selected
     * @return void
     */
    private function createHttp(array $models, array $selected): void
    {
        $this->ensureGraphQLConfigExists();

        if (in_array('-- select all --', $selected)) {
            foreach ($models as $model) {
                $this->initModelHttp($model);
            }
        } else {
            foreach ($selected as $model) {
                $this->initModelHttp($model);
            }
        }
    }

    /**
     * Initilize GraphQL endpoint required classes.
     *
     * @param  string $model
     * @return void
     */
    private function initModelHttp(string $model): void
    {
        $basename = class_basename($model);
        $model = 'App\Http\Pepper\\'.$basename;

        $this->info('Creating Http'.$basename.'...');
        $this->call('make:pepper:http', [
            'name' => $basename, // Class
        ]);
    }

    /**
     * Ensure GraphQL config file exists, otherwise we would publish a new one.
     *
     * @todo refactor to trait
     * @return void
     */
    private function ensureGraphQLConfigExists(): void
    {
        if (! file_exists(config_path('graphql.php'))) {
            $this->info('Publishing default GraphQL config...');
            $this->call('vendor:publish', [
                '--provider' => 'Rebing\GraphQL\GraphQLServiceProvider',
            ]);
        }

        if (! file_exists(config_path('pepper.php'))) {
            $this->info('Publishing default Pepper config...');
            $this->call('vendor:publish', [
                '--provider' => 'Pepper\PepperServiceProvider',
            ]);
        }
    }
}
