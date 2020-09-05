<?php

namespace Pepper\Mutations;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pepper\GraphQL as PepperGraphQL;
use Rebing\GraphQL\Support\Facades\GraphQL;

class DeleteByPk extends PepperGraphQL
{
    /**
     * Get delete by PK mutation name.
     *
     * @return string
     */
    public function getDeleteByPkMutationName(): string
    {
        return $this->getName().'DeleteByPkMutation';
    }

    /**
     * Get delete by PK mutation description.
     *
     * @return string
     */
    public function getDeleteByPkMutationDescription(): string
    {
        return $this->getName().' DeleteByPk mutation description.';
    }

    /**
     * Delete by PK mutation.
     *
     * @param  object $root
     * @param  array $args
     * @param  object $context
     * @param  ResolveInfo $resolveInfo
     * @param  Closure $getSelectFields
     * @return object
     */
    public function deleteByPkMutation($root, $args, $context, $resolveInfo, $getSelectFields)
    {
        $pk = $this->pk();

        $builder = $this->model()::where($pk, $args[$pk]);

        $resolve = $this->getQueryResolve($builder, $args, $context, $resolveInfo, $getSelectFields)
                        ->first();

        $builder->delete();

        return $resolve;
    }

    /**
     * Get delete by PK mutation type.
     *
     * @return Type
     */
    public function getDeleteByPKMutationType(): Type
    {
        return GraphQL::type($this->getTypeName());
    }

    /**
     * Get delete by PK mutation fields.
     *
     * @return array
     */
    public function getDeleteByPkMutationFields(): array
    {
        $pk = $this->pk();

        return [
            $pk => [
                'name' => $pk,
                'type' => $this->callGraphQLType($pk),
            ],
        ];
    }
}
