<?php

namespace Pepper\Console;

use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Pepper\Supports\Config;

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

        $classes = [
            'Types' => [
                '{{studly}}ResultAggregateType' => 'ResultAggregateType',
                '{{studly}}FieldAggregateUnresolvableType' => 'FieldAggregateUnresolvableType',
                '{{studly}}FieldAggregateType' => 'FieldAggregateType',
                '{{studly}}AggregateType' => 'AggregateType',
                '{{studly}}Type' => 'Type',
            ],
            'Mutations' => [
                '{{snake}}_update' => 'UpdateMutation',
                '{{snake}}_insert' => 'InsertMutation',
                '{{snake}}_delete' => 'DeleteMutation',
                'update_{{snake}}_by_pk' => 'UpdateByPkMutation',
                'delete_{{snake}}_by_pk' => 'DeleteByPkMutation',
                'insert_{{snake}}_one' => 'InsertOneMutation',
            ],
            'Queries' => [
                '{{snake}}_by_pk' => 'ByPkQuery',
                '{{snake}}_aggregate' => 'AggregateQuery',
                '{{snake}}' => 'Query',
            ],
            'Inputs' => [
                '{{studly}}MutationInput' => 'MutationInput',
                '{{studly}}OrderInput' => 'OrderInput',
                '{{studly}}Input' => 'Input',
            ],
        ];

        foreach ($classes as $kind => $classes) {
            $this->gql = $kind;
            foreach ($classes as $key => $class) {
                $name = $this->qualifyClass($studly.$class);
                $path = $this->getPath($name);
                $this->makeDirectory($path);
                $this->files->put($path, $this->sortImports(
                    $this->build(
                        $name,
                        $studly.$class,
                        'Pepper\GraphQL\\'.$kind.'\\'.$class,
                        'App\Http\Pepper\\'.$basename,
                    )
                ));

                $this->addConfig(
                    $kind,
                    Str::of($key)->replace('{{studly}}', $studly)->replace('{{snake}}', $snake),
                    $studly.$class
                );
            }
        }
    }

    public function addConfig($kind, $key, $class)
    {
        if (! $this->hasOption('no-config') || ! $this->option('no-config')) {
            $this->ensureGraphQLConfigExists();
            $config = new Config(null);
            $gql = strtolower($this->gql);

            if ($kind == 'Types') {
                $config->addType($key, $class, $kind);
            } elseif ($kind == 'Inputs') {
                $config->addInput($key, $class, $kind);
            } elseif ($kind == 'Queries') {
                $config->addQuery($key, $class, $kind);
            } elseif ($kind == 'Mutations') {
                $config->addMutation($key, $class, $kind);
            }
        }
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
