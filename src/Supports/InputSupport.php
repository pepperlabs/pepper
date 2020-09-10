<?php

namespace Pepper\Supports;

use Rebing\GraphQL\Support\Facades\GraphQL;

trait InputSupport
{
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

    public function getRelatedInput($attribute)
    {
        return GraphQL::type($this->relatedGraphQL($attribute)->getInputName());
    }
}
