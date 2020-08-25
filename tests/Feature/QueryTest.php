<?php

namespace Tests\Feature;

use Tests\TestCaseDatabase;
use Tests\Support\Models\Post;

class QueryTest extends TestCaseDatabase
{
    /** @test */
    public function simple_query_by_pk()
    {
        $post = factory(Post::class)->create([
            'title' => 'Title of the post',
        ]);

        $graphql = <<<GRAQPHQL
{
    post_by_pk(id: $post->id) {
        id
        title
    }
}
GRAQPHQL;

        $response = $this->call('GET', '/graphql', [
            'query' => $graphql,
        ]);

        $expectedResult = [
            'data' => [
                'post_by_pk' => [
                    'id' => "$post->id",
                    'title' => 'Title of the post',
                ],
            ],
        ];

        $this->assertEquals($response->getStatusCode(), 200);
        $this->assertEquals($expectedResult, $response->json());
    }
}
