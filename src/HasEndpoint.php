<?php

namespace Amirmasoud\Pepper;

interface HasEndpoint
{
    public function HasEndpoint(): bool;

    public function endpointFields(): array;

    public function endpointRelations(): array;

    public function guessFieldType(string $field): string;

    public function getFields(): array;

    public static function typeName(): string;

    // public function toArray(): array;

    // public function collection(): array;
}
