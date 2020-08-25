<?php

declare(strict_types=1);

namespace Pepper\Console;

class QueryMakeCommand extends BaseMakeCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:pepper:query
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
    protected $description = 'Create a new Pepper query class';

    /**
     * Query of GraphQL class.
     *
     * @var string
     */
    protected $gql = 'query';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/Stubs/query.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\GraphQL\Queries\Pepper';
    }
}
