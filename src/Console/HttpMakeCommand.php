<?php

declare(strict_types=1);

namespace Pepper\Console;

use Illuminate\Console\GeneratorCommand;

class HttpMakeCommand extends GeneratorCommand
{
    protected $signature = 'make:pepper:http {name}';
    protected $description = 'Create a new Pepper class';

    protected function getStub()
    {
        return __DIR__ . '/Stubs/http.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Pepper';
    }
}
