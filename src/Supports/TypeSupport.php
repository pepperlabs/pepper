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
     * Merging every available fields for the model by combining the attributes
     * fields, relations and augmented attributes, aggregate fields and give
     * the possibility to override any of them by optional fields option.
     *
     * @return array
     */
    public function getTypeFields(): array
    {
        return array_merge(
            $this->getAttributesFields(),
            $this->getTypeRelations(),
            $this->getAggregatedFields(),
            $this->getOptionalFields()
        );
    }

    /**
     * Get the types of the models attributes fields without relations or any
     * other fields such aggregation or overriders. Type and privay can be
     * overrided by calling set[Attribute]Type and set[Attribute]Pricay.
     *
     * @return array
     */
    public function getAttributesFields(): array
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

        return $fields;
    }

    /**
     * Loop through all explicitly defined relations and make them ready to be
     * added to list of type by defining their name, type, available args
     * and resolver method. Relations are always wrapped in an array.
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

            // The return of a relation is always a list of the defined type for
            // related relation. We have explicitly defined the available
            // supported relation by Laravel to prune any future bug.
            if (in_array($relationType, [
                BelongsTo::class,
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
     * Get related field type by passing method name. At this moment we cannot
     * guess the relation type if it's not explicitly stated by the defined
     * return type at the end of method implementation in the app model.
     *
     * @param  string  $method
     * @return \GraphQL\Type\Definition\Type
     */
    public function getRelatedType(string $method): Type
    {
        return GraphQL::type($this->relatedGraphQL($method)->getTypeName());
    }
}
