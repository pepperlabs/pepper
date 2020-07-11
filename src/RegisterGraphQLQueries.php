<?php

namespace Amirmasoud\Pepper;

use Closure;

class RegisterGraphQLQueries
{
    public function handle($request, Closure $next)
    {
        $queries = [];
        $namespace = 'App\GraphQL\Queries\Pepper';
        foreach (glob(app_path() . '/GraphQL/Queries/Pepper/*Query.php') as $path) {
            $class = $namespace . '\\' . str_replace(glob(app_path() . '/GraphQL/Queries/Pepper/'), '', $path);
            $class = preg_replace('/.php$/', '', $class);
            $queries[(new $class)->getAttributes()['name']] = $path;
        }
        app('config')->set('graphql.schemas.default.query', array_merge($queries, config('graphql.schemas.default.query')));

        return $next($request);
    }
}
