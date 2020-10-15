<?php

namespace Pepper\Extra\Auth;

use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Hash;

class Register
{
    /**
     * Register args.
     *
     * @return array
     */
    public static function getArgs(): array
    {
        return [
            'name' => ['name' => 'name', 'type' => Type::string()],
            'email' => ['name' => 'email', 'type' => Type::string()],
            'password' => ['name' => 'password', 'type' => Type::string()],
            'password_confirmation' => ['name' => 'password_confirmation', 'type' => Type::string()],
        ];
    }

    /**
     * Resolve register user.
     *
     * @param  array  $args
     * @param  mixed  $user
     * @return mixed
     */
    public static function getResolve(array $args, $user)
    {
        return $user::create([
            'name' => $args['name'],
            'email' => $args['email'],
            'password' => Hash::make($args['password']),
        ]);
    }

    /**
     * Authorize register request.
     *
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
     * Authorization message.
     *
     * @return string
     */
    public static function getAuthorizationMessage(): string
    {
        return 'validation error';
    }

    /**
     * Register validation rules.
     *
     * @return array
     */
    public static function getRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'confirmed'],
        ];
    }
}
