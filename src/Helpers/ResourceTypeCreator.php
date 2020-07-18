<?php

namespace Amirmasoud\Pepper\Helpers;

use Closure;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ResourceTypeCreator
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    protected $path;

    /**
     * Create a new resource type creator instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  string  $customStubPath
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
        $this->path = app_path('GraphQL/Types/Pepper');
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
        $this->resetResourceTypeClass($class, $this->path);

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

        return $class;
    }

    /**
     * Ensure that a migration with the given name doesn't already exist.
     *
     * @param  string  $name
     * @param  string  $migrationPath
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function resetResourceTypeClass($name, $resourceTypePath = null)
    {
        if (!empty($resourceTypePath)) {
            $resourceTypesPaths = $this->files->glob($resourceTypePath . '/*.php');

            foreach ($resourceTypesPaths as $resourceTypeFile) {
                $this->files->requireOnce($resourceTypeFile);
                if (class_exists($this->getClassName($name))) {
                    $this->files->delete($resourceTypeFile);
                }
            }
        }
    }

    /**
     * Get the resource type stub file.
     *
     * @return string
     */
    protected function getStub()
    {
        $stub = $this->stubPath() . '/type.stub';

        return $this->files->get($stub);
    }

    /**
     * Populate the place-holders in the resource type stub.
     *
     * @param  string  $name
     * @param  string|null  $value
     * @param  string  $stub
     * @return string
     */
    protected function populateStub($name, $value, $stub)
    {
        $stub = str_replace("{{ $name }}", $value, $stub);

        return $stub;
    }

    /**
     * Get the class name of a resource type name.
     *
     * @param  string  $name
     * @return string
     */
    protected function getClassName($name)
    {
        return Str::studly($name);
    }

    /**
     * Get the full path to the resource type.
     *
     * @param  string  $name
     * @param  string  $path
     * @return string
     */
    protected function getPath($name, $path)
    {
        return $path . '/' . $name . '.php';
    }

    /**
     * Get the path to the stubs.
     *
     * @return string
     */
    public function stubPath()
    {
        return __DIR__ . '/../stubs';
    }

    /**
     * Get the filesystem instance.
     *
     * @return \Illuminate\Filesystem\Filesystem
     */
    public function getFilesystem()
    {
        return $this->files;
    }
}
