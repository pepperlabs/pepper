<?php

declare(strict_types=1);

namespace Pepper\Extra\Auth\GraphQL\Queries;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pepper\Extra\Auth\Login;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class LoginQuery extends Query
{
    protected $attributes = [
        'name' => 'login',
        'description' => 'login query',
    ];

    protected $instance;

    public function __construct()
    {
        $pepper = config('pepper.namespace.root').'\Http\Pepper\\'.class_basename(config('pepper.auth.model'));
        $this->instance = new $pepper;
    }

    public function type(): Type
    {
        return GraphQL::type('JWTType');
    }

    public function args(): array
    {
        return $this->instance->overrideMethod(
            'setLoginArgs',
            [Login::class, 'getArgs']
        );
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return ['token' => auth()->attempt($args)];
    }

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        return auth()->validate($args);
    }

    public function getAuthorizationMessage(): string
    {
        return 'Authorization failed';
    }
}
