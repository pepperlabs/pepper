<?php

namespace Pepper\Console;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use HaydenPierce\ClassFinder\ClassFinder;
use Pepper\Helpers\ConfigHelper as Config;

class PepperGrindCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pepper:grind
                            {--N|--no-config : Do not update the config file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update or create pepper GraphQL classes';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $models = $this->getModels();

        $selected = $this->choice(
            'Select models to be included',
            array_merge(['-- select all --'], $models),
            null,
            null,
            true
        );

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
        foreach (ClassFinder::getClassesInNamespace(config('pepper.namespace')) as $class) {
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
        $model = 'App\Http\Pepper\\' . $basename;
        $studly = Str::of($basename)->studly();
        $snake = Str::of($basename)->snake();
        $noConfig = $this->hasOption('--no-config') && $this->option('--no-config');

        $this->ensureGraphQLConfigExists();
        $config = new Config(null);

        $this->info('Adding default types to config...');
        $config->addType('ConditionInput', 'ConditionInput', 'Pepper\\');
        $config->addType('OrderByEnum', 'OrderByEnum', 'Pepper\\');
        $config->addType('AnyScalar', 'AnyScalar', 'Pepper\\');
        $config->addType('AllUnion', 'AllUnion', 'Pepper\\');

        // Creeat new type
        $typeName = $typeClass = $studly . 'Type';
        $this->info('Creating ' . $typeName . '...');
        $this->call('make:pepper:type', [
            'name' => $typeName, // ClassType
            'class' => $typeClass, // ClassType
            'description' => $basename . ' type description',
            'model' => $model,
            '--no-config' => $noConfig
        ]);

        // Create new type aggregate
        $typeName = $typeClass = $studly . 'AggregateType';
        $this->info('Creating ' . $typeName . '...');
        $this->call('make:pepper:type:aggregate', [
            'name' => $typeName, // ClassAggregateType
            'class' => $typeClass, // ClassAggregateType
            'description' => $basename . ' aggregate type description',
            'model' => $model,
            '--no-config' => $noConfig
        ]);

        // Create new field aggregate type
        $typeName = $typeClass = $studly . 'FieldAggregateType';
        $this->info('Creating ' . $typeName . '...');
        $this->call('make:pepper:type:field-aggregate', [
            'name' => $typeName, // ClassFieldAggregateType
            'class' => $typeClass, // ClassFieldAggregateType
            'description' => $basename . ' field aggregate type description',
            'model' => $model,
            '--no-config' => $noConfig
        ]);

        // Create new field aggregate unresolvalbe type
        $typeName = $typeClass = $studly . 'FieldAggregateUnresolvableType';
        $this->info('Creating ' . $typeName . '...');
        $this->call('make:pepper:type:field-aggregate-unresolvable', [
            'name' => $typeName, // ClassFieldAggregateUnresolvableType
            'class' => $typeClass, // ClassFieldAggregateUnresolvableType
            'description' => $basename . ' field aggregate unresolvable type description',
            'model' => $model,
            '--no-config' => $noConfig
        ]);

        // Create new result aggregate type
        $typeName = $typeClass = $studly . 'ResultAggregateType';
        $this->info('Creating ' . $typeName . '...');
        $this->call('make:pepper:type:result-aggregate', [
            'name' => $typeName, // ClassResultAggregateType
            'class' => $typeClass, // ClassResultAggregateType
            'description' => $basename . ' result aggregate type description',
            'model' => $model,
            '--no-config' => $noConfig
        ]);

        // Create new input
        $inputName = $inputClass = $studly . 'Input';
        $this->info('Creating ' . $inputName . '...');
        $this->call('make:pepper:input', [
            'name' => $inputName, // ClassInput
            'class' => $inputClass, // ClassInput
            'description' => $basename . ' input description',
            'model' => $model,
            '--no-config' => $noConfig
        ]);

        // Create new order input
        $inputName = $inputClass = $studly . 'OrderInput';
        $this->info('Creating ' . $inputName . '...');
        $this->call('make:pepper:input:order', [
            'name' => $inputName, // ClassOrderInput
            'class' => $inputClass, // ClassOrderInput
            'description' => $basename . ' order input description',
            'model' => $model,
            '--no-config' => $noConfig
        ]);

        // Create new mutation input
        $inputName = $inputClass = $studly . 'MutationInput';
        $this->info('Creating ' . $inputName . '...');
        $this->call('make:pepper:input:mutation', [
            'name' => $inputName, // ClassMutationInput
            'class' => $inputClass, // ClassMutationInput
            'description' => $basename . ' mutation input description',
            'model' => $model,
            '--no-config' => $noConfig
        ]);

        // Create new query
        $queryName = $studly . 'Query';
        $queryClass = $snake->__toString();
        $this->info('Creating ' . $queryName . '...');
        $this->call('make:pepper:query', [
            'name' => $queryName, // ClassQuery
            'class' => $queryClass, // class
            'description' => $basename . ' query description',
            'model' => $model,
            '--no-config' => $noConfig
        ]);

        // Create new query aggregate
        $queryName = $studly . 'AggregateQuery';
        $queryClass = $snake . '_aggregate';
        $this->info('Creating ' . $queryName . '...');
        $this->call('make:pepper:query:aggregate', [
            'name' => $queryName, // ClassQuery
            'class' => $queryClass, // class_aggregate
            'description' => $basename . ' query description',
            'model' => $model,
            '--no-config' => $noConfig
        ]);

        // Create new query by PK aggregate
        $queryName = $studly . 'ByPkQuery';
        $queryClass = $snake . '_by_pk';
        $this->info('Creating ' . $queryName . '...');
        $this->call('make:pepper:query:by-pk', [
            'name' => $queryName, // ClassQuery
            'class' => $queryClass, // class_by_pk
            'description' => $basename . ' by PK query description',
            'model' => $model,
            '--no-config' => $noConfig
        ]);
    }

    /**
     * Ensure GraphQL config file exists, otherwise we would publish a new one.
     *
     * @return void
     */
    private function ensureGraphQLConfigExists(): void
    {
        if (!file_exists(config_path('graphql.php'))) {
            $this->info('Publishing default graphql config...');
            $this->call('vendor:publish', [
                '--provider' => 'Rebing\GraphQL\GraphQLServiceProvider'
            ]);
        }
    }
}
