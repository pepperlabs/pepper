<?php

namespace Tests\Support\GraphQL;

use Pepper\Supports\GraphQL;

class test_graphql
{
    use GraphQL;

    public $model = \Tests\Support\Models\User::class;
}
