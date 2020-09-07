<?php

declare(strict_types=1);

namespace Pepper\GraphQL\Queries;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query as GraphQLQuery;

class Query extends GraphQLQuery
{
    protected $attributes = [
        // 'name' => 'user',
        // 'description' => '',
    ];

    protected $instance;

    private $class;

    public function __construct($pepper)
    {
        // $this->class = new $class;
        // $pepper = resolve($class);
        $this->instance = new $pepper;
        $this->attributes['name'] = $this->instance->getQueryName();
        $this->attributes['description'] = $this->instance->getQueryDescription();
    }

    public function type(): Type
    {
        return $this->instance->getQueryType();
    }

    public function args(): array
    {
        return $this->instance->getQueryArgs();
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->instance->getQueryResolve($root, $args, $context, $resolveInfo, $getSelectFields)->get();
    }

    // public function __call($method, $arguments)
    // {

    // }
}
