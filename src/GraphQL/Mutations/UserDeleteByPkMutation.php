<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\Pepper;

use App;
use Closure;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;

use GraphQL\Type\Definition\ResolveInfo;

class UserDeleteByPkMutation extends Mutation
{
    protected $attributes = [
        'name' => 'delete_user_by_pk',
        'description' => 'User delete by PK mutation description'
    ];

    protected $instance;

    public function __construct()
    {
        $this->instance = new App\Http\Pepper\User;
    }

    public function type(): Type
    {
        return $this->instance->getDeleteByPKMutationType();
    }

    public function args(): array
    {
        return $this->instance->getDeleteByPkMutationFields();
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->instance->deleteByPkMutation($root, $args, $context, $resolveInfo, $getSelectFields);
    }
}
