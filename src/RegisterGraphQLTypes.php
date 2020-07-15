<?php

namespace Amirmasoud\Pepper;

use Closure;

class RegisterGraphQLTypes
{
    public static function init()
    {
        $types = [];
        $namespace = 'App\GraphQL\Types\Pepper';
        foreach (glob(app_path() . '/GraphQL/Types/Pepper/*Query.php') as $path) {
            $class = $namespace . '\\' . str_replace(glob(app_path() . '/GraphQL/Types/Pepper/'), '', $path);
            $class = preg_replace('/.php$/', '', $class);
            $types[(new $class)->getAttributes()['name']] = $class;
        }
        app('config')->set('graphql.types', array_merge($types, config('graphql.types')));
    }
}
