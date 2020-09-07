<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\Pepper;

use App;
use Closure;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

use GraphQL\Type\Definition\ResolveInfo;

class UserUpdateByPkMutation extends Mutation
{
    protected $attributes = [
        'name' => 'update_user_by_pk',
        'description' => 'User update by PK mutation description'
    ];

    protected $instance;

    public function __construct()
    {
        $this->instance = new App\Http\Pepper\User;
    }

    public function type(): Type
    {
        return $this->instance->getMutationUpdateByPkType();
    }

    public function args(): array
    {
        return $this->instance->getMutationUpdateByPkFields();
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->instance->updateByPkMutation($root, $args, $context, $resolveInfo, $getSelectFields);
    }
}
