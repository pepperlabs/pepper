<?php

namespace Pepper\GraphQL\Inputs;

use Pepper\GraphQL as PepperGraphQL;
use Rebing\GraphQL\Support\Facades\GraphQL;

class OrderInput extends PepperGraphQL
{
    /**
     * Get GraphQL Order name.
     *
     * @return string
     */
    public function getOrderName(): string
    {
        return $this->getName().'OrderInput';
    }

    /**
     * Get GraphQL order description.
     *
     * @return string
     */
    public function getOrderDescription(): string
    {
        return $this->getName().' order description.';
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
