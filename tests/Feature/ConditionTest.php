<?php

namespace Tests\Feature;

use Tests\Support\Models\Post;
use Tests\TestCaseDatabase;

class ConditionTest extends TestCaseDatabase
{
    /**
     * @group sqlite
     * @group mysql
     * @group pgsql
     * @group sqlsrv
     * @test
     */
    public function equal_simple_query()
    {
        $post = factory(Post::class)->create([
            'title' => 'TITLE',
        ]);

        $graphql = '{
            post (where: {title: {_eq: "TITLE"}}) {
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
                        'id' => "$post->id",
                        'title' => 'TITLE',
                    ],
                ],
            ],
        ];

        $response->assertOk();
        $this->assertEquals($expectedResult, $response->json());
    }

    /**
     * @group sqlite
     * @group mysql
     * @group pgsql
     * @group sqlsrv
     * @test
     */
    public function not_equal_simple_query()
    {
        $post = factory(Post::class)->create([
            'title' => 'TITLE',
        ]);

        $graphql = '{
            post (where: {title: {_neq: "SUBJECT"}}) {
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
                        'id' => "$post->id",
                        'title' => 'TITLE',
                    ],
                ],
            ],
        ];

        $response->assertOk();
        $this->assertEquals($expectedResult, $response->json());
    }

    /**
     * @group sqlite
     * @group mysql
     * @group pgsql
     * @group sqlsrv
     * @test
     */
    public function greater_than_simple_query()
    {
        $post = factory(Post::class)->create([
            'title' => 'TITLE',
        ]);

        $graphql = '{
            post (where: {id: {_gt: 0}}) {
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
                        'id' => "$post->id",
                        'title' => 'TITLE',
                    ],
                ],
            ],
        ];

        $response->assertOk();
        $this->assertEquals($expectedResult, $response->json());
    }

    /**
     * @group sqlite
     * @group mysql
     * @group pgsql
     * @group sqlsrv
     * @test
     */
    public function less_than_simple_query()
    {
        $post = factory(Post::class)->create([
            'title' => 'TITLE',
        ]);

        $graphql = '{
            post (where: {id: {_lt: 2}}) {
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
                        'id' => "$post->id",
                        'title' => 'TITLE',
                    ],
                ],
            ],
        ];

        $response->assertOk();
        $this->assertEquals($expectedResult, $response->json());
    }

    /**
     * @group sqlite
     * @group mysql
     * @group pgsql
     * @group sqlsrv
     * @test
     */
    public function greater_than_equal_simple_query()
    {
        $post = factory(Post::class)->create([
            'title' => 'TITLE',
        ]);

        $graphql = '{
            post (where: {id: {_gte: 1}}) {
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
                        'id' => "$post->id",
                        'title' => 'TITLE',
                    ],
                ],
            ],
        ];

        $response->assertOk();
        $this->assertEquals($expectedResult, $response->json());
    }

    /**
     * @group sqlite
     * @group mysql
     * @group pgsql
     * @group sqlsrv
     * @test
     */
    public function less_than_equal_simple_query()
    {
        $post = factory(Post::class)->create([
            'title' => 'TITLE',
        ]);

        $graphql = '{
                post (where: {id: {_lte: 1}}) {
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
                        'id' => "$post->id",
                        'title' => 'TITLE',
                    ],
                ],
            ],
        ];

        $response->assertOk();
        $this->assertEquals($expectedResult, $response->json());
    }

    /**
     * @group sqlite
     * @group mysql
     * @group pgsql
     * @group sqlsrv
     * @test
     */
    public function where_in_simple_query()
    {
        $post = factory(Post::class)->create([
            'title' => 'TITLE',
        ]);

        $graphql = '{
                post (where: {id: {_in: [1]}}) {
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
                        'id' => "$post->id",
                        'title' => 'TITLE',
                    ],
                ],
            ],
        ];

        $response->assertOk();
        $this->assertEquals($expectedResult, $response->json());
    }

    /**
     * @group sqlite
     * @group mysql
     * @group pgsql
     * @group sqlsrv
     * @test
     */
    public function where_not_in_simple_query()
    {
        $post = factory(Post::class)->create([
            'title' => 'TITLE',
        ]);

        $graphql = '{
                post (where: {id: {_nin: [0]}}) {
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
                        'id' => "$post->id",
                        'title' => 'TITLE',
                    ],
                ],
            ],
        ];

        $response->assertOk();
        $this->assertEquals($expectedResult, $response->json());
    }

    /**
     * @group sqlite
     * @group mysql
     * @group pgsql
     * @group sqlsrv
     * @test
     */
    public function like_simple_query()
    {
        $post = factory(Post::class)->create([
            'title' => 'TITLE',
        ]);

        $graphql = '{
                post (where: {title: {_like: "TITL%"}}) {
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
                        'id' => "$post->id",
                        'title' => 'TITLE',
                    ],
                ],
            ],
        ];

        $response->assertOk();
        $this->assertEquals($expectedResult, $response->json());
    }

    /**
     * @group sqlite
     * @group mysql
     * @group pgsql
     * @group sqlsrv
     * @test
     */
    public function not_like_simple_query()
    {
        $post = factory(Post::class)->create([
            'title' => 'TITLE',
        ]);

        $graphql = '{
                post (where: {title: {_nlike: "SUB%"}}) {
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
                        'id' => "$post->id",
                        'title' => 'TITLE',
                    ],
                ],
            ],
        ];

        $response->assertOk();
        $this->assertEquals($expectedResult, $response->json());
    }

    /**
     * @group pgsql
     * @test
     */
    public function ilike_simple_query()
    {
        $post = factory(Post::class)->create([
            'title' => 'TITLE',
        ]);

        $graphql = '{
                post (where: {title: {_ilike: "titl%"}}) {
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
                        'id' => "$post->id",
                        'title' => 'TITLE',
                    ],
                ],
            ],
        ];

        $response->assertOk();
        $this->assertEquals($expectedResult, $response->json());
    }

    /**
     * @group pgsql
     * @test
     */
    public function not_ilike_simple_query()
    {
        $post = factory(Post::class)->create([
            'title' => 'TITLE',
        ]);

        $graphql = '{
                post (where: {title: {_nilike: "subj%"}}) {
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
                        'id' => "$post->id",
                        'title' => 'TITLE',
                    ],
                ],
            ],
        ];

        $response->assertOk();
        $this->assertEquals($expectedResult, $response->json());
    }

    /**
     * @group sqlite
     * @group mysql
     * @group pgsql
     * @group sqlsrv
     * @test
     */
    public function is_null_true_simple_query()
    {
        $post = factory(Post::class)->create([
            'title' => 'TITLE',
        ]);

        $graphql = '{
                post (where: {published_at: {_is_null: true}}) {
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
                        'id' => "$post->id",
                        'title' => 'TITLE',
                    ],
                ],
            ],
        ];

        $response->assertOk();
        $this->assertEquals($expectedResult, $response->json());
    }

    /**
     * @group sqlite
     * @group mysql
     * @group pgsql
     * @group sqlsrv
     * @test
     */
    public function is_null_false_simple_query()
    {
        $post = factory(Post::class)->create([
            'title' => 'TITLE',
        ]);

        $graphql = '{
                post (where: {body: {_is_null: false}}) {
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
                        'id' => "$post->id",
                        'title' => 'TITLE',
                    ],
                ],
            ],
        ];

        $response->assertOk();
        $this->assertEquals($expectedResult, $response->json());
    }
}
