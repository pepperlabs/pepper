<?php

declare(strict_types=1);

namespace Pepper\GraphQL;

use Rebing\GraphQL\Support\InputType;

class MutationInput extends InputType
{
    protected $attributes = [
        'name' => 'MutationInput',
        'description' => 'Available conditions',
    ];

    protected $instance;

    public function __construct()
    {
        $this->instance = new \App\Http\Pepper\User;
    }

    public function fields(): array
    {
        return $this->instance->getMutationFields();
    }
}
