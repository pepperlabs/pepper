<?php

namespace Pepper\Mutations;

use GraphQL\Type\Definition\Type;
use Pepper\Supports\GraphQL as PepperGraphQL;
use Pepper\Supports\Resolve;
use Rebing\GraphQL\Support\Facades\GraphQL;

class DeleteByPkMutation
{
    use PepperGraphQL, Resolve;

    /**
     * Get delete by PK mutation name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name().'DeleteByPkMutation';
    }

    /**
     * Get delete by PK mutation description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->name().' DeleteByPk mutation description.';
    }

    /**
     * Get delete by PK mutation type.
     *
     * @return Type
     */
    public function getType(): Type
    {
        return GraphQL::type($this->getName());
    }

    /**
     * Get delete by PK mutation fields.
     *
     * @return array
     */
    public function getArgs(): array
    {
        $pk = $this->pk();

        return [
            $pk => [
                'name' => $pk,
                'type' => $this->callGraphQLType($pk),
            ],
        ];
    }

    /**
     * Delete by PK mutation.
     *
     * @param  object $root
     * @param  array $args
     * @param  object $context
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @param  \Closure  $getSelectFields
     * @return object
     */
    public function getResolve($root, $args, $context, $resolveInfo, $getSelectFields)
    {
        $pk = $this->pk();

        $builder = $this->model()::where($pk, $args[$pk]);

        $resolve = $this->getQueryResolve($builder, $args, $context, $resolveInfo, $getSelectFields)
                        ->first();

        $builder->delete();

        return $resolve;
    }
}
