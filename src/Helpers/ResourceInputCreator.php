<?php

namespace Amirmasoud\Pepper\Helpers;

use Closure;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ResourceInputCreator
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    protected $path;

    /**
     * Create a new resource input creator instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  string  $customStubPath
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
        $this->path = app_path('GraphQL/Inputs/Pepper');
    }

    /**
     * Create a new resource input at the given path.
     *
     * @param  string  $name
     * @param  string  $path
     * @param  string|null  $class
     * @param  bool  $create
     * @return string
     */
    public function create($class, $name, $description, $model)
    {
        $this->resetResourceInputClass($class, $this->path);

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
    protected function resetResourceInputClass($name, $resourceInputPath = null)
    {
        if (!empty($resourceInputPath)) {
            $resourceInputsPaths = $this->files->glob($resourceInputPath . '/*.php');

            foreach ($resourceInputsPaths as $resourceInputFile) {
                $this->files->requireOnce($resourceInputFile);
                if (class_exists($this->getClassName($name))) {
                    $this->files->delete($resourceInputFile);
                }
            }
        }
    }

    /**
     * Get the resource input stub file.
     *
     * @return string
     */
    protected function getStub()
    {
        $stub = $this->stubPath() . '/input.stub';

        return $this->files->get($stub);
    }

    /**
     * Populate the place-holders in the resource input stub.
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
     * Get the class name of a resource input name.
     *
     * @param  string  $name
     * @return string
     */
    protected function getClassName($name)
    {
        return Str::studly($name);
    }

    /**
     * Get the full path to the resource input.
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
