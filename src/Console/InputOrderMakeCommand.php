<?php

declare(strict_types=1);

namespace Pepper\Console;

class InputOrderMakeCommand extends BaseMakeCommand
{
    protected $signature = 'make:pepper:input:order {name} {class} {description} {model}';
    protected $description = 'Create a new Pepper input order class';
    protected $type = 'class';

    protected function getStub()
    {
        return __DIR__ . '/stubs/input_order.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\GraphQL\Inputs\Pepper';
    }
}
