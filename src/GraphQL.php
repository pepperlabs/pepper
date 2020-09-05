<?php

namespace Pepper;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Pepper\Supports\AggregateSupport;
use Pepper\Supports\InputSupport;
use Pepper\Supports\MutationSupport;
use Pepper\Supports\OrderSupport;
use Pepper\Supports\QuerySupport;
use Pepper\Supports\TypeSupport;
use Prophecy\Exception\Doubler\ClassNotFoundException;
use ReflectionClass;
use ReflectionException;

abstract class GraphQL
{
    use TypeSupport, QuerySupport, InputSupport, OrderSupport, MutationSupport, AggregateSupport;

    /**
     * An instance of GraphQL class.
     *
     * @var object
     */
    protected $instance;

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
     * @param  bool $withColumns
     * @return array
     */
    public function exposedFields(bool $withRelations = true, bool $withColumns = true): array
    {
        if (property_exists($this, 'exposed')) {
            return array_merge(
                // Exposed - Relations = Columns|Empty
                $withColumns ? array_diff($this->exposed, $this->relations()) : [],
                // Exposed - Columns = Relations|Empty
                $withRelations ? array_diff($this->exposed, $this->columns()) : []
            );
        } else {
            return array_merge(
                $withColumns ? $this->columns() : [],
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
     * @param  bool $withColumns
     * @return array
     */
    public function fieldsArray(bool $withRelations = true, bool $withColumns = true): array
    {
        return array_values(
            array_diff(
                $this->exposedFields($withRelations, $withColumns),
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
                if ($this->RelatedGraphQLExists($method->name)) {
                    $relations[] = $method->name;
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
     * @return object
     */
    public function relatedModel(string $method): object
    {
        return $this->relatedModelRelflection($method)->newInstanceArgs();
    }

    /**
     * Get class of the related GraphQL.
     *
     * @param  string $method
     * @return string
     */
    private function RelatedGraphQLClass(string $method): string
    {
        $related = get_class($this->model()->$method()->getRelated());
        $basename = class_basename($related);

        return config('pepper.namespace.root').'\Http\Pepper\\'.$basename;
    }

    /**
     * Wheter corrosponding GraphQL class for relation exists or not.
     *
     * @param  string $method
     * @return bool
     */
    public function RelatedGraphQLExists(string $method): bool
    {
        return class_exists($this->RelatedGraphQLClass($method));
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

    /**
     * Override a method with method if it exists.
     *
     * @param  string  $method
     * @param  callable  $func
     * @param  mixed  ...$args
     * @return mixed
     */
    public function overrideMethod(string $method, callable $func, ...$args)
    {
        $method = 'set'.Str::studly($method);
        if (method_exists($this->instance, $method)) {
            return $this->instance->$method($args);
        } else {
            return $func(...$args);
        }
    }

    /**
     * Get the type of the given field.
     *
     * @param  string  $field
     * @return string
     */
    public function getFieldType(string $field): string
    {
        return $this->overrideMethod('FieldType', [$this, 'guessFieldType'], $field);
    }

    /**
     * Guess field type for GraphQL.
     *
     * @param  string $field
     * @return string
     */
    private function guessFieldType(string $field): string
    {
        switch ($this->getColumnType($field)) {
            case 'smallint':
            case 'integer':
                return 'int';
                break;
            case 'float':
                return 'float';
                break;
            case 'binary':
            case 'boolean':
                return 'boolean';
            case 'bigint':
            case 'string':
            case 'text':
                return 'string';
            default:
                return 'string';
                break;
        }
    }

    /**
     * Get table column type.
     *
     * @param  string $column
     * @return string
     */
    private function getColumnType(string $column): string
    {
        $table = $this->model()->getTable();

        return Schema::getColumnType($table, $column);
    }

    /**
     * Get the base name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->overrideMethod('FieldType', [$this, 'studly'], $this->name());
    }

    /**
     * Call default GraphQL type.
     *
     * @param  string  $field
     * @return mixed
     */
    public function callGraphQLType(string $field)
    {
        return call_user_func('\GraphQL\Type\Definition\Type::'.$this->getFieldType($field));
    }
}
