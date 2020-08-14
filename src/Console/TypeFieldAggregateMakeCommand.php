<?php

declare(strict_types=1);

namespace Pepper\Console;

class TypeFieldAggregateMakeCommand extends BaseMakeCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:pepper:type:field-aggregate {name} {class} {description} {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Pepper type field aggregate class';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/type_field_aggregate.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\GraphQL\Types\Pepper';
    }
}
