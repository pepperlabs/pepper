<?php

declare(strict_types=1);

namespace App\GraphQL\Types\Pepper;

use App;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UserType extends GraphQLType
{
    protected $attributes = [
        'name' => 'UserType',
        'description' => 'User type description'
    ];

    protected $instance;

    public function __construct()
    {
        $this->instance = new App\Http\Pepper\User;
    }

    public function fields(): array
    {
        return $this->instance->getTypeFields();
    }
}
