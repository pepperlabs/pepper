<?php

namespace Amirmasoud\Pepper;

interface HasEndpoint
{
    /**
     * If model has endpoint.
     *
     * @return boolean
     */
    public function hasEndpoint(): bool;

    /**
     * Available fields on the endpoint.
     *
     * @return array
     */
    public function endpointFields(): array;

    /**
     * Available relations on the endpoint.
     *
     * @return array
     */
    public function endpointRelations($model): array;

    /**
     * Guess type of the field.
     *
     * @param  string $field
     * @return string
     */
    public function guessFieldType(string $field): string;

    public function getFields(): array;

    public function typeName(): string;

    public function getTypeName(): string;

    public function getDescription(): string;

    public function getQueryName(): string;

    public function getQueryDescription(): string;
}
