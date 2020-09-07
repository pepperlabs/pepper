<?php

namespace Pepper;

use Closure;
use HaydenPierce\ClassFinder\ClassFinder;
use Pepper\GraphQL\Queries\Query;

class PepperMiddleware
{
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
                '{{studly}}ResultAggregateType' => 'ResultAggregateType',
                '{{studly}}FieldAggregateUnresolvableType' => 'FieldAggregateUnresolvableType',
                '{{studly}}FieldAggregateType' => 'FieldAggregateType',
                '{{studly}}AggregateType' => 'AggregateType',
                '{{studly}}Type' => 'Type',
            ],
            'Mutations' => [
                '{{snake}}_update' => 'UpdateMutation',
                '{{snake}}_insert' => 'InsertMutation',
                '{{snake}}_delete' => 'DeleteMutation',
                'update_{{snake}}_by_pk' => 'UpdateByPkMutation',
                'delete_{{snake}}_by_pk' => 'DeleteByPkMutation',
                'insert_{{snake}}_one' => 'InsertOneMutation',
            ],
            'Queries' => [
                '{{snake}}_by_pk' => 'ByPkQuery',
                '{{snake}}_aggregate' => 'AggregateQuery',
                '{{snake}}' => 'Query',
            ],
            'Inputs' => [
                '{{studly}}MutationInput' => 'MutationInput',
                '{{studly}}OrderInput' => 'OrderInput',
                '{{studly}}Input' => 'Input',
            ],
        ];

        $classes = [];
        $models = config('pepper.namespace.root').'\Http\Pepper';
        foreach (ClassFinder::getClassesInNamespace($models) as $class) {
            $classes[] = $class;
        }
        foreach ($classes as $pepper) {
            // $pepper = ('\\'.$pepper);
            $instance = new $pepper;
            // logger($pepper);
            /**
             * I wish I could have a syntax like this:
             * $input = new class($pepper) extends Input;
             * extremely useful for anonymouse classes.
             */
            $input = new class($pepper) extends \Pepper\GraphQL\Inputs\Input {
            };
            config(['graphql.types'.$instance->getStudly().'Input' => get_class($input)]);
            config(['graphql.types.UserMutationInput' => \App\GraphQL\Inputs\Pepper\UserMutationInput::class]);
            config(['graphql.types.UserOrderInput' => \App\GraphQL\Inputs\Pepper\UserOrderInput::class]);
            config(['graphql.types.UserInput' => \App\GraphQL\Inputs\Pepper\UserInput::class]);
            config(['graphql.types.UserResultAggregateType' => \App\GraphQL\Types\Pepper\UserResultAggregateType::class]);
            config(['graphql.types.UserFieldAggregateUnresolvableType' => \App\GraphQL\Types\Pepper\UserFieldAggregateUnresolvableType::class]);
            config(['graphql.types.UserFieldAggregateType' => \App\GraphQL\Types\Pepper\UserFieldAggregateType::class]);
            config(['graphql.types.UserAggregateType' => \App\GraphQL\Types\Pepper\UserAggregateType::class]);
            config(['graphql.types.UserType' => \App\GraphQL\Types\Pepper\UserType::class]);
            config(['graphql.types.AllUnion' => \Pepper\AllUnion::class]);
            config(['graphql.types.AnyScalar' => \Pepper\AnyScalar::class]);
            config(['graphql.types.OrderByEnum' => \Pepper\OrderByEnum::class]);
            config(['graphql.types.ConditionInput' => \Pepper\ConditionInput::class]);

            $query = new class($pepper) extends Query {
                public function __construct($pepper)
                {
                    parent::__construct(($pepper));
                }
            };
            // $api = new $query($pepper);
            // app()->instance(get_class($query), $api);

            // define('FUCK', get_class($query));
            // $fuck = new class extends AjiMaji {
            // };
            // app()->singletonIf($query, function ($pepper) {
            //     return $pepper;
            // });
            // var_dump($query);
            // define('USER', get_class($query));

            // var_dump(new $query);
            // dd(1);

            config(['graphql.schemas.default.query.'.$instance->getSnake() => get_class($query)]);
            // config(['graphql.schemas.default.query.user_by_pk' => \App\GraphQL\Queries\Pepper\UserByPkQuery::class]);
            // config(['graphql.schemas.default.query.user_aggregate' => \App\GraphQL\Queries\Pepper\UserAggregateQuery::class]);

            // config(['graphql.schemas.default.mutation.update_user_by_pk' => \App\GraphQL\Mutations\Pepper\UserUpdateByPkMutation::class]);
            // config(['graphql.schemas.default.mutation.update_user' => \App\GraphQL\Mutations\Pepper\UserUpdateMutation::class]);
            // config(['graphql.schemas.default.mutation.insert_user_one' => \App\GraphQL\Mutations\Pepper\UserInsertOneMutation::class]);
            // config(['graphql.schemas.default.mutation.insert_user' => \App\GraphQL\Mutations\Pepper\UserInsertMutation::class]);
            // config(['graphql.schemas.default.mutation.delete_user_by_pk' => \App\GraphQL\Mutations\Pepper\UserDeleteByPkMutation::class]);
            // config(['graphql.schemas.default.mutation.delete_user' => \App\GraphQL\Mutations\Pepper\UserDeleteMutation::class]);
        }

        return $next($request);
    }
}
