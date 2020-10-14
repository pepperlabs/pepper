<?php

declare(strict_types=1);

namespace Pepper\Extra\Auth\GraphQL\Mutations;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Auth\Events\Registered;
use Pepper\Extra\Auth\Register;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class RegisterMutation extends Mutation
{
    protected $attributes = [
        'name' => 'register',
        'description' => 'register mutation',
    ];

    protected $instance;

    protected $user;

    public function __construct()
    {
        $pepper = config('pepper.namespace.root').'\Http\Pepper\\'.class_basename(config('pepper.auth.model'));
        $this->instance = new $pepper;
        $this->user = config('pepper.auth.model');
    }

    public function type(): Type
    {
        return GraphQL::type('JWTType');
    }

    public function args(): array
    {
        return $this->instance->overrideMethod(
            'setRegisterArgs',
            [Register::class, 'getArgs']
        );
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $user = $this->instance->overrideMethod(
            'setRegisterResolve',
            [Register::class, 'getResolve'],
            $args,
            $this->user
        );

        event(new Registered($user));

        return ['token' => auth()->attempt($args)];
    }

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        return $this->instance->overrideMethod(
            'setRegisterAuthorize',
            [Register::class, 'getAuthorize'],
            $root,
            $args,
            $ctx,
            $resolveInfo,
            $getSelectFields
        );
    }

    public function getAuthorizationMessage(): string
    {
        return $this->instance->overrideMethod(
            'setRegisterAuthorizationMessage',
            [Register::class, 'getAuthorizationMessage'],
        );
    }

    protected function rules(array $args = []): array
    {
        return $this->instance->overrideMethod(
            'setRegisterRules',
            [Register::class, 'getRules'],
            $args,
        );
    }
}
