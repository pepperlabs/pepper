<?php

namespace Pepper\GraphQL\Inputs;

use Pepper\GraphQL as PepperGraphQL;
use Rebing\GraphQL\Support\Facades\GraphQL;

class Input extends PepperGraphQL
{
    /**
     * Get GraphQL Input name.
     *
     * @return string
     */
    public function getInputName(): string
    {
        return $this->name().'Input';
    }

    /**
     * Get GraphQL Input description.
     *
     * @return string
     */
    public function getInputDescription(): string
    {
        return $this->name().' input description.';
    }

    public function getInputFields(): array
    {
        $fields = [];

        $relations = $this->fieldsArray(true, false);
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
        return GraphQL::type($this->relatedModel($attribute)->getInputName());
    }
}
