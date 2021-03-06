<?php

namespace Pepper\GraphQL\Types;

use Rebing\GraphQL\Support\Type as GraphQLType;

class Type extends GraphQLType
{
    protected $attributes = [];

    protected $instance;

    public function __construct($pepper)
    {
        // Dynamic definition of common attributes.
        $this->instance = new $pepper;
        $this->attributes['name'] = $this->instance->getTypeName();
        $this->attributes['description'] = $this->instance->getTypeDescription();
        $this->attributes['model'] = $this->instance->modelClass();
    }

    public function fields(): array
    {
        return $this->instance->getTypeFields();
    }
}
