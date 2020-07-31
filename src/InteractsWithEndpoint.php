<?php

namespace Amirmasoud\Pepper;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Illuminate\Support\Facades\DB;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use GraphQL\Type\Definition\ResolveInfo;
use Closure;

trait InteractsWithEndpoint
{
    public function hasEndpoint(): bool
    {
        return $this->endpoint ?? true;
    }

    public function endpointFields(): array
    {
        $exposedAttributes = $this->exposedAttributes ?? $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
        $hiddenAttributes = $this->hiddenAttributes ?? [];
        $attributes = array_values(array_diff($exposedAttributes, $hiddenAttributes));

        $fields = [];
        foreach ($attributes as $attribute) {
            $fields[$attribute] = [
                'name' => $attribute,
                'type' => call_user_func('\GraphQL\Type\Definition\Type::' . $this->guessFieldType($attribute))
            ];
        }
        return $fields;
    }

    public function endpointRelations($model): array
    {
        $relations = [];
        // @TODO refactor to method
        $reflector = new \ReflectionClass($this);
        foreach ($reflector->getMethods() as $reflectionMethod) {
            $returnType = $reflectionMethod->getReturnType();
            if ($returnType) {
                if (in_array(class_basename($returnType->getName()), ['HasOne', 'HasMany', 'BelongsTo', 'BelongsToMany', 'MorphToMany', 'MorphTo'])) {
                    $relations[] = $reflectionMethod->name;
                }
            }
        }

        $exposedRelations = $this->exposedRelations ?? $relations;
        $hiddenRelations = $this->hiddenRelations ?? [];
        $relations = array_values(array_diff($exposedRelations, $hiddenRelations));

        $fields = [];
        foreach ($relations as $relation) {
            $reflector = new \ReflectionClass($model);
            $relationType = $reflector->getMethod($relation)->getReturnType()->getName();
            $type = '';
            if ($relationType === BelongsTo::class) {
                $type = GraphQL::type($this->getTypeName());
            } elseif ($relationType === HasMany::class) {
                $type = Type::listOf(GraphQL::type($this->getTypeName()));
            }

            $fields[$relation] = [
                'name' => $relation,
                'type' => $type,
                'resolve' => function ($root, $args) use ($relation) {
                    return $root->{$relation};
                }
            ];
        }
        return $fields;
    }

    public function guessFieldType(string $field): string
    {
        $type = DB::getSchemaBuilder()->getColumnType($this->getTable(), $field);

        // @todo add other types
        if ($type == 'integer') {
            return 'int';
        } else {
            return 'string';
        }
    }

    public function getFields($model = null): array
    {
        return array_merge($this->endpointFields(), $this->endpointRelations($model ?? $this));
    }

    public function getInputFields(): array
    {
        $exposedAttributes = $this->exposedAttributes ?? $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
        $hiddenAttributes = $this->hiddenAttributes ?? [];
        $attributes = array_values(array_diff($exposedAttributes, $hiddenAttributes));

        $fields = [];
        foreach ($attributes as $attribute) {
            $fields[$attribute] = [
                'name' => $attribute,

                // User conditional input for correct type
                'type' => GraphQL::type('ConditionInput')
            ];
        }

        return $fields;
    }

    public function typeName(): string
    {
        return str_replace('-', ' ', Str::of(get_called_class())->afterLast('\\')->kebab()->plural());
    }

    public function getTypeName(): string
    {
        return Str::of($this->typeName())->singular()->studly();
    }

    public function getDescription(): string
    {
        return Str::of($this->typeName())->singular()->studly() . ' description.';
    }

    public function getQueryName(): string
    {
        return $this->queryName ?? Str::of($this->typeName())->studly();
    }

    public function getQueryDescription(): string
    {
        return $this->queryDescription ?? Str::of($this->typeName())->studly() . ' query description.';
    }

    public function queryResolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this
            ->when(isset($args['where']), function ($query) use (&$args) {
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
            })->get();
    }

    public function queryArgs()
    {
        // print_r($this);
        // die();
        return [
            // Condition
            'where' => ['type' => GraphQL::type('UserInput')],

            'distinct' => ['name' => 'distinct', 'type' => Type::boolean()],

            // Paginate
            'limit' => ['name' => 'limit', 'type' => Type::int()],
            'offset' => ['name' => 'offset', 'type' => Type::int()],
            'skip' => ['name' => 'skip', 'type' => Type::int()],
            'take' => ['name' => 'take', 'type' => Type::int()],
        ];
    }

    public function queryType()
    {
        return Type::listOf(GraphQL::type($this->getTypeName()));
    }
}
