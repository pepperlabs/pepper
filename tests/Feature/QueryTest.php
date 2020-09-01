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

    /** @test */
    public function simple_query_by_pk_not_found()
    {
        $graphql = '
        {
            post_by_pk(id: 0) {
                id
                title
            }
        }';

        $response = $this->call('GET', '/graphql', [
            'query' => $graphql,
        ]);

        $expectedResult = [
            'data' => [
                'post_by_pk' => null,
            ],
        ];

        $response->assertOk();
        $this->assertEquals($expectedResult, $response->json());
    }

    /** @test */
    public function simple_query()
    {
        $post_1 = factory(Post::class)->create([
            'title' => 'Title of the post #1',
        ]);

        $post_2 = factory(Post::class)->create([
            'title' => 'Title of the post #2',
        ]);

        $post_3 = factory(Post::class)->create([
            'title' => 'Title of the post #3',
        ]);

        $graphql = '
        {
            post {
                id
                title
            }
        }';

        $response = $this->call('GET', '/graphql', [
            'query' => $graphql,
        ]);

        $expectedResult = [
            'data' => [
                'post' => [
                    [
                        'id' => "$post_1->id",
                        'title' => 'Title of the post #1',
                    ],
                    [
                        'id' => "$post_2->id",
                        'title' => 'Title of the post #2',
                    ],
                    [
                        'id' => "$post_3->id",
                        'title' => 'Title of the post #3',
                    ],
                ],
            ],
        ];

        $response->assertOk();
        $this->assertEquals($expectedResult, $response->json());
    }

    /** @test */
    public function simple_query_not_found()
    {
        $graphql = '
        {
            post {
                id
                title
            }
        }';

        $response = $this->call('GET', '/graphql', [
            'query' => $graphql,
        ]);

        $expectedResult = [
            'data' => [
                'post' => [],
            ],
        ];

        $response->assertOk();
        $this->assertEquals($expectedResult, $response->json());
    }

    /** @test */
    public function simple_query_aggregate()
    {
        $post_1 = factory(Post::class)->create([
            'title' => 'Title of the post #1',
        ]);

        $post_2 = factory(Post::class)->create([
            'title' => 'Title of the post #2',
        ]);

        $post_3 = factory(Post::class)->create([
            'title' => 'Title of the post #3',
        ]);

        $graphql = '
        query {
            post_aggregate {
                aggregate {
                    count
                    sum {
                        ...aggregateOnFragment
                    }
                    avg {
                        ...aggregateOnFragment
                    }
                    max {
                        ...aggregateOnFragment
                    }
                    min {
                        ...aggregateOnFragment
                    }
                }
            }
        }

        fragment aggregateOnFragment on PostResultAggregateType {
            id
        }';

        $response = $this->call('GET', '/graphql', [
            'query' => $graphql,
        ]);

        $expectedResult = [
            'data' => [
                'post_aggregate' => [
                    'aggregate' => [
                        'count' => 3,
                        'sum' => [
                            'id' => array_sum([$post_1->id, $post_2->id, $post_3->id]),
                        ],
                        'avg' => [
                            'id' => array_sum([$post_1->id, $post_2->id, $post_3->id]) / 3,
                        ],
                        'max' => [
                            'id' => max([$post_1->id, $post_2->id, $post_3->id]),
                        ],
                        'min' => [
                            'id' => min([$post_1->id, $post_2->id, $post_3->id]),
                        ],
                    ],
                ],
            ],
        ];

        $response->assertOk();
        $this->assertEquals($expectedResult, $response->json());
    }
}
