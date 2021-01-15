<?php

declare(strict_types=1);

namespace Pepper\Extra\Auth\GraphQL\Mutations;

use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Pepper\Extra\Auth\ResetPassword;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Str;

class ResetPasswordMutation extends Mutation
{
    protected $attributes = [
        'name' => 'reset_password',
        'description' => 'reset password mutation',
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
        return GraphQL::type('ResetPasswordStatusType');
    }

    public function args(): array
    {
        return $this->instance->overrideMethod(
            'setResetPasswordArgs',
            [ResetPassword::class, 'getArgs']
        );
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $status = Password::reset(
            [
                'email' => $args['email'],
                'password' => $args['password'],
                'password_confirmation' => $args['password_confirmation'],
                'token' => $args['token'],
            ],
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();

                $user->setRememberToken(Str::random(60));

                event(new PasswordReset($user));
            }
        );

        return $status == Password::PASSWORD_RESET
                    ? ['status' => __($status)]
                    : throw new Error(__($status));
    }

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        return $this->instance->overrideMethod(
            'setResetPasswordAuthorize',
            [ResetPassword::class, 'getAuthorize'],
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
            'setResetPasswordAuthorizationMessage',
            [ResetPassword::class, 'getAuthorizationMessage'],
        );
    }

    protected function rules(array $args = []): array
    {
        return $this->instance->overrideMethod(
            'setResetPasswordRules',
            [ResetPassword::class, 'getRules'],
            $args,
        );
    }
}
