<?php

namespace Pepper\Console;

use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Pepper\Helpers\ConfigHelper as Config;

class PepperGrindCommand extends Command
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
    protected $description = 'Update or create pepper GraphQL classes';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle() : void
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
    private function getModels() : array
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
    private function createHttp(array $models, array $selected) : void
    {
        $this->ensureGraphQLConfigExists();
        $config = new Config(null);

        $this->info('Adding default types to config...');
        $config->addGlobalType('ConditionInput');
        $config->addGlobalType('OrderByEnum');
        $config->addGlobalType('AnyScalar');
        $config->addGlobalType('AllUnion');

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
    private function initModelHttp(string $model) : void
    {
        $basename = class_basename($model);
        $model = 'App\Http\Pepper\\'.$basename;
        $studly = Str::studly($basename);
        $snake = Str::snake($basename);
        $noConfig = $this->hasOption('no-config') && $this->option('no-config');

        $this->info('Creating Http'.$basename.'...');
        $this->call('make:pepper:http', [
            'name' => $basename, // Class
        ]);

        // Creeat new type
        $typeName = $typeClass = $studly.'Type';
        $this->info('Creating '.$typeName.'...');
        $this->call('make:pepper:type', [
            'name' => $typeName, // ClassType
            'class' => $typeClass, // ClassType
            'description' => $basename.' type description',
            'model' => $model,
            '--no-config' => $noConfig,
        ]);

        // Create new type aggregate
        $typeName = $typeClass = $studly.'AggregateType';
        $this->info('Creating '.$typeName.'...');
        $this->call('make:pepper:type:aggregate', [
            'name' => $typeName, // ClassAggregateType
            'class' => $typeClass, // ClassAggregateType
            'description' => $basename.' aggregate type description',
            'model' => $model,
            '--no-config' => $noConfig,
        ]);

        // Create new field aggregate type
        $typeName = $typeClass = $studly.'FieldAggregateType';
        $this->info('Creating '.$typeName.'...');
        $this->call('make:pepper:type:field-aggregate', [
            'name' => $typeName, // ClassFieldAggregateType
            'class' => $typeClass, // ClassFieldAggregateType
            'description' => $basename.' field aggregate type description',
            'model' => $model,
            '--no-config' => $noConfig,
        ]);

        // Create new field aggregate unresolvalbe type
        $typeName = $typeClass = $studly.'FieldAggregateUnresolvableType';
        $this->info('Creating '.$typeName.'...');
        $this->call('make:pepper:type:field-aggregate-unresolvable', [
            'name' => $typeName, // ClassFieldAggregateUnresolvableType
            'class' => $typeClass, // ClassFieldAggregateUnresolvableType
            'description' => $basename.' field aggregate unresolvable type description',
            'model' => $model,
            '--no-config' => $noConfig,
        ]);

        // Create new result aggregate type
        $typeName = $typeClass = $studly.'ResultAggregateType';
        $this->info('Creating '.$typeName.'...');
        $this->call('make:pepper:type:result-aggregate', [
            'name' => $typeName, // ClassResultAggregateType
            'class' => $typeClass, // ClassResultAggregateType
            'description' => $basename.' result aggregate type description',
            'model' => $model,
            '--no-config' => $noConfig,
        ]);

        // Create new input
        $inputName = $inputClass = $studly.'Input';
        $this->info('Creating '.$inputName.'...');
        $this->call('make:pepper:input', [
            'name' => $inputName, // ClassInput
            'class' => $inputClass, // ClassInput
            'description' => $basename.' input description',
            'model' => $model,
            '--no-config' => $noConfig,
        ]);

        // Create new order input
        $inputName = $inputClass = $studly.'OrderInput';
        $this->info('Creating '.$inputName.'...');
        $this->call('make:pepper:input:order', [
            'name' => $inputName, // ClassOrderInput
            'class' => $inputClass, // ClassOrderInput
            'description' => $basename.' order input description',
            'model' => $model,
            '--no-config' => $noConfig,
        ]);

        // Create new mutation input
        $inputName = $inputClass = $studly.'MutationInput';
        $this->info('Creating '.$inputName.'...');
        $this->call('make:pepper:input:mutation', [
            'name' => $inputName, // ClassMutationInput
            'class' => $inputClass, // ClassMutationInput
            'description' => $basename.' mutation input description',
            'model' => $model,
            '--no-config' => $noConfig,
        ]);

        // Create new query
        $queryName = $studly.'Query';
        $queryClass = $snake;
        $this->info('Creating '.$queryClass.'...');
        $this->call('make:pepper:query', [
            'name' => $queryName, // ClassQuery
            'class' => $queryClass, // class
            'description' => $basename.' query description',
            'model' => $model,
            '--no-config' => $noConfig,
        ]);

        // Create new query aggregate
        $queryName = $studly.'AggregateQuery';
        $queryClass = $snake.'_aggregate';
        $this->info('Creating '.$queryClass.'...');
        $this->call('make:pepper:query:aggregate', [
            'name' => $queryName, // ClassQuery
            'class' => $queryClass, // class_aggregate
            'description' => $basename.' query description',
            'model' => $model,
            '--no-config' => $noConfig,
        ]);

        // Create new query by PK aggregate
        $queryName = $studly.'ByPkQuery';
        $queryClass = $snake.'_by_pk';
        $this->info('Creating '.$queryClass.'...');
        $this->call('make:pepper:query:by-pk', [
            'name' => $queryName, // ClassQuery
            'class' => $queryClass, // class_by_pk
            'description' => $basename.' by PK query description',
            'model' => $model,
            '--no-config' => $noConfig,
        ]);

        // Create new delete mutation
        $mutationName = $studly.'DeleteMutation';
        $mutationClass = 'delete_'.$snake;
        $this->info('Creating '.$mutationClass.'...');
        $this->call('make:pepper:mutation:delete', [
            'name' => $mutationName, // ClassDeleteMutation
            'class' => $mutationClass, // delete_class
            'description' => $basename.' delete mutation description',
            'model' => $model,
            '--no-config' => $noConfig,
        ]);

        // Create new delete mutation by PK
        $mutationName = $studly.'DeleteByPkMutation';
        $mutationClass = 'delete_'.$snake.'_by_pk';
        $this->info('Creating '.$mutationClass.'...');
        $this->call('make:pepper:mutation:delete:by-pk', [
            'name' => $mutationName, // ClassDeleteByPkMutation
            'class' => $mutationClass, // delete_class_by_pk
            'description' => $basename.' delete by PK mutation description',
            'model' => $model,
            '--no-config' => $noConfig,
        ]);

        // Create new insert mutation
        $mutationName = $studly.'InsertMutation';
        $mutationClass = 'insert_'.$snake;
        $this->info('Creating '.$mutationClass.'...');
        $this->call('make:pepper:mutation:insert', [
            'name' => $mutationName, // ClassInsertMutation
            'class' => $mutationClass, // insert_class
            'description' => $basename.' insert mutation description',
            'model' => $model,
            '--no-config' => $noConfig,
        ]);

        // Create new insert one mutation
        $mutationName = $studly.'InsertOneMutation';
        $mutationClass = 'insert_'.$snake.'_one';
        $this->info('Creating '.$mutationClass.'...');
        $this->call('make:pepper:mutation:insert:one', [
            'name' => $mutationName, // ClassInsertOneMutation
            'class' => $mutationClass, // insert_class_one
            'description' => $basename.' insert one mutation description',
            'model' => $model,
            '--no-config' => $noConfig,
        ]);

        // Create new update mutation
        $mutationName = $studly.'UpdateMutation';
        $mutationClass = 'update_'.$snake;
        $this->info('Creating '.$mutationClass.'...');
        $this->call('make:pepper:mutation:update', [
            'name' => $mutationName, // ClassUpdateMutation
            'class' => $mutationClass, // update_class
            'description' => $basename.' update mutation description',
            'model' => $model,
            '--no-config' => $noConfig,
        ]);

        // Create new update mutation by PK
        $mutationName = $studly.'UpdateByPkMutation';
        $mutationClass = 'update_'.$snake.'_by_pk';
        $this->info('Creating '.$mutationClass.'...');
        $this->call('make:pepper:mutation:update:by-pk', [
            'name' => $mutationName, // ClassUpdateByPkMutation
            'class' => $mutationClass, // update_class_by_pk
            'description' => $basename.' update by PK mutation description',
            'model' => $model,
            '--no-config' => $noConfig,
        ]);
    }

    /**
     * Ensure GraphQL config file exists, otherwise we would publish a new one.
     *
     * @todo refactor to trait
     * @return void
     */
    private function ensureGraphQLConfigExists() : void
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
