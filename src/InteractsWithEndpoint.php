<?php

namespace Amirmasoud\Pepper;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Illuminate\Support\Facades\DB;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Str;

trait InteractsWithEndpoint
{
    public function HasEndpoint(): bool
    {
        return $this->endpoint ?? true;
    }

    public function endpointFields(): array
    {
        $exposedAttributes = $this->exposedAttributes ?? $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
        $hiddenAttributes = $this->hiddenAttributes ?? [];
        return array_values(array_diff($exposedAttributes, $hiddenAttributes));
    }

    public function endpointRelations(): array
    {
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
        return array_values(array_diff($exposedRelations, $hiddenRelations));
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

        $fields = [];
        foreach ($this->endpointFields() as $field) {
            $fields[$field] = [
                'name' => $field,
                'type' => call_user_func('\GraphQL\Type\Definition\Type::' . $this->guessFieldType($field))
            ];
        }

        // @TODO
        // foreach ($this->endpointRelations() as $relation) {
        //     $reflector = new \ReflectionClass($model);
        //     $relationType = $reflector->getMethod($relation)->getReturnType();
        //     $fields[$field] = [];
        //     if ($relationType instanceof HasMany) {
        //         $fields[$field]['type'] = Type::listOf(GraphQL::type('book'));
        //         $fields[$field]['resolve'] = function ($root, $args) use ($relation) {
        //             return $root->{$relation};
        //         };
        //     }
        // }
        return $fields;
    }

    public function typeName(): string
    {
        return str_replace('-', ' ', Str::of(get_called_class())->afterLast('\\')->kebab()->plural());
    }

    public function getName(): string
    {
        return $this->name ?? Str::of($this->typeName())->singular()->studly();
    }

    public function getDescription(): string
    {
        return $this->description ?? Str::of($this->typeName())->singular()->studly() . ' Description.';
    }
}
