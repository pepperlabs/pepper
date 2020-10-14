<?php

namespace Pepper\Extra\Auth;

use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Hash;

class Register
{
    /**
     * Default login args.
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

    public static function getResolve($args, $user)
    {
        return $user::create([
            'name' => $args['name'],
            'email' => $args['email'],
            'password' => Hash::make($args['password']),
        ]);
    }

    public static function getAuthorize($root, $args, $ctx, $resolveInfo, $getSelectFields)
    {
        return true;
    }

    public static function getAuthorizationMessage()
    {
        return 'Validation error';
    }

    public static function getRules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'confirmed'],
        ];
    }
}
