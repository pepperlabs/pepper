<?php

namespace Tests\Feature;

use Tests\Support\Models\Post;
use Tests\TestCaseDatabase;

class OverrideTest extends TestCaseDatabase
{
    /** @test */
    public function simple_query_by_pk()
    {
        $moch = $this->getMockBuilder(\App\Http\Pepper\Post::class)
            ->addMethods(['resolvePostdCountAggregate'])
            ->getMock();

        $post = factory(Post::class)->create([
            'title' => 'Title of the post',
        ]);

        $graphql = "
        {
            post_by_pk(id: $post->id) {
                id
                title
            }
        }";

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

        $response->assertOk();
        $this->assertEquals($expectedResult, $response->json());
    }
}

class overridePostClass extends \App\Http\Pepper\Post
{
    public function resolvePostCountAggregate()
    {
        return -1;
    }
}

class mockPost extends overridePostClass
{
}
