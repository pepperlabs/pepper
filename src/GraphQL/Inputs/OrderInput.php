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
        $this->attributes['name'] = $this->instance->getOrderName();
        $this->attributes['description'] = $this->instance->getQueryDescription();
    }

    public function fields(): array
    {
        return $this->instance->getOrderFields();
    }
}
