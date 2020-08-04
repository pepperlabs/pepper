<?php

namespace Pepper\Helpers;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

trait GraphQLAggregate
{
    public function getAggregatedFields(): array
    {
        $fields = [];

        foreach ($this->getFields(false) as $attribute) {
            $fields[$attribute . '_aggregate'] = [
                'name' => $attribute . '_aggregate',
                'type' => GraphQL::type('FieldAggregateType'),
                'selectable' => false,
                'resolve' => function ($root, $args) {
                    return [];
                }
            ];
        }

        return $fields;
    }
}
