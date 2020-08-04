<?php

namespace Pepper\Helpers;

use Rebing\GraphQL\Support\Facades\GraphQL;
use GraphQL\Type\Definition\Type;

trait GraphQLMutation
{
    /**
     * Get GraphQL Mutation name.
     *
     * @return string
     */
    public function getMutationName(): string
    {
        $method = 'setMutationName';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName() . 'Mutation';
        }
    }

    public function getMutationDescription(): string
    {
        $method = 'setMutationDescription';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName() . ' mutation description.';
        }
    }

    public function getMutationType(): Type
    {
        return Type::listOf(GraphQL::type($this->getTypeName()));
    }

    public function getMutationFields(): array
    {
        $fields = [];

        // Get fields excluded relations
        foreach ($this->getFields(false) as $attribute) {
            $fields[$attribute] = [
                'name' => $attribute,
                'type' => GraphQL::type('OrderByEnum')
            ];
        }

        return $fields;
    }
}
