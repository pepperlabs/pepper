<?php

namespace Pepper;

use Closure;
use HaydenPierce\ClassFinder\ClassFinder;

class Middleware
{
    /**
     * Get all classes in the Pepper root namespace.
     *
     * @return array
     */
    private function getPepperClasses(): array
    {
        $classes = [];
        $peppers = config('pepper.namespace.root').'\Http\Pepper';
        foreach (ClassFinder::getClassesInNamespace($peppers) as $class) {
            $classes[] = $class;
        }

        return $classes;
    }

    /**
     * Register each GraphQL class.
     *
     * @param  string  $parent
     * @param  string  $pepper
     * @param  string  $type
     * @param  string  $key
     * @return void
     */
    private function registerPepperGraphQLClass(string $parent, string $pepper, string $type, string $key)
    {
        /**
         * Replace studly and snake cases with the token provided in the config
         * file. these names can be changed in the config('pepper.available').
         */
        $instance = new $pepper;
        $key = str_replace('{{studly}}', $instance->studly(), $key);
        $key = str_replace('{{snake}}', $instance->snake(), $key);

        /**
         * Create a new anonymous class for the given parent and pepper and then
         * get its class namespace.
         */
        $graphql = MockGraphQL::graphQL($pepper, $parent);
        $graphqlClass = get_class($graphql);

        /**
         * We have to define alias for anonymous classes in order to be able to
         * define multiple classes on the fly using anonymous classes. if not
         * we would refere to the last defined anonymouse class for everyone.
         */
        $alias = $parent.'@'.$pepper.'@'.$type.'@'.$key;
        if (! class_exists($alias)) {
            class_alias($graphqlClass, $alias);
        }

        /**
         * Add the key and value of the newly created class to config('graphql').
         */
        if ($type == 'query') {
            config(['graphql.schemas.default.query.'.$key => $alias]);
        } elseif ($type == 'mutation') {
            config(['graphql.schemas.default.mutation.'.$key => $alias]);
        } else {
            // type, Input, enum, union, scalar
            config(['graphql.types.'.$key => $alias]);
        }

        /**
         * Finally we have to tell the alias how instantiate. IoC would bind the
         * pepper and parent to it.
         */
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
