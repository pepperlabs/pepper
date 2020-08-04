<?php

namespace Pepper\Helpers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphOneOrMany;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Rebing\GraphQL\Support\Facades\GraphQL;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Str;
use Closure;

trait GraphQLQuery
{
    /**
     * Get GraphQL Query name.
     *
     * @return string
     */
    public function getQueryName(): string
    {
        $method = 'setQueryName';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName() . 'Query';
        }
    }

    /**
     * Get GraphQL Query description.
     *
     * @return string
     */
    public function getQueryDescription(): string
    {
        $method = 'setQueryDescription';
        if (method_exists($this, $method)) {
            $this->$method($this->getClassName);
        } else {
            return $this->getName() . ' query description.';
        }
    }

    /**
     * Get GraphQL Query resolve.
     *
     * @param  object $root
     * @param  array $args
     * @param  object $context
     * @param  ResolveInfo $resolveInfo
     * @param  Closure $getSelectFields
     * @return object
     */
    public function getQueryResolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): object
    {
        return $this->newModel()->when(isset($args['where']), function ($query) use (&$args) {
            foreach ($args['where'] as $field => $criteria) {
                foreach ($criteria as $operation => $value) {
                    switch ($operation) {
                        case '_eq':
                            $query = $query->where($field, '=', $value);
                            break;

                        case '_neq':
                            $query = $query->where($field, '!=', $value);
                            break;

                        case '_gt':
                            $query = $query->where($field, '>', $value);
                            break;

                        case '_lt':
                            $query = $query->where($field, '<', $value);
                            break;

                        case '_gte':
                            $query = $query->where($field, '>=', $value);
                            break;

                        case '_lte':
                            $query = $query->where($field, '<=', $value);
                            break;

                        case '_in':
                            $query = $query->whereIn($field, $value);
                            break;

                        case '_nin':
                            $query = $query->whereNotIn($field, $value);
                            break;

                        case '_like':
                            $query = $query->where($field, 'like', $value);
                            break;

                        case '_nlike':
                            $query = $query->where($field, 'not like', $value);
                            break;

                        case '_ilike':
                            $query = $query->where($field, 'ilike', $value);
                            break;

                        case '_nilike':
                            $query = $query->where($field, 'not ilike', $value);
                            break;

                        case '_is_null':
                            $query = $value ? $query->whereNull($field) : $query->whereNotNull($field);
                            break;

                        case '_date':
                            $query = $query->whereDate($field, $value);
                            break;

                        case '_month':
                            $query = $query->whereMonth($field, $value);
                            break;

                        case '_day':
                            $query = $query->whereDay($field, $value);
                            break;

                        case '_year':
                            $query = $query->whereYear($field, $value);
                            break;

                        case '_time':
                            $query = $query->whereTime($field, $value);
                            break;

                        default:
                            break;
                    }
                }
            }
            return $query;
        })
            ->when(isset($args['limit']), function ($query) use (&$args) {
                return $query->limit($args['limit']);
            })
            ->when(isset($args['offset']), function ($query) use (&$args) {
                return $query->offset($args['offset']);
            })
            ->when(isset($args['skip']), function ($query) use (&$args) {
                return $query->skip($args['skip']);
            })
            ->when(isset($args['take']), function ($query) use (&$args) {
                return $query->take($args['take']);
            })->when(isset($args['order_by']), function ($query) use (&$args) {
                foreach ($args['order_by'] as $column => $direction) {
                    $query = $query->orderBy($column, $direction);
                }
                return $query;
            })->get();
    }

    public function getQueryArgs()
    {
        return [
            // Condition
            'where' => ['type' => GraphQL::type($this->getInputName())],

            'distinct' => ['name' => 'distinct', 'type' => Type::boolean()],

            // Order
            'order_by' => ['type' => GraphQL::type($this->getOrderName())],

            // Paginate
            'limit' => ['name' => 'limit', 'type' => Type::int()],
            'offset' => ['name' => 'offset', 'type' => Type::int()],
            'skip' => ['name' => 'skip', 'type' => Type::int()],
            'take' => ['name' => 'take', 'type' => Type::int()],
        ];
    }

    /**
     * Get GraphQL query type.
     *
     * @return void
     */
    public function getQueryType(): Type
    {
        return Type::listOf(GraphQL::type($this->getTypeName()));
    }

    /**
     * Get graphQL query relations.
     *
     * @return array
     */
    public function getQueryRelations(): array
    {
        $fields = [];
        foreach ($this->exposedRelations() as $relation) {
            $model = $this->newModelReflection();
            $relationType = $model->getMethod($relation)->getReturnType()->getName();
            $type = '';
            if ($relationType === BelongsTo::class) {
                $type = GraphQL::type($this->getTypeName());
            } elseif (in_array($relationType, [
                BelongsToMany::class,
                HasMany::class,
                HasManyThrough::class,
                HasOne::class,
                HasOneOrMany::class,
                HasOneThrough::class,
                MorphMany::class,
                MorphOne::class,
                MorphOneOrMany::class,
                MorphPivot::class,
                MorphTo::class,
                MorphToMany::class
            ])) {
                $type = Type::listOf(GraphQL::type($this->getTypeName()));
            }

            $fields[$relation] = [
                'name' => $relation,
                'type' => $type,
                'resolve' => function ($root, $args) use ($relation) {
                    $method = 'set' . Str::of($relation)->studly() . 'Relation';
                    if (method_exists($this, $method)) {
                        $this->$method($root, $args);
                    } else {
                        return $root->$relation();
                    }
                }
            ];
        }

        return $fields;
    }
}
