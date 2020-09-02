<?php

namespace Pepper\GraphQL;

use Illuminate\Support\Str;

class BaseGraphQL
{
    /**
     * Get given instance class basename.
     *
     * @return void
     */
    public function name(): string
    {
        return class_basename($this);
    }

    /**
     * Get studly version of the class basename.
     *
     * @return string
     */
    public function studly(): string
    {
        return Str::studly($this->name());
    }
}
