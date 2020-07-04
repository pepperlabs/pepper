<?php

namespace Amirmasoud\Pepper;

trait InteractsWithEndpoint
{
    public function HasEndpoint(): bool
    {
        return $this->endpoint ?? true;
    }

    public function endpointAttributes(): array
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
}
