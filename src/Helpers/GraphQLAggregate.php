<?php

namespace Pepper\Helpers;

use Closure;
use Illuminate\Support\Str;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\Facades\GraphQL;

trait GraphQLAggregate
{
    private function getFieldAggregateRelationType($method)
    {
        $override = 'set' . Str::of($method)->studly() . 'FieldAggregateRelationType';
        if (method_exists($this, $override)) {
            return $this->$override();
        } else {
            $guess = Str::of($method)->singular()->studly();
            return GraphQL::type($guess . 'FieldAggregateType');
        }
    }

    public function getAggregatedFields(): array
    {
        $fields = [];

        $relations = $this->getRelations();
        foreach ($this->getFields() as $attribute) {
            if (in_array($attribute, $relations)) {
                $type = $this->getFieldAggregateRelationType($attribute);
            } else {
                $type = GraphQL::type($this->getName() . 'FieldAggregateType');
            }
            $fields[$attribute . '_aggregate'] = [
                'name' => $attribute . '_aggregate',
                'type' => $type,
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

    public function resolveCountAggregate($root, $args, $context, $resolveInfo)
    {
        $method = 'resolve' . Str::of($root['name'])->studly() . 'CountAggregate';
        if (method_exists($this, $method)) {
            return $this->$method($root, $args, $context, $resolveInfo);
        }

        if (method_exists($root['root'], $root['name'])) {
            return $root['root']->{$root['name']}->count();
        } else {
            return 1;
        }
    }

    public function resolveSumAggregate($root, $args, $context, $resolveInfo)
    {
        $method = 'resolve' . Str::of($root['name'])->studly() . 'SumAggregate';
        if (method_exists($this, $method)) {
            return $this->$method($root, $args, $context, $resolveInfo);
        }

        $result = [];
        $result['__type'] = $this->getFragmentType($resolveInfo);
        foreach ($resolveInfo->getFieldSelection() as $field => $key) {
            $result[$field] = $root['root']->{$root['name']}->sum($field);
        }
        return $result;
    }

    private function getFragmentType($resolveInfo)
    {
        $pos = $resolveInfo->operation->name->loc->startToken;
        while ($pos->next->value != array_reverse($resolveInfo->path)[2]) {
            $pos = $pos->next;
        }
        while ($pos->next->kind != '...') {
            $pos = $pos->next;
        }
        $fragmentName = $pos->next->next->value;
        return $resolveInfo->fragments[$fragmentName]->typeCondition->name->value;
    }

    public function resolveAvgAggregate($root, $args, $context, $resolveInfo)
    {
        $method = 'resolve' . Str::of($root['name'])->studly() . 'AvgAggregate';
        if (method_exists($this, $method)) {
            return $this->$method($root, $args, $context, $resolveInfo);
        }

        $result = [];
        $result['__type'] = $this->getFragmentType($resolveInfo);
        foreach ($resolveInfo->getFieldSelection() as $field => $key) {
            $result[$field] = $root['root']->{$root['name']}->avg($field);
        }
        return $result;
    }

    public function resolveMaxAggregate($root, $args, $context, $resolveInfo)
    {
        $method = 'resolve' . Str::of($root['name'])->studly() . 'MaxAggregate';
        if (method_exists($this, $method)) {
            return $this->$method($root, $args, $context, $resolveInfo);
        }

        $result = [];
        $result['__type'] = $this->getFragmentType($resolveInfo);
        foreach ($resolveInfo->getFieldSelection() as $field => $key) {
            $result[$field] = $root['root']->{$root['name']}->max($field);
        }
        return $result;
    }

    public function resolveMinAggregate($root, $args, $context, $resolveInfo)
    {
        $method = 'resolve' . Str::of($root['name'])->studly() . 'MinAggregate';
        if (method_exists($this, $method)) {
            return $this->$method($root, $args, $context, $resolveInfo);
        }

        $result = [];
        $result['__type'] = $this->getFragmentType($resolveInfo);
        foreach ($resolveInfo->getFieldSelection() as $field => $key) {
            $result[$field] = $root['root']->{$root['name']}->min($field);
        }
        return $result;
    }

    public function getAvailableAggregators()
    {
        return [
            'count' => [
                'type' => Type::int(),
                'selectable' => false,
                'resolve' => function ($root, $args, $context, ResolveInfo $resolveInfo) {
                    return $this->resolveCountAggregate($root, $args, $context, $resolveInfo);
                }
            ],
            'sum' => [
                'type' => GraphQL::type('AllUnion'),
                'selectable' => false,
                'resolve' => function ($root, $args, $context, ResolveInfo $resolveInfo) {
                    return $this->resolveSumAggregate($root, $args, $context, $resolveInfo);
                }
            ],
            'avg' => [
                'type' => GraphQL::type('AllUnion'),
                'selectable' => false,
                'resolve' => function ($root, $args, $context, ResolveInfo $resolveInfo) {
                    return $this->resolveAvgAggregate($root, $args, $context, $resolveInfo);
                },
            ],
            'max' => [
                'type' => GraphQL::type('AllUnion'),
                'selectable' => false,
                'resolve' => function ($root, $args, $context, ResolveInfo $resolveInfo) {
                    return $this->resolveMaxAggregate($root, $args, $context, $resolveInfo);
                },
            ],
            'min' => [
                'type' => GraphQL::type('AllUnion'),
                'selectable' => false,
                'resolve' => function ($root, $args, $context, ResolveInfo $resolveInfo) {
                    return $this->resolveMinAggregate($root, $args, $context, $resolveInfo);
                },
            ]
        ];
    }
}
