<?php

namespace Pepper\GraphQL;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Prophecy\Exception\Doubler\ClassNotFoundException;
use ReflectionClass;
use ReflectionException;

class BaseGraphQL
{
    /**
     * Get the class basename of the GraphQL class.
     *
     * @return void
     */
    public function name(): string
    {
        return class_basename($this);
    }

    /**
     * Get studly case of the class basename of the GraphQL class.
     *
     * @return string
     */
    public function studly(): string
    {
        return Str::studly($this->name());
    }

    /**
     * Default model for each GraphQL class is set to defined model namespace
     * concated with studly case of the GraphQL class. for example a class
     * of User and model name space of App\\ would resolve to App\User.
     *
     * @return string
     */
    private function defaultModel(): string
    {
        return config('pepper.namespace.models').'\\'.$this->studly();
    }

    /**
     * If model property has been set for the GraphQL class, it would override
     * the default generate model guessed based on the namespace and class
     * basename and will start use the defined model property instead.
     *
     * @return string
     */
    public function modelClass(): string
    {
        return property_exists($this, 'model')
            ? $this->model
            : $this->defaultModel();
    }

    /**
     * Make a new reflection from the model class. this method will be used
     * to access the dynamic model of the GraphQL class.
     *
     * @return ReflectionClass
     * @throws ReflectionException
     */
    private function modelRelflection(): ReflectionClass
    {
        try {
            return new ReflectionClass($this->modelClass());
        } catch (ReflectionException $e) {
            throw new ModelNotFoundException("Trying to get {$this->modelClass()} failed. please check pepper.namespace.models config to be correct and if you have defined model in GraphQL class, make sure {$this->modelClass()} model exists.");
        }
    }

    /**
     * Get a new instance of the model for the GraphQL class.
     *
     * @return mixed
     */
    public function model()
    {
        return $this->modelRelflection()->newInstanceArgs();
    }

    /**
     * Gets an array of allowed fields on the model defined by the exposed
     * property of the class. preassumption is all fields are allowed to
     * be exposed to the public and there is no restriction for them.
     *
     * @param  bool $withRelations
     * @param  bool $withFields
     * @return array
     */
    public function exposedFields(bool $withRelations = true, bool $withFields = true): array
    {
        if (property_exists($this, 'exposed')) {
            return array_merge(
                // Exposed - Relations = Columns|Empty
                $withFields ? array_diff($this->exposed, $this->relations()) : [],
                // Exposed - Columns = Relations|Empty
                $withRelations ? array_diff($this->exposed, $this->columns()) : []
            );
        } else {
            return array_merge(
                $withFields ? $this->columns() : [],
                $withRelations ? $this->relations() : []
            );
        }
    }

    /**
     * Gets an array of the denied fields on the model defined by the covered
     * property of the class. pre-assumption is that no fields is denied to
     * be exposed to the public and all of have no restriction by default.
     *
     * @return array
     */
    public function coveredFields(): array
    {
        return property_exists($this, 'covered')
            ? $this->covered
            : [];
    }

    /**
     * Using the defined model, we would query the table schema to get a listing
     * of the all columns avaialble in the column. later we would also scan
     * through their types and cast their corresponsing GraphQL types.
     *
     * @return array
     */
    private function columns(): array
    {
        $model = $this->model();
        $table = $model->getTable();

        return $model->getConnection()
            ->getSchemaBuilder()
            ->getColumnListing($table);
    }

    /**
     * List of all of the avialable fields can be exposed from the model after
     * aggregating all of the exposed fields and relations and subtracting
     * them from covered fields and relations.
     *
     * @param  bool $withRelations
     * @param  bool $withFields
     * @return array
     */
    public function fieldsArray(bool $withRelations = true, bool $withFields = true): array
    {
        return array_values(
            array_diff(
                $this->exposedFields($withRelations, $withFields),
                $this->coveredFields()
            )
        );
    }

    /**
     * Extract list of avaialbe methods in a model by getting a list of all of
     * the methods in the defined model and check the return type of the
     * reflection class and return the array of the relations after.
     *
     * @return array
     */
    private function relations(): array
    {
        $relations = [];
        $supported = [
            'BelongsTo',
            'HasOne',
            'BelongsToMany',
            'HasMany',
            'HasOneOrMany',
            'HasManyThrough',
            'HasOneThrough',
            'MorphOne',
            'MorphMany',
            'MorphOneOrMany',
            'MorphPivot',
            'MorphTo',
            'MorphToMany',
        ];
        foreach ($this->modelMethods() as $method) {
            $type = $method->getReturnType();
            if ($type && in_array(class_basename($type->getName()), $supported)) {
                try {
                    $this->relatedModel($method->name);
                    $relations[] = $method->name;
                } catch (ClassNotFoundException $e) {
                }
            }
        }

        return $relations;
    }

    /**
     * Get related model GraphQL class. For post, user example, this method
     * on calling Post GraphQL class and method user would return User
     * GraphQL model.
     *
     * @param  string $method
     * @return void
     */
    private function relatedModelClass(string $method): string
    {
        $related = get_class($this->model()->$method()->getRelated());
        $basename = class_basename($related);

        return config('pepper.namespace.models').'\\'.$basename;
    }

    /**
     * Create new reflection from the related model class.
     *
     * @param  string $method
     * @return ReflectionClass
     */
    private function relatedModelRelflection(string $method): ReflectionClass
    {
        try {
            return new ReflectionClass($this->modelClass());
        } catch (ReflectionException $e) {
            throw new ClassNotFoundException("Trying to get {$this->relatedModelClass($method)} failed. please check pepper.namespace.root config to be correct and if GraphQL class exists.", $this->relatedModelClass($method));
        }
    }

    /**
     * Get a new instance of the related model for the GraphQL class.
     *
     * @param  string $method
     * @return mixed
     */
    public function relatedModel(string $method)
    {
        return $this->relatedModelRelflection($method)->newInstanceArgs();
    }

    /**
     * Get a full array of the model methods. this method will be used later
     * to extract relations defined in the model.
     *
     * @return array
     */
    private function modelMethods(): array
    {
        return $this->modelRelflection()->getMethods();
    }
}
