<?php

namespace Pepper\GraphQL\Inputs;

use Pepper\Supports\GraphQL as PepperGraphQL;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\InputType;

class Input extends InputType
{
    use PepperGraphQL;

    protected $attributes = [];

    /**
     * Get GraphQL Input name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->instance->name().'Input';
    }

    /**
     * Get GraphQL Input description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->instance->name().' input description.';
    }

    public function fields(): array
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
            'type' => GraphQL::type($this->getName()),
        ];

        $fields['_or'] = [
            'name' => '_or',
            'type' => GraphQL::type($this->getName()),
        ];

        $fields['_not'] = [
            'name' => '_not',
            'type' => GraphQL::type($this->getName()),
        ];

        return $fields;
    }

    private function getRelatedInput($attribute)
    {
        return GraphQL::type($this->relatedModel($attribute)->getName());
    }
}
