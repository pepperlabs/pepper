<?php

namespace Pepper\Supports;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Str;
use Rebing\GraphQL\Support\Facades\GraphQL;

trait AggregateSupport
{
    public function getFieldAggregateNodeType($method)
    {
        $override = 'set'.Str::studly($method).'FieldAggregateNodeType';
        if (method_exists($this, $override)) {
            return $this->$override();
        } else {
            return $this->getRelatedFieldAggregateType($method);
        }
    }

    public function getFieldAggregateTypeFields($resolvable = true)
    {
        $fields = [
            'aggregate' => [
                'name' => 'aggregate',
                'type' => GraphQL::type($this->getAggregateName()),
            ],
            'nodes' => [
                'name' => 'nodes',
                'type' => Type::listOf(GraphQL::type($this->getTypeName())),
            ],
        ];

        if ($resolvable) {
            $fields['aggregate']['resolve'] = function ($root, $args, $context, ResolveInfo $resolveInfo) {
                return $root;
            };

            $fields['nodes']['resolve'] = function ($root, $args, $context, ResolveInfo $resolveInfo) {
                return $root['root']->{$root['name']}()->get();
            };
        }

        return $fields;
    }

    private function getFieldAggregateRelationType($method)
    {
        $override = 'set'.Str::studly($method).'FieldAggregateRelationType';
        if (method_exists($this, $override)) {
            return $this->$override();
        } else {
            return $this->getRelatedFieldAggregateType($method);
        }
    }

    public function getRelatedFieldAggregateType($attribute)
    {
        return $this->getRelatedModel($attribute)->getFieldAggregateName();
    }

    public function getAggregatedFields() : array
    {
        $fields = [];

        $relations = $this->getRelations();
        foreach ($relations as $attribute) {
            $fields[$attribute.'_aggregate'] = [
                'name' => $attribute.'_aggregate',
                'type' => $this->getFieldAggregateRelationType($attribute),
                'selectable' => false,
                'args' => $this->getQueryArgs(),
                'resolve' => function ($root, $args, $context, ResolveInfo $resolveInfo) use ($attribute) {
                    return ['root' => $root, 'args' => $args, 'name' => $attribute];
                },
            ];
        }

        return $fields;
    }

    public function getResultAggregateFields() : array
    {
        $fields = [];

        // Get fields excluded relations
        foreach ($this->getFields(false) as $attribute) {
            $fields[$attribute] = [
                'name' => $attribute,
                'type' => GraphQL::type('AnyScalar'),
            ];
        }

        return $fields;
    }

    public function getResultAggregateName() : string
    {
        return $this->getName().'ResultAggregateType';
    }

    public function getResultAggregateDescription() : string
    {
        return $this->getName().' result aggregate type description';
    }

    public function getFieldAggregateName() : string
    {
        return $this->getName().'FieldAggregateType';
    }

    public function getFieldAggregateDescription() : string
    {
        return $this->getName().' field aggregate type description';
    }

    public function getAggregateUnresolvableName() : string
    {
        return $this->getName().'FieldAggregateUnresolvableType';
    }

    public function getAggregateUnresolvableDescription() : string
    {
        return $this->getName().' unresolvable aggregate type description';
    }

    public function getAggregateName() : string
    {
        return $this->getName().'AggregateType';
    }

    public function getAggregateDescription() : string
    {
        return $this->getName().' aggregate type description';
    }

    /**
     * Get GraphQL Query name.
     *
     * @return string
     */
    public function getAggregateQueryName() : string
    {
        $method = 'setAggregateQueryName';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName().'AggregateQuery';
        }
    }

    public function getAggregateQueryDescription() : string
    {
        $method = 'setAggregateQueryDescription';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName().' aggregate query description.';
        }
    }

    public function resolveCountAggregate($root, $args, $context, $resolveInfo)
    {
        $method = 'resolve'.Str::studly($root['name']).'CountAggregate';
        if (method_exists($this, $method)) {
            return $this->$method($root, $args, $context, $resolveInfo);
        }

        if (method_exists($root['root'], $root['name'])) {
            return $root['root']->{$root['name']}->count();
        } else {
            return $root['root']->count();
        }
    }

    public function resolveSumAggregate($root, $args, $context, $resolveInfo)
    {
        $method = 'resolve'.Str::studly($root['name']).'SumAggregate';
        if (method_exists($this, $method)) {
            return $this->$method($root, $args, $context, $resolveInfo);
        }

        $result = [];
        $result['__Union_Type'] = $this->getFragmentType($resolveInfo);
        foreach ($resolveInfo->getFieldSelection() as $field => $key) {
            if (method_exists($root['root'], $root['name'])) {
                $result[$field] = $root['root']->{$root['name']}->sum($field);
            } else {
                $result[$field] = $root['root']->sum($field);
            }
        }

        return $result;
    }

    private function getFragmentType($resolveInfo)
    {
        $operation = $resolveInfo->operation;
        $pos = is_object($operation->name)
            ? $operation->name->loc->startToken
            : $resolveInfo->operation->loc->startToken;
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
        $method = 'resolve'.Str::studly($root['name']).'AvgAggregate';
        if (method_exists($this, $method)) {
            return $this->$method($root, $args, $context, $resolveInfo);
        }

        $result = [];
        $result['__Union_Type'] = $this->getFragmentType($resolveInfo);
        foreach ($resolveInfo->getFieldSelection() as $field => $key) {
            if (method_exists($root['root'], $root['name'])) {
                $result[$field] = $root['root']->{$root['name']}->avg($field);
            } else {
                $result[$field] = $root['root']->avg($field);
            }
        }

        return $result;
    }

    public function resolveMaxAggregate($root, $args, $context, $resolveInfo)
    {
        $method = 'resolve'.Str::studly($root['name']).'MaxAggregate';
        if (method_exists($this, $method)) {
            return $this->$method($root, $args, $context, $resolveInfo);
        }

        $result = [];
        $result['__Union_Type'] = $this->getFragmentType($resolveInfo);
        foreach ($resolveInfo->getFieldSelection() as $field => $key) {
            if (method_exists($root['root'], $root['name'])) {
                $result[$field] = $root['root']->{$root['name']}->max($field);
            } else {
                $result[$field] = $root['root']->max($field);
            }
        }

        return $result;
    }

    public function resolveMinAggregate($root, $args, $context, $resolveInfo)
    {
        $method = 'resolve'.Str::studly($root['name']).'MinAggregate';
        if (method_exists($this, $method)) {
            return $this->$method($root, $args, $context, $resolveInfo);
        }

        $result = [];
        $result['__Union_Type'] = $this->getFragmentType($resolveInfo);
        foreach ($resolveInfo->getFieldSelection() as $field => $key) {
            if (method_exists($root['root'], $root['name'])) {
                $result[$field] = $root['root']->{$root['name']}->min($field);
            } else {
                $result[$field] = $root['root']->min($field);
            }
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
                },
            ],
            'sum' => [
                'type' => GraphQL::type('AllUnion'),
                'selectable' => false,
                'resolve' => function ($root, $args, $context, ResolveInfo $resolveInfo) {
                    return $this->resolveSumAggregate($root, $args, $context, $resolveInfo);
                },
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
            ],
        ];
    }

    public function getQueryAggregateType() : Type
    {
        return GraphQL::type($this->getStudly().'FieldAggregateUnresolvableType');
    }

    public function resolveQueryAggregate($root, $args, $context, $resolveInfo, $getSelectFields)
    {
        $query = $this->getQueryResolve($root, $args, $context, $resolveInfo, $getSelectFields)->get();

        return [
            'aggregate' => ['root' => $query, 'name' => preg_replace('/_aggregate$/', '', array_reverse($resolveInfo->path)[0])],
            'nodes' => $query,
        ];
    }
}
