<?php

namespace Pepper;

use BadMethodCallException;
use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Pepper\Extra\Cache\Cache;
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

    /** @var object */
    // protected $model;

    /** @var array */
    // protected $exposed = [];

    /** @var array */
    // protected $covered = [];

    /**
     * Get class basename.
     *
     * @return string
     */
    private function name(): string
    {
        return class_basename($this);
    }

    /**
     * Get studly case of the class basename.
     *
     * @return string
     */
    public function studly(): string
    {
        return Str::studly($this->name());
    }

    /**
     * Get snake case of the class basename.
     *
     * @return string
     */
    public function snake(): string
    {
        return Str::snake($this->name());
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
        return config('pepper.base.namespace.models').'\\'.$this->studly();
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
    public function modelRelflection(): ReflectionClass
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
     * @param  bool  $withRelations
     * @param  bool  $withColumns
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
        return Cache::get('pepper:'.$this->name().':__columns', function () {
            $model = $this->model();
            $table = $model->getTable();

            $columns = $model->getConnection()
                ->getSchemaBuilder()
                ->getColumnListing($table);

            return $columns;
        });
    }

    /**
     * List of all of the avialable fields can be exposed from the model after
     * aggregating all of the exposed fields and relations and subtracting
     * them from covered fields and relations.
     *
     * @param  bool  $withRelations
     * @param  bool  $withColumns
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
                if ($this->relatedGraphQLExists($method->name)) {
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
     * @param  string  $method
     * @return void
     */
    private function relatedModelClass(string $method): string
    {
        return get_class($this->model()->$method()->getRelated());
    }

    /**
     * Create new reflection from the related model class.
     *
     * @param  string  $method
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
     * @param  string  $method
     * @return mixed
     */
    public function relatedModel(string $method)
    {
        return $this->relatedModelRelflection($method)->newInstanceArgs();
    }

    /**
     * Get class of the related GraphQL.
     *
     * @param  string  $method
     * @return string
     * @throws ClassNotFoundException
     */
    private function relatedGraphQLClass(string $method)
    {
        $relatedModel = $this->relatedModel($method);

        // Expected defaults
        $related = get_class($this->model()->$method()->getRelated());
        $basename = class_basename($related);
        $relatedGraphQLClass = config('pepper.base.namespace.root').'\Http\Pepper\\'.$basename;

        if (file_exists(app('path')."/Http/Pepper/{$basename}.php") && class_exists(config('pepper.base.namespace.root').'\Http\Pepper\\'.$basename)) {
            return $relatedGraphQLClass;
        } else {
            return false;
        }

        // Is it necessary to check this?
        // --
        // $relatedGraphQLInstance = new $relatedGraphQLClass;
        // $relatedGraphQLModel = $relatedGraphQLInstance->model();
        // if ($relatedGraphQLModel instanceof $relatedModel) {
        //     return $relatedGraphQLClass;
        // }

        // Searching for model implementation
        // This is not a good idea. leads to huge execution time penalty.
        // --
        // foreach ($this->allGraphQLClasses() as $pepper) {
        //     $relatedGraphQLInstance = new $pepper;
        //     $relatedGraphQLModel = $relatedGraphQLInstance->model();
        //     if ($relatedGraphQLModel instanceof $relatedModel) {
        //         return $relatedGraphQLClass;
        //     }
        // }

        // throw new ClassNotFoundException("Could not find any Pepper GraphQL class that relates to {$related}.", $relatedGraphQLClass);
    }

    /**
     * Get all GraphQL classes.
     *
     * @return array
     */
    private function allGraphQLClasses(): array
    {
        $classes = [];

        foreach (ClassFinder::getClassesInNamespace(config('pepper.base.namespace.root').'\Http\Pepper\\', 2) as $class) {
            $classes[] = $class;
        }

        return $classes;
    }

    /**
     * Wheter corrosponding GraphQL class for relation exists or not.
     *
     * @param  string  $method
     * @return bool
     */
    public function relatedGraphQLExists(string $method): bool
    {
        return $this->relatedGraphQLClass($method) != ''
            ? true
            : false;
    }

    /**
     * Instance of the related GraphQL class.
     *
     * @param  string  $method
     * @return mixed
     */
    public function relatedGraphQL(string $method)
    {
        $related = $this->relatedGraphQLClass($method);

        return new $related;
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
        $method = Str::studly($method);
        if (method_exists($this, $method)) {
            return $this->$method($args);
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
        return $this->overrideMethod(
            'set'.Str::studly($field).'Type',
            [$this, 'guessFieldType'],
            $field
        );
    }

    /**
     * Guess field type for GraphQL.
     *
     * @param  string  $field
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
     * @param  string  $column
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
        return $this->overrideMethod('setName', [$this, 'studly'], $this->name());
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

    /**
     * Generate name of GraphQL name based on config.
     *
     * @param  string  $name
     * @return string
     */
    protected function generateName(string $name): string
    {
        $studly = config('pepper.base.available.type.{{studly}}'.$name, false);
        $snake = config('pepper.base.available.type.{{snake}}'.$name, false);
        if (
            Str::endsWith($name, 'Type') && $studly ||
            Str::endsWith($name, 'Mutation') && $studly ||
            Str::endsWith($name, 'Query') && $studly ||
            Str::endsWith($name, 'Input') && $studly
        ) {
            return $this->studly().$name;
        } elseif (
            Str::endsWith($name, 'Type') && $snake ||
            Str::endsWith($name, 'Mutation') && $snake ||
            Str::endsWith($name, 'Query') && $snake ||
            Str::endsWith($name, 'Input') && $snake
        ) {
            return $this->snake().$name;
        } else {
            return $this->studly().$name;
        }
    }

    /**
     * Generate description of GraphQL class based on config.
     *
     * @param  string  $name
     * @return string
     */
    protected function generateDescription(string $name): string
    {
        $studly = config('pepper.base.available.type.{{studly}}'.$name, false);
        $snake = config('pepper.base.available.type.{{snake}}'.$name, false);
        if (
            Str::endsWith($name, 'Type') && $studly ||
            Str::endsWith($name, 'Mutation') && $studly ||
            Str::endsWith($name, 'Query') && $studly ||
            Str::endsWith($name, 'Input') && $studly
        ) {
            return $this->studly().$name.' description.';
        } elseif (
            Str::endsWith($name, 'Type') && $snake ||
            Str::endsWith($name, 'Mutation') && $snake ||
            Str::endsWith($name, 'Query') && $snake ||
            Str::endsWith($name, 'Input') && $snake
        ) {
            return $this->snake().$name.' description.';
        } else {
            return $this->studly().$name.' description.';
        }
    }

    /**
     * Generate authorize.
     *
     * @param  mixed ...$params
     * @return bool
     */
    public function generateAuthorize(...$params): bool
    {
        return true;
    }

    /**
     * Generate default authorization message.
     *
     * @return string
     */
    public function generateAuthorizationMessage(): string
    {
        return 'You are not authorized to perform this action';
    }

    /**
     * Generate privacy.
     *
     * @param  mixed  ...$args
     * @return bool
     */
    public function generatePrivacy(...$args): bool
    {
        return true;
    }

    /**
     * Generate rules.
     *
     * @param  mixed  ...$params
     * @return array
     */
    public function generateRules(...$params): array
    {
        return [];
    }

    public function __call(string $method, array $params)
    {
        // Get name
        if (Str::startsWith($method, 'get') && Str::endsWith($method, 'Name')) {
            $needle = Str::replaceFirst('get', '', $method);
            $needle = Str::replaceLast('Name', '', $needle);

            return $this->overrideMethod(
                Str::replaceFirst('get', 'set', $method),
                [$this, 'generateName'],
                $needle
            );
        }

        // Get description
        if (Str::startsWith($method, 'get') && Str::endsWith($method, 'Description')) {
            $needle = Str::replaceFirst('get', '', $method);
            $needle = Str::replaceLast('Description', '', $needle);

            return $this->overrideMethod(
                Str::replaceFirst('get', 'set', $method),
                [$this, 'generateDescription'],
                $needle
            );
        }

        // Get authorization
        if (Str::startsWith($method, 'get') && Str::endsWith($method, 'Authorize')) {
            return $this->overrideMethod(
                Str::replaceFirst('get', 'set', $method),
                [$this, 'generateAuthorize'],
                $params
            );
        }

        // Get authorization message
        if (Str::startsWith($method, 'get') && Str::endsWith($method, 'AuthorizationMessage')) {
            return $this->overrideMethod(
                Str::replaceFirst('get', 'set', $method),
                [$this, 'generateAuthorizationMessage'],
                $params
            );
        }

        // Get privacy
        if (Str::startsWith($method, 'get') && Str::endsWith($method, 'Privacy')) {
            return $this->overrideMethod(
                Str::replaceFirst('get', 'set', $method),
                [$this, 'generatePrivacy'],
                $params
            );
        }

        // Get rules
        if (Str::startsWith($method, 'get') && Str::endsWith($method, 'Rules')) {
            return $this->overrideMethod(
                Str::replaceFirst('get', 'set', $method),
                [$this, 'generateRules'],
                $params
            );
        }

        throw new BadMethodCallException(sprintf(
            'Method %s::%s does not exist.',
            static::class,
            $method
        ));
    }
}
