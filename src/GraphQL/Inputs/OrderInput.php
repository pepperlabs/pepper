<?php

declare(strict_types=1);

namespace Pepper\GraphQL\Inputs;

use Rebing\GraphQL\Support\InputType;

class OrderInput extends InputType
{
    protected $attributes = [];

    protected $instance;

    public function __construct($pepper)
    {
        $this->instance = new $pepper;
        $this->attributes['name'] = $this->instance->getOrderInputName();
        $this->attributes['description'] = $this->instance->getOrderInputDescription();
    }

    public function fields(): array
    {
        return $this->instance->getOrderFields();
    }
}
