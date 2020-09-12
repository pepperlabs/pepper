<?php

declare(strict_types=1);

namespace Pepper\GraphQL\Inputs;

use Rebing\GraphQL\Support\InputType;

class Input extends InputType
{
    protected $attributes = [];

    protected $instance;

    public function __construct($pepper)
    {
        $this->instance = new $pepper;
        $this->attributes['name'] = $this->instance->getInputName();
        $this->attributes['description'] = $this->instance->getInputDescription();
    }

    public function fields(): array
    {
        return $this->instance->getInputFields();
    }
}
