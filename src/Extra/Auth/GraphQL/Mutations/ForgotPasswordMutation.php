<?php

declare(strict_types=1);

namespace Pepper\Extra\Auth\GraphQL\Mutations;

use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Password;
use Pepper\Extra\Auth\ForgotPassword;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class ForgotPasswordMutation extends Mutation
{
    protected $attributes = [
        'name' => 'forgot_password',
        'description' => 'forgot password mutation',
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
        return GraphQL::type('ForgotPasswordStatusType');
    }

    public function args(): array
    {
        return $this->instance->overrideMethod(
            'setForogotPasswordArgs',
            [ForgotPassword::class, 'getArgs']
        );
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $status = Password::sendResetLink(
            ['email' => $args['email']],
            function ($user, $token) {
                ResetPassword::createUrlUsing(function ($notifiable, $token) {
                    return sprintf(
                        '%s/%s/?token=%s&email=%s',
                        config('pepper.frontend_url'),
                        config('pepper.auth.password_reset'),
                        $token,
                        $notifiable->getEmailForPasswordReset(),
                    );
                });

                return $user->notify(new ResetPassword($token));
            }
        );

        if ($status === Password::RESET_LINK_SENT) {
            return ['status' => __($status)];
        } else {
            throw new Error(__($status));
        }
    }

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        return $this->instance->overrideMethod(
            'setForgotPasswordAuthorize',
            [ForgotPassword::class, 'getAuthorize'],
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
            'setForgotPasswordAuthorizationMessage',
            [ForgotPassword::class, 'getAuthorizationMessage'],
        );
    }

    protected function rules(array $args = []): array
    {
        return $this->instance->overrideMethod(
            'setForgotPasswordRules',
            [ForgotPassword::class, 'getRules'],
            $args,
        );
    }
}
