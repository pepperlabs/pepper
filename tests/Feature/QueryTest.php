<?php

namespace Tests\Feature;

use Tests\Support\Models\Post;
use Tests\TestCaseDatabase;

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

        $response->assertSuccessful();

        $this->assertEquals($expectedResult, $response->json());
    }
}
