<?php

namespace Pepper\Helpers;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

trait GraphQLAggregate
{
    public function getAggregatedFields(): array
    {
        $fields = [];

        foreach ($this->getFields() as $attribute) {
            $fields[$attribute . '_aggregate'] = [
                'name' => $attribute . '_aggregate',
                'type' => GraphQL::type($this->getName() . 'FieldAggregateType'),
                'selectable' => false,
                'resolve' => function ($root, $args) {
                    return [];
                }
            ];
        }

        return $fields;
    }

    public function getFieldAggregateName(): string
    {
        return $this->getName() . 'FieldAggregateType';
    }

    public function getFieldAggregateDescription(): string
    {
        return $this->getName() . ' field aggregate type description';
    }

    public function getAggregateName(): string
    {
        return $this->getName() . 'AggregateType';
    }

    public function getAggregateDescription(): string
    {
        return $this->getName() . ' aggregate type description';
    }
}
