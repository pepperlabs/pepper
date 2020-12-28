<?php

namespace Pepper\Supports;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

trait InputSupport
{
    /**
     * Get all input fields can be present in a GraphQL query or mutation.
     *
     * @return array
     */
    public function getInputFields(): array
    {
        $fields = [];

        $relations = $this->relations();
        foreach ($this->fieldsArray() as $attribute) {
            if (in_array($attribute, $relations)) {
                $fields[$attribute] = [
                    'name' => $attribute,
                    'type' => GraphQL::type($this->getRelatedInput($attribute)),
                ];
            } else {
                $fields[$attribute] = [
                    'name' => $attribute,
                    'type' => GraphQL::type('ConditionInput'),
                ];
            }
        }

        $fields['_and'] = [
            'name' => '_and',
            'type' => GraphQL::type($this->getInputName()),
        ];

        $fields['_or'] = [
            'name' => '_or',
            'type' => GraphQL::type($this->getInputName()),
        ];

        $fields['_not'] = [
            'name' => '_not',
            'type' => GraphQL::type($this->getInputName()),
        ];

        return $fields;
    }

    /**
     * Get type of of the related GraphQL input.
     *
     * @param  string  $attribute
     * @return \GraphQL\Type\Definition\Type
     */
    public function getRelatedInput(string $attribute): Type
    {
        return GraphQL::type($this->relatedGraphQL($attribute)->getInputName());
    }
}
