<?php

declare(strict_types=1);

namespace Pepper\GraphQL\Types;

use Rebing\GraphQL\Support\Type as GraphQLType;

class AggregateType extends GraphQLType
{
    protected $attributes = [];

    protected $instance;

    public function __construct($pepper)
    {
        $this->instance = new $pepper;
        $this->attributes['name'] = $this->instance->getAggregateName();
        $this->attributes['description'] = $this->instance->getQueryDescription();
    }

    public function fields(): array
    {
        return $this->instance->getAvailableAggregators();
    }
}
