<?php

namespace Pepper\Supports;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
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
use Illuminate\Support\Str;
use Rebing\GraphQL\Support\Facades\GraphQL;

trait TypeSupport
{
    /**
     * Generate GraphQL fields with field types.
     *
     * @return array
     */
    public function getTypeFields(): array
    {
        $fields = [];

        // Only fields without relations
        foreach ($this->fieldsArray(false) as $attribute) {
            $fields[$attribute] = [
                'name' => $attribute,
                'type' => $this->overrideMethod(
                    'set'.Str::studly($attribute).'Type',
                    [$this, 'callGraphQLType'],
                    $attribute
                ),
                'privacy' => function (array $args) use ($attribute): bool {
                    $method = 'get'.Str::studly($attribute).'Privacy';

                    return $this->$method($args);
                },
            ];
        }

        return array_merge(
            $fields,
            $this->getTypeRelations(),
            $this->getAggregatedFields(),
            $this->getOptionalFields()
        );
    }

    /**
     * Get graphQL query relations.
     *
     * @return array
     */
    public function getTypeRelations(): array
    {
        $fields = [];
        foreach ($this->fieldsArray(true, false) as $relation) {
            $model = $this->modelRelflection();
            $relationType = $model->getMethod($relation)->getReturnType()->getName();
            $type = '';
            if ($relationType === BelongsTo::class) {
                $type = Type::listOf(GraphQL::type($this->getRelatedType($relation)));
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
                MorphToMany::class,
            ])) {
                $type = Type::listOf(GraphQL::type($this->getRelatedType($relation)));
            }

            $fields[$relation] = [
                'name' => $relation,
                'type' => $type,
                'args' => $this->getQueryArgs(),
                'resolve' => function ($root, $args, $context, ResolveInfo $resolveInfo) use ($relation) {
                    return $this->getQueryResolve($root->$relation(), $args, $context, $resolveInfo, function () {
                    })->get();
                },
            ];
        }

        return $fields;
    }

    /**
     * Related field type.
     *
     * @param  string  $method
     * @return \GraphQL\Type\Definition\Type
     */
    public function getRelatedType(string $method): Type
    {
        return GraphQL::type($this->relatedGraphQL($method)->getTypeName());
    }
}
