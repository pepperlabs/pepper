<?php

namespace Amirmasoud\Pepper\Helpers;

class ResourceTypeCreator extends ResourceCreator
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

        $this->path = app_path('GraphQL/Types/Pepper');
        $this->stub = '/type.stub';
    }

    /**
     * Create a new resource type at the given path.
     *
     * @param  string  $name
     * @param  string  $path
     * @param  string|null  $class
     * @param  bool  $create
     * @return string
     */
    public function create($class, $name, $description, $model)
    {
        $this->resetResourceClass($class, $this->path);

        $stub = $this->getStub();

        $this->files->ensureDirectoryExists($this->path);

        $stub = $this->populateStub('class', $class, $stub);
        $stub = $this->populateStub('model', $model, $stub);
        $stub = $this->populateStub('name', $name, $stub);
        $stub = $this->populateStub('description', $description, $stub);

        $this->files->replace(
            $class = $this->getPath($class, $this->path),
            $stub
        );

        $this->updateConfig($name);

        return $class;
    }

    protected function updateConfig($name)
    {
        if ($this->configKeyExists('graphql.types.' . $name)) {
            $pattern = '/[^\/]{2,}\s*["\']types["\']\s*=>\s*\[\s*/';
            $class = strval('App\GraphQL\Types\Pepper\\' . $name . 'Type::class');
            $update = preg_replace($pattern, "$0 '$name' => $class,\n        ", file_get_contents($this->config));
            file_put_contents($this->config, $update);
        }
    }
}
