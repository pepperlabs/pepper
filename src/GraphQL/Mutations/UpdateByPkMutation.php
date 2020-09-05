<?php

namespace Pepper\Mutations;

use GraphQL\Type\Definition\Type;
use Pepper\Contracts\MutationContract;
use Pepper\GraphQL as PepperGraphQL;
use Rebing\GraphQL\Support\Facades\GraphQL;

class UpdateByPkMutation extends PepperGraphQL implements MutationContract
{
    /**
     * Get update by PK mutation name.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->getName().'UpdateByPkMutation';
    }

    /**
     * Get update by PK mutation description.
     *
     * @return string
     */
    public function description(): string
    {
        return $this->getName().' insert mutation description.';
    }

    /**
     * Get mutation update by PK type.
     *
     * @return Type
     */
    public function type(): Type
    {
        return GraphQL::type($this->getTypeName());
    }

    /**
     * Get mutation update by PK fields.
     *
     * @return array
     */
    public function args(): array
    {
        return [
            'pk_columns' => [
                'name' => 'pk_columns',
                'type' => GraphQL::type($this->getInputMutationName()),
            ],
            '_set' => [
                'name' => '_set',
                'type' => GraphQL::type($this->getInputMutationName()),
            ],
        ];
    }

    /**
     * Update by PK resolve.
     *
     * @param  object $root
     * @param  array $args
     * @param  object $context
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @param  \Closure  $getSelectFields
     * @return object
     */
    public function resolve($root, $args, $context, $resolveInfo, $getSelectFields)
    {
        $pk = $this->model()->getKeyName();

        $builder = $this->modelClass()::where($pk, $args['pk_columns'][$pk]);

        $builder->update($args['_set']);

        return $this->getQueryResolve($builder, $args, $context, $resolveInfo, $getSelectFields)
                    ->first();
    }
}