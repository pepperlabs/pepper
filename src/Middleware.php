<?php

namespace Pepper;

use Closure;
use HaydenPierce\ClassFinder\ClassFinder;
use Pepper\Extra\Cache\Cache;

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
        $peppers = config('pepper.base.namespace.root').'\Http\Pepper';
        $classesInNamespace = Cache::get('pepper:__classes:__list', function () use ($peppers) {
            return ClassFinder::getClassesInNamespace($peppers);
        });
        foreach ($classesInNamespace as $class) {
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
         * file. these names can be changed in the config('pepper.base.available').
         */
        $key = Cache::get('pepper:__class:'.$pepper.':'.$key, function () use ($pepper, $key) {
            $instance = new $pepper;
            $key = str_replace('{{studly}}', $instance->studly(), $key);
            $key = str_replace('{{snake}}', $instance->snake(), $key);

            return $key;
        });

        /**
         * We have to define alias for anonymous classes in order to be able to
         * define multiple classes on the fly using anonymous classes. if not
         * we would refere to the last defined anonymouse class for everyone.
         */
        $alias = $parent.'@'.$pepper.'@'.$type.'@'.$key;
        if (! class_exists($alias)) {
            class_alias(MockGraphQL::class, $alias);
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
        app()->singletonIf($alias, function () use ($alias, $pepper, $parent) {
            return Cache::get('pepper:__class:__'.$alias, function () use ($pepper, $parent) {
                return new $parent($pepper);
            });
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
        $global = config('pepper.base.global');
        $available = config('pepper.base.available');
        if (config('pepper.base.extra.auth')) {
            $global = array_merge_recursive(config('pepper.auth.global'), $global);
            $available = array_merge_recursive(config('pepper.auth.available'), $available);
        }
        // register global classes
        foreach ($global as $key => $value) {
            config(['graphql.types.'.$key => $value]);
        }

        // register generated classes
        foreach ($this->getPepperClasses() as $pepper) {
            foreach ($available as $type => $types) {
                foreach ($types as $key => $parent) {
                    $this->registerPepperGraphQLClass($parent, $pepper, $type, $key);
                }
            }
        }

        return $next($request);
    }
}
