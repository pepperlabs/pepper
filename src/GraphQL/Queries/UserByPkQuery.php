<?php

declare(strict_types=1);

namespace App\GraphQL\Queries\Pepper;

use App;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;

class UserByPkQuery extends Query
{
    protected $attributes = [
        'name' => 'user_by_pk',
        'description' => 'User by PK query description'
    ];

    protected $instance;

    public function __construct()
    {
        $this->instance = new App\Http\Pepper\User;
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
