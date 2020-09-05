<?php

namespace Pepper\Mutations;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pepper\GraphQL as PepperGraphQL;
use Rebing\GraphQL\Support\Facades\GraphQL;

class Insert extends PepperGraphQL
{
    /**
     * Get insert mutation name.
     *
     * @return string
     */
    public function getInsertMutationName(): string
    {
        return $this->getName().'InsertMutation';
    }

    /**
     * Get insert mutation description.
     *
     * @return string
     */
    public function getInsertMutationDescription(): string
    {
        return $this->getName().' insert mutation description.';
    }

    /**
    * Resolve mutation insert.
    *
    * @param  object  $root
    * @param  array  $args
    * @param  object  $context
    * @param  ResolveInfo  $resolveInfo
    * @param  Closure  $getSelectFields
    * @return object
    */
    public function resolveMutationInsert($root, $args, $context, $resolveInfo, $getSelectFields)
    {
        $ids = [];
        foreach ($args['objects'] as $obj) {
            $ids[] = $this->model()::create($obj)->id;
        }

        $root = $this->model()->whereIn('id', $ids);

        return $this->getQueryResolve($root, $args, $context, $resolveInfo, $getSelectFields)->get();
    }

    /**
     * Get mutation type.
     *
     * @return Type
     */
    public function getInsertMutationType(): Type
    {
        return Type::listOf(GraphQL::type($this->getTypeName()));
    }

    /**
     * Get mutation fields.
     *
     * @return array
     */
    public function getMutationFields(): array
    {
        $fields = [];

        // Get fields excluded relations
        foreach ($this->fieldsArray(false) as $type) {
            $fields[$type] = [
                'name' => $type,
                'type' => $this->callGraphQLType($type),
            ];
        }

        return $fields;
    }

    /**
     * Get mutation insert fields.
     *
     * @return array
     */
    public function getMutationInsertFields(): array
    {
        return [
            'objects' => [
                'name' => 'objects',
                'type' => Type::listOf(GraphQL::type($this->getInputMutationName())),
            ],
        ];
    }
}
