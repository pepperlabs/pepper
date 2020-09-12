<?php

declare(strict_types=1);

namespace Pepper\GraphQL\Queries;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;

class AggregateQuery extends Query
{
    protected $attributes = [];

    protected $instance;

    public function __construct($pepper)
    {
        $this->instance = new $pepper;
        $this->attributes['name'] = $this->instance->getAggregateQueryName();
        $this->attributes['description'] = $this->instance->getAggregateQueryDescription();
    }

    public function type(): Type
    {
        return $this->instance->getQueryAggregateType();
    }

    public function args(): array
    {
        return $this->instance->getQueryArgs();
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->instance->resolveQueryAggregate($root, $args, $context, $resolveInfo, $getSelectFields);
    }
}
