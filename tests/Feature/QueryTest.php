<?php

namespace Tests\Feature;


use Tests\TestCaseDatabase;
use Tests\Support\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\DB;

class QueryTest extends TestCaseDatabase
{
    public function testWithoutSelectFields(): void
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

        $response = $this->call('POST', '/graphql', [
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

    // public function testStatus()
    // {
    //     $response = dd($this->getJson('http://localhost:8000/graphql'));

    //     $response->assertStatus(200);
    // }

    // /**
    //  * A basic test example.
    //  *
    //  * @return void
    //  */
    // public function testSimpleQueryByPk()
    // {
    //     $response = $this->withHeaders([
    //         'Content-Type' => 'application/json',
    //     ])->json('GET', '/graphql', [
    //         'query' => 'query {user_by_pk(id: 1){id}}'
    //     ]);
    //     $response->assertStatus(200)->assertExactJson([
    //         'data' => [
    //             'user_by_pk' => [
    //                 ['id' => '1']
    //             ]
    //         ]
    //     ]);
    // }
}
