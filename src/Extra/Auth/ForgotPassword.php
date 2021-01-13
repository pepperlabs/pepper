<?php

namespace Pepper\Extra\Auth;

use GraphQL\Type\Definition\Type;

class ForgotPassword
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
        ];
    }

    /**
     * @param  array  $args
     * @param  mixed  $user
     * @return mixed
     */
    public static function getResolve(array $args, $user)
    {
        return $user::create([
            'email' => $args['email'],
        ]);
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
            'email' => ['required', 'email'],
        ];
    }
}
