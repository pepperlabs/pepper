<?php

namespace Pepper\Mutations;

use GraphQL\Type\Definition\Type;
use Pepper\Supports\GraphQL as PepperGraphQL;
use Pepper\Supports\Resolve;
use Rebing\GraphQL\Support\Facades\GraphQL;

class DeleteMutation
{
    use PepperGraphQL, Resolve;

    /**
     * Get delete mutation name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name().'DeleteMutation';
    }

    /**
     * Get delete mutation description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->name().' delete mutation description.';
    }

    /**
    * Get delete mutation type.
    *
    * @return Type
    */
    public function getType(): Type
    {
        return Type::listOf(GraphQL::type($this->getName()));
    }

    /**
     * Get delete mutation fields.
     *
     * @return array
     */
    public function getArgs(): array
    {
        return [
            'where' => ['type' => GraphQL::type($this->instance->name().'Input')],
        ];
    }

    /**
     * Delete mutation.
     *
     * @param  object  $root
     * @param  array  $args
     * @param  object  $context
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @param  \Closure  $getSelectFields
     * @return object
     */
    public function getResolve($root, $args, $context, $resolveInfo, $getSelectFields)
    {
        $models = $this->getQueryResolve($this->model(), $args, $context, $resolveInfo, $getSelectFields);

        $results = $models->get();

        $models->delete();

        return $results;
    }
}
