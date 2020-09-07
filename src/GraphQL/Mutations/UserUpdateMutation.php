<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\Pepper;

use App;
use Closure;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

use GraphQL\Type\Definition\ResolveInfo;

class UserUpdateMutation extends Mutation
{
    protected $attributes = [
        'name' => 'update_user',
        'description' => 'User update mutation description'
    ];

    protected $instance;

    public function __construct()
    {
        $this->instance = new App\Http\Pepper\User;
    }

    public function type(): Type
    {
        return $this->instance->getMutationType();
    }

    public function args(): array
    {
        return $this->instance->getMutationUpdateFields();
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->instance->updateMutation($root, $args, $context, $resolveInfo, $getSelectFields);
    }
}
