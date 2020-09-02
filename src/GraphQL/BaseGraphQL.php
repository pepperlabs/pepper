<?php

namespace Pepper\GraphQL;

use Illuminate\Support\Str;

class BaseGraphQL
{
    /**
     * Get the class basename of the GraphQL class.
     *
     * @return void
     */
    public function name(): string
    {
        return class_basename($this);
    }

    /**
     * Get studly case of the class basename of the GraphQL class.
     *
     * @return string
     */
    public function studly(): string
    {
        return Str::studly($this->name());
    }

    /**
     * Default model for each GraphQL class is set to defined model namespace
     * concated with studly case of the GraphQL class. for example a class
     * of User and model name space of App\\ would resolve to App\User.
     *
     * @return string
     */
    private function defaultModel(): string
    {
        return config('pepper.namespace.models').'\\'.$this->studly();
    }

    /**
     * If model property has been set for the GraphQL class, it would override
     * the default generate model guessed based on the namespace and class
     * basename and will start use the defined model property instead.
     *
     * @return string
     */
    public function model(): string
    {
        return property_exists($this, 'model')
            ? $this->model
            : $this->defaultModel();
    }
}
