<?php

namespace Pepper\Helpers;

use Illuminate\Support\Str;

class ResourceQueryAggregateCreator extends ResourceCreator
{
    protected $path;
    protected $stub;

    /**
     * Create a new resource query creator instance.
     *
     * @param  string  $customStubPath
     * @return void
     */
    public function __construct()
    {
        Parent::__construct();

        $this->path = app_path('GraphQL/Queries/Pepper');
        $this->stub = '/queryAggregate.stub';
    }

    public function create($class, $name, $description, $model)
    {
        $this->resetResourceClass($class, $this->path);

        $stub = $this->getStub();

        $this->files->ensureDirectoryExists($this->path);

        $stub = $this->populateStub('class', $class, $stub);
        $stub = $this->populateStub('name', $name, $stub);
        $stub = $this->populateStub('description', $description, $stub);
        $stub = $this->populateStub('model', $model, $stub);

        $this->files->replace(
            $class = $this->getPath($class, $this->path),
            $stub
        );

        $this->updateConfig($name);

        return $class;
    }

    protected function updateConfig($name)
    {
        $className = Str::of($name)->studly();
        $name = Str::of($name . '_aggregate')->snake();
        if ($this->configKeyExists('graphql.schemas.default.query.' . $name)) {
            $class = strval('App\GraphQL\Queries\Pepper\\' . $className . 'AggregateQuery::class');
            $pattern = '/\s*["\']schemas["\']\s*=>\s*\[\s*["\']default["\']\s*=>\s*\[\s*["\']query["\']\s*=>\s*\[\s*/';
            $replace = "$0 '$name' => $class,\n                ";
            $update = preg_replace($pattern, $replace, file_get_contents($this->config));
            file_put_contents($this->config, $update);
        }
    }
}
