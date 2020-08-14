<?php

declare(strict_types=1);

namespace Pepper\Console;

class InputMutationMakeCommand extends BaseMakeCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:pepper:input:mutation {name} {class} {description} {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Pepper input mutation class';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/input_mutation.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\GraphQL\Inputs\Pepper';
    }
}
