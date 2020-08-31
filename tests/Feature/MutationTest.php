<?php

namespace Tests\Feature;

use Tests\Support\Models\Post;
use Tests\TestCaseDatabase;

class MutationTest extends TestCaseDatabase
{
    /** @test */
    public function simple_mutation_by_pk()
    {
        $post = factory(Post::class)->create([
            'title' => 'Title of the post',
        ]);

        $graphql = "
        {
            update_user_by_pk(
                pk_columns: {
                    id: $post->id
                  },
                  _set: {
                    name: '[updated] Title of the post'
                  }
            ) {
                id
                name
            }
        }";

        $response = $this->call('GET', '/graphql', [
            'mutation' => $graphql,
        ]);

        $expectedResult = [
            'data' => [
                'update_user_by_pk' => [
                    'id' => "$post->id",
                    'name' => '[updated] Title of the post',
                ],
            ],
        ];

        $response->assertOk();
        $this->assertEquals($expectedResult, $response->json());
    }
}
