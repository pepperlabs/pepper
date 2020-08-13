<?php

declare(strict_types=1);

namespace Pepper\Console;

use Illuminate\Console\GeneratorCommand;

class OrderMakeCommand extends GeneratorCommand
{
    protected $signature = 'make:pepper:Order {name}';
    protected $description = 'Create a new Pepper order class';
    protected $type = 'Order';

    protected function getStub()
    {
        return __DIR__ . '/stubs/order.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\GraphQL\Inputs\Pepper';
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        return $this->replaceGraphqlName($stub);
    }

    protected function replaceGraphqlName(string $stub): string
    {
        $graphqlName = $this->getNameInput();
        $graphqlName = str_replace('InputObject', 'Input', $graphqlName);
        $graphqlName = preg_replace('/Type$/', '', $graphqlName);

        return str_replace(
            'DummyGraphqlName',
            $graphqlName,
            $stub
        );
    }
}
