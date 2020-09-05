<?php

namespace Pepper\Mutations;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pepper\GraphQL as PepperGraphQL;
use Rebing\GraphQL\Support\Facades\GraphQL;

class InsertOne extends PepperGraphQL
{
    /**
    * Get insert one mutation name.
    *
    * @return string
    */
    public function getInsertOneMutationName(): string
    {
        $method = 'setInsertOneMutationName';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName().'InsertOneMutation';
        }
    }

    /**
         * Get insert one mutation description.
         *
         * @return string
         */
    public function getInsertOneMutationDescription(): string
    {
        $method = 'setInsertOneMutationDescription';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName().' insert mutation description.';
        }
    }

    /**
     * Get mutation insert one fields.
     *
     * @return array
     */
    public function getMutationInsertOneFields(): array
    {
        return [
            'object' => [
                'name' => 'object',
                'type' => GraphQL::type($this->getInputMutationName()),
            ],
        ];
    }

    /**
         * Resolve mutation insert one.
         *
         * @param  object  $root
         * @param  array  $args
         * @param  object  $context
         * @param  ResolveInfo  $resolveInfo
         * @param  Closure  $getSelectFields
         * @return object
         */
    public function resolveMutationInsertOne($root, $args, $context, $resolveInfo, $getSelectFields)
    {
        $id = $this->model()::create($args['object'])->id;

        $root = $this->model()->whereIn('id', [$id]);

        return $this->getQueryResolve($root, $args, $context, $resolveInfo, $getSelectFields)
                    ->first();
    }

    /**
     * Get mutation insert one type.
     *
     * @return Type
     */
    public function getMutationInsertOneType(): Type
    {
        return GraphQL::type($this->getTypeName());
    }
}
