<?php

declare(strict_types=1);

namespace App\GraphQL\Inputs\Pepper;

use App;
use Rebing\GraphQL\Support\InputType;

class UserMutationInput extends InputType
{
    protected $attributes = [
        'name' => 'UserMutationInput',
        'description' => 'User mutation input description'
    ];

    protected $instance;

    public function __construct()
    {
        $this->instance = new App\Http\Pepper\User;
    }

    public function fields(): array
    {
        return $this->instance->getMutationFields();
    }
}
