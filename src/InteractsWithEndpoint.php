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
                'type' => call_user_func('\GraphQL\Type\Definition\Type::' . $this->guessFieldType($attribute))
                // 'type' => call_user_func('\App\GraphQL\Inputs\ConditionInput::class')
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
                print_r($args);
                die();
                return $query->limit($args['limit']);
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
        return [
            // Condition
            'where' => ['type' => GraphQL::type('ConditionInput')],

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
