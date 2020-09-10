<?php

namespace Pepper\Supports;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

trait InputSupport
{
    /**
     * Get input fields.
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
     * Get related input.
     *
     * @param  string  $attribute
     * @return \GraphQL\Type\Definition\Type
     */
    public function getRelatedInput(string $attribute): Type
    {
        return GraphQL::type($this->relatedGraphQL($attribute)->getInputName());
    }
}
