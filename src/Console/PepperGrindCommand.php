<?php

namespace Pepper\Console;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use HaydenPierce\ClassFinder\ClassFinder;

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
        $studly = Str::of($basename)->studly();

        // Creeat new type
        $typeName = $typeClass = $studly . 'Type';
        $this->call('make:pepper:type', [
            'name' => $typeName, // ClassType
            'class' => $typeClass, // ClassType
            'description' => $basename . ' type description',
            'model' => $model,
            '--no-config' => $this->hasOption('--no-config') && $this->option('--no-config')
        ]);

        // Create new type aggregate
        $typeName = $typeClass = $studly . 'AggregateType';
        $this->call('make:pepper:type:aggregate', [
            'name' => $typeName, // ClassAggregateType
            'class' => $typeClass, // ClassAggregateType
            'description' => $basename . ' aggregate type description',
            'model' => $model,
            '--no-config' => $this->hasOption('--no-config') && $this->option('--no-config')
        ]);

        // Create new field aggregate type
        $typeName = $typeClass = $studly . 'FieldAggregateType';
        $this->call('make:pepper:type:field-aggregate', [
            'name' => $typeName, // ClassFieldAggregateType
            'class' => $typeClass, // ClassFieldAggregateType
            'description' => $basename . ' field aggregate type description',
            'model' => $model,
            '--no-config' => $this->hasOption('--no-config') && $this->option('--no-config')
        ]);

        // Create new field aggregate unresolvalbe type
        $typeName = $typeClass = $studly . 'FieldAggregateUnresolvableType';
        $this->call('make:pepper:type:field-aggregate-unresolvable', [
            'name' => $typeName, // ClassFieldAggregateUnresolvableType
            'class' => $typeClass, // ClassFieldAggregateUnresolvableType
            'description' => $basename . ' field aggregate unresolvable type description',
            'model' => $model,
            '--no-config' => $this->hasOption('--no-config') && $this->option('--no-config')
        ]);

        // Create new result aggregate type
        $typeName = $typeClass = $studly . 'ResultAggregateType';
        $this->call('make:pepper:type:result-aggregate', [
            'name' => $typeName, // ClassResultAggregateType
            'class' => $typeClass, // ClassResultAggregateType
            'description' => $basename . ' result aggregate type description',
            'model' => $model,
            '--no-config' => $this->hasOption('--no-config') && $this->option('--no-config')
        ]);
    }
}
