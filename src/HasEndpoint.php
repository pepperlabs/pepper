<?php

namespace Amirmasoud\Pepper;

interface HasEndpoint
{
    public function HasEndpoint(): bool;

    public function endpointAttributes(): array;

    // public function toArray(): array;

    // public function collection(): array;
}
