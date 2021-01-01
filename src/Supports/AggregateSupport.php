<?php

namespace Pepper\Supports;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Str;
use Rebing\GraphQL\Support\Facades\GraphQL;

trait AggregateSupport
{
    /**
     * The aggregated field will divide returned json into two fields of nodes
     * which contain actual data - if any - and aggregate field which
     * contains the aggregation data over the data.
     *
     * @param  bool $resolvable
     * @return array
     */
    public function getFieldAggregateTypeFields(bool $resolvable = true): array
    {
        $fields = [
            'aggregate' => [
                'name' => 'aggregate',
                'type' => GraphQL::type($this->getAggregateTypeName()),
                'selectable' => false,
            ],
            'nodes' => [
                'name' => 'nodes',
                'type' => Type::listOf(GraphQL::type($this->getTypeName())),
                'selectable' => false,
            ],
        ];

        return $resolvable
            ? $this->setFieldAggregateTypeResolve($fields)
            : $fields;
    }

    /**
     * Add resolve key to aggregate type.
     *
     * @param  array $fields
     * @return array
     */
    private function setFieldAggregateTypeResolve(array $fields): array
    {
        $fields['aggregate']['resolve'] = function ($root, $args, $context, ResolveInfo $resolveInfo) {
            return $root;
        };

        $fields['nodes']['resolve'] = function ($root, $args, $context, ResolveInfo $resolveInfo) {
            return $root['root']->{$root['name']}()->get();
        };

        return $fields;
    }

    /**
     * Get field aggregate relation type.
     *
     * @param  string $method
     * @return \GraphQL\Type\Definition\Type
     */
    private function getFieldAggregateRelationType($method): Type
    {
        return $this->getRelatedFieldAggregateType($method);
    }

    /**
     * Get related field aggregate type.
     *
     * @param  string $attribute
     * @return \GraphQL\Type\Definition\Type
     */
    public function getRelatedFieldAggregateType(string $attribute): Type
    {
        return GraphQL::type($this->relatedGraphQL($attribute)->getFieldAggregateTypeName());
    }

    /**
     * Get aggregate fields.
     *
     * @return array
     */
    public function getAggregatedFields(): array
    {
        $fields = [];

        $relations = $this->relations();
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

    /**
     * Get result aggregate fields.
     *
     * @return array
     */
    public function getResultAggregateFields(): array
    {
        $fields = [];

        // Get fields excluded relations
        foreach ($this->fieldsArray(false) as $attribute) {
            $fields[$attribute] = [
                'name' => $attribute,
                'type' => GraphQL::type('AnyScalar'),
            ];
        }

        return $fields;
    }

    /**
     * Resolve count function aggregate.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|null  $root
     * @param  array  $args
     * @param  object  $context
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @return int
     */
    public function resolveCountAggregate($root, $args, $context, $resolveInfo): int
    {
        if (method_exists($root['root'], $root['name'])) {
            return $root['root']->{$root['name']}->count();
        } else {
            return $root['root']->count();
        }
    }

    /**
     * Resolve sum function aggregate.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|null  $root
     * @param  array  $args
     * @param  object  $context
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @return array
     */
    public function resolveSumAggregate($root, $args, $context, $resolveInfo): array
    {
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

    /**
     * Get framgmen type.
     *
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @return string
     */
    private function getFragmentType($resolveInfo): string
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

    /**
     * Resolve avg function aggregate.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|null  $root
     * @param  array  $args
     * @param  object  $context
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @return array
     */
    public function resolveAvgAggregate($root, $args, $context, $resolveInfo): array
    {
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

    /**
     * Resolve max function aggregate.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|null  $root
     * @param  array  $args
     * @param  object  $context
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @return array
     */
    public function resolveMaxAggregate($root, $args, $context, $resolveInfo): array
    {
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

    /**
     * Resolve min function aggregate.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|null  $root
     * @param  array  $args
     * @param  object  $context
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @return array
     */
    public function resolveMinAggregate($root, $args, $context, $resolveInfo): array
    {
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

    /**
     * Get available aggregators.
     *
     * @return array
     */
    public function getAvailableAggregators(): array
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

    /**
     * Get query aggregate type.
     *
     * @return \GraphQL\Type\Definition\Type
     */
    public function getQueryAggregateType(): Type
    {
        return GraphQL::type($this->studly().'FieldAggregateUnresolvableType');
    }

    /**
     * Resolve aggregae query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|null  $root
     * @param  array  $args
     * @param  object  $context
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @param  Closure  $getSelectFields
     * @return array
     */
    public function resolveQueryAggregate($root, $args, $context, $resolveInfo, $getSelectFields): array
    {
        $query = $this->getQueryResolve($root, $args, $context, $resolveInfo, $getSelectFields)->get();
        $name = Str::replaceLast('_aggregate', '', array_reverse($resolveInfo->path)[0]);

        return [
            'aggregate' => [
                'root' => $query,
                'name' => $name,
            ],
            'nodes' => $query,
        ];
    }
}
