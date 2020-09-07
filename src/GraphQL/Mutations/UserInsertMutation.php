<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\Pepper;

use App;
use Closure;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

use GraphQL\Type\Definition\ResolveInfo;

class UserInsertMutation extends Mutation
{
    protected $attributes = [
        'name' => 'insert_user',
        'description' => 'User insert mutation description'
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
        return $this->instance->getMutationInsertFields();
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->instance->resolveMutationInsert($root, $args, $context, $resolveInfo, $getSelectFields);
    }
}
