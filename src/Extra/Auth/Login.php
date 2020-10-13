<?php

namespace Pepper\Extra\Auth;

use GraphQL\Type\Definition\Type;

class Login
{
    /**
     * Default login args.
     *
     * @return array
     */
    public static function getArgs(): array
    {
        return [
            'email' => ['name' => 'email', 'type' => Type::string()],
            'password' => ['name' => 'password', 'type' => Type::string()],
        ];
    }
}
