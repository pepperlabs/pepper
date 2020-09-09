<?php

namespace Pepper;

use Closure;
use HaydenPierce\ClassFinder\ClassFinder;

class Middleware
{
    private function getPepperClasses()
    {
        $classes = [];
        $peppers = config('pepper.namespace.root').'\Http\Pepper';
        foreach (ClassFinder::getClassesInNamespace($peppers) as $class) {
            $classes[] = $class;
        }

        return $classes;
    }

    private function registerPepperGraphQLClass(string $parent, string $pepper, string $type, string $key)
    {
        $instance = new $pepper;
        $key = str_replace('{{studly}}', $instance->studly(), $key);
        $key = str_replace('{{snake}}', $instance->snake(), $key);

        $graphql = MockGraphQL::graphQL($pepper, $parent);
        $graphqlClass = get_class($graphql);

        $alias = $parent.'@'.$pepper.'@'.$type.'@'.$key;
        if (! class_exists($alias)) {
            class_alias($graphqlClass, $alias);
        }

        if ($type == 'Queries') {
            config(['graphql.schemas.default.query.'.$key => $alias]);
        } elseif ($type == 'Mutations') {
            config(['graphql.schemas.default.mutation.'.$key => $alias]);
        } else {
            // Types, Inputs, Enums, Unions, Scalars
            config(['graphql.types.'.$key => $alias]);
        }

        app()->singleton($alias, function () use ($graphql, $pepper, $parent) {
            return  new $graphql($pepper, $parent);
        });
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // register global classes
        foreach (config('pepper.global') as $key => $value) {
            config(['graphql.types.'.$key => $value]);
        }

        // register generated classes
        foreach ($this->getPepperClasses() as $pepper) {
            foreach (config('pepper.available') as $type => $types) {
                foreach ($types as $key => $parent) {
                    $this->registerPepperGraphQLClass($parent, $pepper, $type, $key);
                }
            }
        }

        return $next($request);
    }
}
