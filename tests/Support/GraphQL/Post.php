<?php

namespace Tests\Support\GraphQL;

use Pepper\GraphQL;
use Rebing\GraphQL\Support\Facades\GraphQL as ParentGraphQL;

class Post extends GraphQL
{
    public function setCoverType()
    {
        return ParentGraphQL::type('Upload');
    }
}
