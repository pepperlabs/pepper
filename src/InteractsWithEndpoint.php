<?php

namespace Amirmasoud\Pepper;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Illuminate\Support\Facades\DB;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

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
        $model = $model ?? $this;

        // $fields = [];
        // foreach ($this->endpointFields() as $field) {
        //     $fields[$field] = [
        //         'name' => $field,
        //         'type' => call_user_func('\GraphQL\Type\Definition\Type::' . $this->guessFieldType($field))
        //     ];
        // }

        // foreach ($this->endpointRelations() as $relation) {
        //     $reflector = new \ReflectionClass($model);
        //     $relationType = $reflector->getMethod($relation)->getReturnType();
        //     $fields[$field] = [];
        //     if ($relationType->getName() === HasMany::class) {
        //         $fields[$relation] = [
        //             'name' => $relation,
        //             'type' => Type::listOf(GraphQL::type($this->getTypeName())),
        //             'resolve' => function ($root, $args) use ($relation) {
        //                 return $root->{$relation};
        //             }
        //         ];
        //     }
        // }

        return array_merge($this->endpointFields(), $this->endpointRelations($model));
        // return $fields;
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

    public function queryResolve()
    {
        return $this->all();
    }

    public function queryArgs()
    {
        return [];
    }

    public function queryType()
    {
        return Type::listOf(GraphQL::type($this->getTypeName()));
    }
}
