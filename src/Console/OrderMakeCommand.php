<?php

declare(strict_types=1);

namespace Pepper\Console;

class OrderMakeCommand extends BaseMakeCommand
{
    protected $signature = 'make:pepper:order {name} {class} {description} {model}';
    protected $description = 'Create a new Pepper order class';
    protected $type = 'class';

    protected function getStub()
    {
        return __DIR__ . '/stubs/order.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\GraphQL\Inputs\Pepper';
    }
}
