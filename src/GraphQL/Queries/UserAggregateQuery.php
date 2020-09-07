<?php

declare(strict_types=1);

namespace App\GraphQL\Queries\Pepper;

use App;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;

class UserAggregateQuery extends Query
{
    protected $attributes = [
        'name' => 'user_aggregate',
        'description' => 'User query description'
    ];

    protected $instance;

    public function __construct()
    {
        $this->instance = new App\Http\Pepper\User;
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
