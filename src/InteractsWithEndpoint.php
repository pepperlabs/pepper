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
}
