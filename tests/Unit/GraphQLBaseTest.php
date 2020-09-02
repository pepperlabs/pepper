<?php

namespace Tests\Unit;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\Support\GraphQL\Post;
use Tests\Support\GraphQL\test_graphql;
use Tests\Support\GraphQL\TestGraphQL;
use Tests\TestCaseDatabase;

class GraphQLBaseTest extends TestCaseDatabase
{
    private $test_1;
    private $test_2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->test_1 = new TestGraphQL();
        $this->test_2 = new test_graphql();
        $this->post = new Post();
    }

    /** @test */
    public function base_classname()
    {
        $this->assertEquals($this->test_1->name(), 'TestGraphQL');
        $this->assertEquals($this->test_2->name(), 'test_graphql');
    }

    /** @test */
    public function base_classname_studly()
    {
        $this->assertEquals($this->test_1->studly(), 'TestGraphQL');
        $this->assertEquals($this->test_2->studly(), 'TestGraphql');
    }

    /** @test */
    public function model_class_is_customizable()
    {
        // We have changed our namespace.models config value to Tests\Support\Models in setup environment method.
        $this->assertEquals($this->test_1->modelClass(), 'Tests\Support\Models\TestGraphQL');

        /**
         * custom model is set in test_2.
         *
         * tests private method defaultModel as well.
         */
        $this->assertEquals($this->test_2->modelClass(), 'Tests\Support\Models\User');
    }

    /** @test */
    public function can_get_instance_of_model()
    {
        $this->assertTrue($this->test_2->model() instanceof \Tests\Support\Models\User);

        /** tests private method modelRelflection */
        $this->expectException(ModelNotFoundException::class);
        $this->assertTrue($this->test_1->model() instanceof App\TestGraphQL);
    }

    /** @test */
    public function exposed_fields_are_effective()
    {
        $columns = [
            'id',
            'title',
            'body',
            'user_id',
            'properties',
            'flag',
            'published_at',
            'created_at',
            'updated_at',
        ];

        $relations = [
            'user',
            'comments',
            'likes',
        ];

        // All fields
        $this->assertEquals($this->post->exposedFields(), array_merge($columns, $relations));

        // Without relations
        $this->assertEquals($this->post->exposedFields(false), $columns);

        // Limit fields to defined values in exposed array.
        $exposed = $this->post->exposed = [
            'properties',
            'flag',
        ];
        $this->assertEquals($this->post->exposedFields(), $exposed);
    }
}
