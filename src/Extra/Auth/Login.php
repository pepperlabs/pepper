<?php

namespace Pepper\Extra\Auth;

use GraphQL\Type\Definition\Type;

class Login
{
    /**
     * @return array
     */
    public static function getArgs(...$fields): array
    {
        return [
            self::getUsernameField($fields[0]) => ['name' => self::getUsernameField($fields[0]), 'type' => Type::string()],
            'password' => ['name' => 'password', 'type' => Type::string()],
        ];
    }

    /**
     * @return string
     */
    public static function getUsernameField($instance): string
    {
        return $instance->overrideMethod(
            'setLoginUsernameField',
            function () {
                return 'email';
            }
        );
    }
}
