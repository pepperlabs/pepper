<?php

namespace Pepper\GraphQL\Inputs;

use Pepper\Supports\GraphQL as PepperGraphQL;
use Rebing\GraphQL\Support\Facades\GraphQL;

class OrderInput
{
    use PepperGraphQL;

    /**
     * Get GraphQL Order name.
     *
     * @return string
     */
    public function getOrderName(): string
    {
        return $this->name().'OrderInput';
    }

    /**
     * Get GraphQL order description.
     *
     * @return string
     */
    public function getOrderDescription(): string
    {
        return $this->name().' order description.';
    }

    /**
     * Get GraphQL ordering fields.
     *
     * @return array
     */
    public function getOrderFields(): array
    {
        $fields = [];

        foreach ($this->fieldsArray() as $field) {
            $fields[$field] = [
                'name' => $field,
                'type' => GraphQL::type('OrderByEnum'),
            ];
        }

        return $fields;
    }
}
