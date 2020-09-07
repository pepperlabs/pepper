<?php

declare(strict_types=1);

namespace App\GraphQL\Types\Pepper;

use App;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UserAggregateType extends GraphQLType
{
    protected $attributes = [
        'name' => 'UserAggregateType',
        'description' => 'User aggregate type description'
    ];

    protected $instance;

    public function __construct()
    {
        $this->instance = new App\Http\Pepper\User;
    }

    public function fields(): array
    {
        return $this->instance->getAvailableAggregators();
    }
}
