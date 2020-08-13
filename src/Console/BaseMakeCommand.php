<?php

declare(strict_types=1);

namespace Pepper\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;

abstract class BaseMakeCommand extends GeneratorCommand
{
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the class'],
            ['class', InputArgument::REQUIRED, 'The name of the GraphQL class'],
            ['description', InputArgument::REQUIRED, 'The description of the GraphQL class'],
            ['model', InputArgument::REQUIRED, 'The model of the GraphQL'],
        ];
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        return $this->replaceName($stub, $this->argument('class'))
            ->replaceDescription($stub, $this->argument('description'))
            ->replaceModel($stub, $this->argument('model'));
    }

    protected function replaceName(&$stub, $name)
    {
        str_replace(['DummyName', '{{ name }}', '{{name}}'], $name, $stub);

        return $this;
    }

    protected function replaceDescription(&$stub, $description)
    {
        str_replace(['DummyDescription', '{{ description }}', '{{description}}'], $description, $stub);

        return $this;
    }

    protected function replaceModel($stub, $model)
    {
        return str_replace(['DummyModel', '{{ model }}', '{{model}}'], $model, $stub);
    }
}
