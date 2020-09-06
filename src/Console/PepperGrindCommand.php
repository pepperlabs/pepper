<?php

namespace Pepper\Console;

use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Pepper\Supports\Config;
use Symfony\Component\Console\Input\InputArgument;

class PepperGrindCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pepper:grind
                            {--N|no-config : Do not update the config file}
                            {--A|all : Include all models}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Pepper GraphQL classes.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'class';

    /**
     * Type of GraphQL class.
     *
     * @var string
     */
    protected $gql = 'Queries';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/Stubs/graphql.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace."\GraphQL\\{$this->gql}\\Pepper";
    }

    // /**
    //  * Get the console command arguments.
    //  *
    //  * @return array
    //  */
    // protected function getArguments()
    // {
    //     return [
    //         ['class', InputArgument::REQUIRED, 'The name of the class'],
    //         ['parent', InputArgument::REQUIRED, 'The name of the parent GraphQL class'],
    //         ['model', InputArgument::REQUIRED, 'The namespace to model'],
    //         ['modelClass', InputArgument::REQUIRED, 'The class basename of the model'],
    //     ];
    // }

    protected function build($name, $class, $parent, $model)
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceNamespace($stub, $name)
                    ->replaceClassName($stub, $class)
                    ->replaceParent($stub, $parent)
                    ->replaceParentClass($stub, class_basename($parent))
                    ->replaceModel($stub, $model)
                    ->replaceModelClass($stub, class_basename($model));
    }

    protected function replaceClassName(&$stub, $class)
    {
        $stub = str_replace('DummyClass', $class, $stub);

        return $this;
    }

    protected function replaceParent(&$stub, $parent)
    {
        $stub = str_replace('DummyParent', $parent, $stub);

        return $this;
    }

    protected function replaceParentClass(&$stub, $parentClass)
    {
        $stub = str_replace('DummyBaseParentClass', $parentClass, $stub);

        return $this;
    }

    protected function replaceModel(&$stub, $model)
    {
        $stub = str_replace('DummyModel', $model, $stub);

        return $this;
    }

    protected function replaceModelClass(&$stub, $modelClass)
    {
        return str_replace('DummyBaseModelClass', $modelClass, $stub);
    }

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
                'Select models to be included',
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
            : config('pepper.namespace.models');

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

        $this->addGlobalTypes();

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

    private function addGlobalTypes(): void
    {
        $config = new Config(null);

        $config->addGlobalType('ConditionInput');
        $config->addGlobalType('OrderByEnum');
        $config->addGlobalType('AnyScalar');
        $config->addGlobalType('AllUnion');
        // $this->info(' Global Pepper types added.');
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
        $studly = Str::studly($basename);
        $snake = Str::snake($basename);
        // $noConfig = $this->hasOption('no-config') && $this->option('no-config');

        // $this->info('Creating Http'.$basename.'...');
        $this->call('make:pepper:http', ['name' => $basename]);

        $this->gql = 'Types';
        foreach ([
            'ResultAggregateType',
            'FieldAggregateUnresolvableType',
            'FieldAggregateType',
            'AggregateType',
            'Type',
        ] as $type) {
            $name = $this->qualifyClass($studly.$type);
            $path = $this->getPath($name);
            $this->makeDirectory($path);
            $this->files->put($path, $this->sortImports(
                $this->build(
                    $name,
                    $studly.$type,
                    "Pepper\GraphQL\Types\\{$type}",
                    'App\Http\Pepper\\'.$basename,
                )
            ));
        }

        $this->gql = 'Mutations';
        foreach ([
            'Update',
            'Insert',
            'Delete',
        ] as $mutation) {
            $name = $this->qualifyClass($studly.$mutation);
            $path = $this->getPath($name);
            $this->makeDirectory($path);
            $this->files->put($path, $this->sortImports(
                $this->build(
                    $name,
                    $studly.$mutation,
                    "Pepper\GraphQL\Mutations\\{$mutation}",
                    'App\Http\Pepper\\'.$basename,
                )
            ));
        }

        foreach ([
            'Update',
            // 'InsertOne',
            'Delete',
        ] as $mutation) {
            $name = $this->qualifyClass($studly.$mutation);
            $path = $this->getPath($name);
            $this->makeDirectory($path);
            $this->files->put($path, $this->sortImports(
                $this->build(
                    $name,
                    'Update'.$studly.'Pk',
                    "Pepper\GraphQL\Mutations\\{$mutation}",
                    'App\Http\Pepper\\'.$basename,
                )
            ));
        }

        foreach ([
            'Insert',
        ] as $mutation) {
            $name = $this->qualifyClass($studly.$mutation);
            $path = $this->getPath($name);
            $this->makeDirectory($path);
            $this->files->put($path, $this->sortImports(
                $this->build(
                    $name,
                    'Insert'.$studly.'One',
                    "Pepper\GraphQL\Mutations\\{$mutation}",
                    'App\Http\Pepper\\'.$basename,
                )
            ));
        }

        $this->gql = 'Queries';
        foreach ([
            'ByPkQuery',
            'AggregateQuery',
            'Query',
        ] as $query) {
            $name = $this->qualifyClass($studly.$query);
            $path = $this->getPath($name);
            $this->makeDirectory($path);
            $this->files->put($path, $this->sortImports(
                $this->build(
                    $name,
                    $studly.$query,
                    "Pepper\GraphQL\Queries\\{$query}",
                    'App\Http\Pepper\\'.$basename,
                )
            ));
        }

        $this->gql = 'Inputs';
        foreach ([
            'MutationInput',
            'OrderInput',
            'Input',
        ] as $query) {
            $name = $this->qualifyClass($studly.$query);
            $path = $this->getPath($name);
            $this->makeDirectory($path);
            $this->files->put($path, $this->sortImports(
                $this->build(
                    $name,
                    $studly.$query,
                    "Pepper\GraphQL\Inputs\\{$query}",
                    'App\Http\Pepper\\'.$basename,
                )
            ));
        }

        // $this->info($this->type.' created successfully.');
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
            $this->info('Publishing default graphql config...');
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
