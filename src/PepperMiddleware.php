<?php

namespace Pepper;

use Closure;
use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Support\Str;
use Pepper\GraphQL\Queries\Query;

class PepperMiddleware
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
        $graphql = new class($pepper, $parent) extends AjiMaji {
        };
        $graphqlClass = get_class($graphql);
        $resolve = new $graphql($pepper, $parent);
        app()->instance($graphqlClass, $resolve);

        $instance = new $pepper;
        $key = Str::of($key)
                  ->replace('{{studly}}', $instance->getStudly())
                  ->replace('{{snake}}', $instance->getSnake());

        if ($type == 'Queries') {
            config(['graphql.schemas.default.query.'.$key => $graphqlClass]);
        } elseif ($type == 'Mutations') {
            config(['graphql.schemas.default.mutation.'.$key => $graphqlClass]);
        } else {
            // Types, Inputs
            config(['graphql.types'.$key => $graphqlClass]);
        }
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
        $available = [
            'Types' => [
                // '{{studly}}ResultAggregateType' => \Pepper\GraphQL\Types\ResultAggregateType::class,
                // '{{studly}}FieldAggregateUnresolvableType' => \Pepper\GraphQL\Types\FieldAggregateUnresolvableType::class,
                // '{{studly}}FieldAggregateType' => \Pepper\GraphQL\Types\FieldAggregateType::class,
                // '{{studly}}AggregateType' => \Pepper\GraphQL\Types\AggregateType::class,
                '{{studly}}Type' => \Pepper\GraphQL\Types\Type::class,
            ],
            // 'Mutations' => [
            //     '{{snake}}_update' => \Pepper\GraphQL\Mutations\UpdateMutation::class,
            //     '{{snake}}_insert' => \Pepper\GraphQL\Mutations\InsertMutation::class,
            //     '{{snake}}_delete' => \Pepper\GraphQL\Mutations\DeleteMutation::class,
            //     'update_{{snake}}_by_pk' => \Pepper\GraphQL\Mutations\UpdateByPkMutation::class,
            //     'delete_{{snake}}_by_pk' => \Pepper\GraphQL\Mutations\DeleteByPkMutation::class,
            //     'insert_{{snake}}_one' => \Pepper\GraphQL\Mutations\InsertOneMutation::class,
            // ],
            'Queries' => [
                // '{{snake}}_by_pk' => \Pepper\GraphQL\Queries\ByPkQuery::class,
                // '{{snake}}_aggregate' => \Pepper\GraphQL\Queries\AggregateQuery::class,
                '{{snake}}' => \Pepper\GraphQL\Queries\Query::class,
            ],
            'Inputs' => [
                // '{{studly}}MutationInput' => \Pepper\GraphQL\Inputs\MutationInput::class,
                // '{{studly}}OrderInput' => \Pepper\GraphQL\Inputs\OrderInput::class,
                '{{studly}}Input' => \Pepper\GraphQL\Inputs\Input::class,
            ],
        ];

        config(['graphql.types.AllUnion' => \Pepper\AllUnion::class]);
        config(['graphql.types.AnyScalar' => \Pepper\AnyScalar::class]);
        config(['graphql.types.OrderByEnum' => \Pepper\OrderByEnum::class]);
        config(['graphql.types.ConditionInput' => \Pepper\ConditionInput::class]);

        foreach ($this->getPepperClasses() as $pepper) {
            foreach ($available as $type => $types) {
                foreach ($types as $key => $parent) {
                    $this->registerPepperGraphQLClass($parent, $pepper, $type, $key);
                }
            }
        }

        // die();

        // foreach ($classes as $pepper) {
        //     // $pepper = ('\\'.$pepper);
        //     $instance = new $pepper;
        //     // logger($pepper);
        //     /**
        //      * I wish I could have a syntax like this:
        //      * $input = new class($pepper) extends Input;
        //      * extremely useful for anonymouse classes.
        //      */
        //     $input = new class($pepper) extends \Pepper\GraphQL\Inputs\Input {
        //     };
        //     config(['graphql.types'.$instance->getStudly().'Input' => get_class($input)]);
        //     config(['graphql.types.UserMutationInput' => \App\GraphQL\Inputs\Pepper\UserMutationInput::class]);
        //     config(['graphql.types.UserOrderInput' => \App\GraphQL\Inputs\Pepper\UserOrderInput::class]);
        //     config(['graphql.types.UserInput' => \App\GraphQL\Inputs\Pepper\UserInput::class]);
        //     config(['graphql.types.UserResultAggregateType' => \App\GraphQL\Types\Pepper\UserResultAggregateType::class]);
        //     config(['graphql.types.UserFieldAggregateUnresolvableType' => \App\GraphQL\Types\Pepper\UserFieldAggregateUnresolvableType::class]);
        //     config(['graphql.types.UserFieldAggregateType' => \App\GraphQL\Types\Pepper\UserFieldAggregateType::class]);
        //     config(['graphql.types.UserAggregateType' => \App\GraphQL\Types\Pepper\UserAggregateType::class]);
        //     config(['graphql.types.UserType' => \App\GraphQL\Types\Pepper\UserType::class]);

        //     $parent = \Pepper\GraphQL\Queries\Query::class;
        //     $query = new class($pepper, $parent) extends AjiMaji {
        //     };
        //     $api = new $query($pepper, $parent);
        //     app()->instance(get_class($query), $api);
        //     config(['graphql.schemas.default.query.'.$instance->getSnake() => get_class($query)]);

        //     // config(['graphql.schemas.default.query.user_by_pk' => \App\GraphQL\Queries\Pepper\UserByPkQuery::class]);
        //     // config(['graphql.schemas.default.query.user_aggregate' => \App\GraphQL\Queries\Pepper\UserAggregateQuery::class]);

        //     // config(['graphql.schemas.default.mutation.update_user_by_pk' => \App\GraphQL\Mutations\Pepper\UserUpdateByPkMutation::class]);
        //     // config(['graphql.schemas.default.mutation.update_user' => \App\GraphQL\Mutations\Pepper\UserUpdateMutation::class]);
        //     // config(['graphql.schemas.default.mutation.insert_user_one' => \App\GraphQL\Mutations\Pepper\UserInsertOneMutation::class]);
        //     // config(['graphql.schemas.default.mutation.insert_user' => \App\GraphQL\Mutations\Pepper\UserInsertMutation::class]);
        //     // config(['graphql.schemas.default.mutation.delete_user_by_pk' => \App\GraphQL\Mutations\Pepper\UserDeleteByPkMutation::class]);
        //     // config(['graphql.schemas.default.mutation.delete_user' => \App\GraphQL\Mutations\Pepper\UserDeleteMutation::class]);
        // }

        return $next($request);
    }
}
