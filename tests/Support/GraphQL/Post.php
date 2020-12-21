<?php

namespace Tests\Support\GraphQL;

use GraphQL\Type\Definition\Type;
use Pepper\GraphQL;
use Rebing\GraphQL\Support\Facades\GraphQL as ParentGraphQL;

class Post extends GraphQL
{
    public function setCoverType()
    {
        return ParentGraphQL::type('Upload');
    }

    public function setOptionalFields()
    {
        return [
            'cover_url' => [
                'type' => Type::string(),
                'selectable' => false,
                'resolve' => function ($root) {
                    $root->refresh();

                    return $root->cover;
                },
            ],
        ];
    }
}
