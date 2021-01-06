<?php

namespace Tests\Feature;

use Tests\TestCaseDatabase;

class AuthTest extends TestCaseDatabase
{
    /**
     * @group sqlite
     * @group mysql
     * @group pgsql
     * @group sqlsrv
     * @test
     */
    public function login()
    {
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
