<?php

namespace Pepper;

use ReflectionClass;

use Illuminate\Support\Facades\DB;

abstract class GraphQL
{
    /** @var object */
    // protected $model;

    /** @var array */
    // protected $exposed = [];

    /** @var array */
    // protected $covered = [];

    /**
     * Get class name.
     *
     * @return string
     */
    private function getClassName(): string
    {
        return (new ReflectionClass($this))->getShortName();
    }

    /**
     * Get model by class name.
     *
     * @return void
     */
    public function getModel(): string
    {
        // @todo check for appended slash
        return property_exists($this, 'model')
            ? $this->model
            : config('pepper.namespace') . '\\' . $this->getClassName();
    }

    /**
     * Make new model reflection.
     *
     * @return object
     */
    private function newModelReflection(): object
    {
        return new ReflectionClass($this->getModel());
    }

    /**
     * Get new instance of the model.
     *
     * @return object
     */
    private function newModel(): object
    {
        return $this->newModelReflection()->newInstanceArgs();
    }

    /**
     * Get list of model's methods.
     *
     * @return array
     */
    private function getModelMethods(): array
    {
        return $this->newModelReflection()->getMethods();
    }

    /**
     * Get exposed fields.
     *
     * @return array
     */
    public function exposedFields(): array
    {
        if (property_exists($this, 'exposed')) {
            return $this->exposed;
        } else {
            $model = $this->newModel();
            $table = $model->getTable();
            return $model->getConnection()
                ->getSchemaBuilder()
                ->getColumnListing($table);
        }
    }

    public function coveredFields(): array
    {
        return property_exists($this, 'covered')
            ? $this->covered
            : [];
    }

    /**
     * List of model fields.
     *
     * @return array
     */
    public function fields(): array
    {
        return array_values(array_diff($this->exposedFields(), $this->coveredFields()));
    }

    /**
     * Generate GraphQL fields with field types.
     *
     * @return array
     */
    public function graphQLFields(): array
    {
        $fields = [];

        foreach ($this->fields() as $attribute) {
            $fields[$attribute] = [
                'name' => $attribute,
                'type' => call_user_func('\GraphQL\Type\Definition\Type::' . $this->guessFieldType($attribute))
            ];
        }

        return $fields;
    }

    /**
     * Map table column types to GraphQL types.
     *
     * @param  string $field
     * @return string
     */
    public function getFieldType(string $field): string
    {
        /** @todo Make it available in config */
        $method = 'set' . $field . 'Type';
        if (method_exists($this, $method)) {
            return $this->overrideFieldType($field, $method);
        } else {
            return $this->guessFieldType($field);
        }
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
            case 'blob':
                /** @todo */
                return 'upload';
            case 'boolean':
                return 'boolean';
            case 'bigint':
            case 'decimal':
            case 'string':
            case 'text':
            case 'guid':
            case 'date':
            case 'datetime':
            case 'datetimez':
            case 'time':
                return 'string';
            case 'array':
            case 'json_array':
                /** @todo */
                return 'listOf';
            case 'object':
                /** @todo */
                return 'stdObj';
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
        $table = $this->newModel()->getTable();
        return DB::getSchemaBuilder()->getColumnType($table, $column);
    }

    /**
     * Override field type by calling set[Field]Type method.
     *
     * @param  string $field
     * @param  string $method
     * @return string
     */
    private function overrideFieldType(string $field, string $method): string
    {
        return $this->$method($field);
    }

    public function endpointRelations(): array
    {
        $relations = [];
        foreach ($this->getModelMethods() as $method) {
            $returnType = $method->getReturnType();
            if ($returnType) {
                if (in_array(class_basename($returnType->getName()), config('pepper.relations'))) {
                    $relations[] = $method->name;
                }
            }
        }

        $exposedRelations = $this->exposedRelations ?? $relations;
        $hiddenRelations = $this->hiddenRelations ?? [];
        $relations = array_values(array_diff($exposedRelations, $hiddenRelations));

        $fields = [];
        foreach ($relations as $relation) {
            $reflector = new \ReflectionClass($model);
            $relationType = $reflector->getMethod($relation)->getReturnType()->getName();
            $type = '';
            if ($relationType === BelongsTo::class) {
                $type = GraphQL::type($this->getTypeName());
            } elseif ($relationType === HasMany::class) {
                $type = Type::listOf(GraphQL::type($this->getTypeName()));
            }

            $fields[$relation] = [
                'name' => $relation,
                'type' => $type,
                'resolve' => function ($root, $args) use ($relation) {
                    return $root->{$relation};
                }
            ];
        }

        return $fields;
    }
}
