<?php

declare(strict_types=1);

namespace Pepper\Console;

class InputOrderMakeCommand extends BaseMakeCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:pepper:input:order
                            {name : The name of the class}
                            {class : The name of the GraphQL class}
                            {description : The description of the GraphQL class}
                            {model : The model of the GraphQL class}
                            {--N|no-config : Do not update the config file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Pepper input order class';

    /**
     * Input of GraphQL class.
     *
     * @var string
     */
    protected $gql = 'input';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/Stubs/input_order.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\GraphQL\Inputs\Pepper';
    }
}
