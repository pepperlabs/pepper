<?php

namespace Tests\Support\GraphQL;

use Pepper\GraphQL\BaseGraphQL;

class test_graphql extends BaseGraphQL
{
    public $model = \Tests\Support\Models\User::class;
}