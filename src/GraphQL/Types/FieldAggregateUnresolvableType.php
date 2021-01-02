<?php

declare(strict_types=1);

namespace Pepper\GraphQL\Types;

use Rebing\GraphQL\Support\Type as GraphQLType;

class FieldAggregateUnresolvableType extends GraphQLType
{
    protected $attributes = [];

    protected $instance;

    public function __construct($pepper)
    {
        // Dynamic definition of common attributes.
        $this->instance = new $pepper;
        $this->attributes['name'] = $this->instance->getFieldAggregateUnresolvableTypeName();
        $this->attributes['description'] = $this->instance->getFieldAggregateUnresolvableTypeDescription();
    }

    public function fields(): array
    {
        return $this->instance->getFieldAggregateTypeFields(false);
    }
}
