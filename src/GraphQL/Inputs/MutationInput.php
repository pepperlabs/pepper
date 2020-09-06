<?php

namespace Pepper\GraphQL\Inputs;

use Pepper\Supports\GraphQL as PepperGraphQL;

class MutationInput
{
    use PepperGraphQL;

    /**
     * Get input mutation name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name().'MutationInput';
    }

    /**
         * Get input mutation description.
         *
         * @return string
         */
    public function getInputMutationDescription(): string
    {
        return $this->getName().' input mutation description.';
    }

    /**
     * Get mutation fields.
     *
     * @return array
     */
    public function getMutationFields(): array
    {
        $fields = [];

        // Get fields excluded relations
        foreach ($this->fieldsArray(false) as $type) {
            $fields[$type] = [
                'name' => $type,
                'type' => $this->callGraphQLType($type),
            ];
        }

        return $fields;
    }
}
