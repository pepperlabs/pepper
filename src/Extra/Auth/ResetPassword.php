<?php

namespace Pepper\Extra\Auth;

use GraphQL\Type\Definition\Type;

class ResetPassword
{
    /**
     * Register args.
     *
     * @return array
     */
    public static function getArgs(): array
    {
        return [
            'email' => ['name' => 'email', 'type' => Type::string()],
            'token' => ['name' => 'token', 'type' => Type::string()],
            'password' => ['name' => 'password', 'type' => Type::string()],
            'password_confirmation' => ['name' => 'password_confirmation', 'type' => Type::string()],
        ];
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder|null  $root
     * @param  array  $args
     * @param  object  $context
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @param  Closure  $getSelectFields
     * @return bool
     */
    public static function getAuthorize($root, $args, $ctx, $resolveInfo, $getSelectFields): bool
    {
        return true;
    }

    /**
     * @return string
     */
    public static function getAuthorizationMessage(): string
    {
        return 'validation error';
    }

    /**
     * @return array
     */
    public static function getRules(): array
    {
        return [
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ];
    }
}
