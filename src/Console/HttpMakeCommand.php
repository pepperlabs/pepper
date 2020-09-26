<?php

declare(strict_types=1);

namespace Pepper\Console;

use Illuminate\Console\GeneratorCommand;

class HttpMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:pepper:http {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Pepper class';

    /**
     * Get stub path.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/Stubs/http.stub';
    }

    /**
     * Get default namespace.
     *
     * @param  string  $rootNamespace
     * @return void
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Http\Pepper';
    }
}
