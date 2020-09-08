<?php

declare(strict_types=1);

namespace Pepper\GraphQL\Queries;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;

class ByPkQuery extends Query
{
    protected $attributes = [];

    protected $instance;

    public function __construct($pepper)
    {
        $this->instance = new $pepper;
        $this->attributes['name'] = $this->instance->getQueryName().'_by_pk';
        $this->attributes['description'] = $this->instance->getQueryDescription();
    }

    public function type(): Type
    {
        return $this->instance->getQueryByPkType();
    }

    public function args(): array
    {
        return $this->instance->getQueryByPkFields();
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->instance->queryByPk($root, $args, $context, $resolveInfo, $getSelectFields);
    }
}
