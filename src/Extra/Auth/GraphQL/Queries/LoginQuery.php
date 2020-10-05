<?php

declare(strict_types=1);

namespace Pepper\Extra\Auth\GraphQL\Queries;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class LoginQuery extends Query
{
    protected $attributes = [
        'name' => 'login',
        'description' => 'login query',
    ];

    public function type(): Type
    {
        return GraphQL::type('JWTType');
    }

    public function args(): array
    {
        return [
            'email' => ['name' => 'email', 'type' => Type::string()],
            'password' => ['name' => 'password', 'type' => Type::string()],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        dd(auth()->attempt($args));
        return auth()->attempt($args);
    }

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        return auth()->attempt($args);
    }

    public function getAuthorizationMessage(): string
    {
        return 'Authorization failed';
    }
}
