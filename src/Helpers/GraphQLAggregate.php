<?php

namespace Pepper\Helpers;

use Closure;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
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
                'args' => $this->getQueryArgs(),
                'resolve' => function ($root, $args, $context, ResolveInfo $resolveInfo) use ($attribute) {
                    return ['root' => $root, 'args' => $args, 'name' => $attribute];
                }
            ];
        }

        return $fields;
    }

    public function getResultAggregateFields(): array
    {
        $fields = [];

        // Get fields excluded relations
        foreach ($this->getFields(false) as $attribute) {
            $fields[$attribute] = [
                'name' => $attribute,
                'type' => GraphQL::type('AnyScalar')
            ];
        }

        return $fields;
    }

    public function getResultAggregateName(): string
    {
        return $this->getName() . 'ResultAggregateType';
    }

    public function getResultAggregateDescription(): string
    {
        return $this->getName() . ' result aggregate type description';
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
