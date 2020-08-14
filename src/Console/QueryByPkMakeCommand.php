<?php

declare(strict_types=1);

namespace Pepper\Console;

class QueryByPkMakeCommand extends BaseMakeCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:pepper:query:by-pk {name} {class} {description} {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Pepper query by PK class';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/query_by_pk.stub';
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
