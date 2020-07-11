<?php

namespace Amirmasoud\Pepper\Helpers;

use Closure;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ResourceQueryCreator
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    protected $path;

    /**
     * Create a new resource query creator instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  string  $customStubPath
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
        $this->path = app_path('GraphQL/Queries');
    }

    /**
     * Create a new resource query at the given path.
     *
     * @param  string  $name
     * @param  string  $path
     * @param  string|null  $class
     * @param  bool  $create
     * @return string
     */
    public function create($class, $name, $description, $type, $args, $resolve)
    {
        $this->resetResourceQueryClass($class, $this->path);

        $stub = $this->getStub();

        $this->files->ensureDirectoryExists($this->path);

        $stub = $this->populateStub('class', $class, $stub);
        $stub = $this->populateStub('name', $name, $stub);
        $stub = $this->populateStub('description', $description, $stub);
        $stub = $this->populateStub('type', $type, $stub);
        $stub = $this->populateStub('args', $args, $stub);
        $stub = $this->populateStub('resolve', $resolve, $stub);

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
    protected function resetResourceQueryClass($name, $resourceQueryPath = null)
    {
        if (!empty($resourceQueryPath)) {
            $resourceQueriesPaths = $this->files->glob($resourceQueryPath . '/*.php');

            foreach ($resourceQueriesPaths as $resourceQueryFile) {
                $this->files->requireOnce($resourceQueryFile);
                if (class_exists($this->getClassName($name))) {
                    $this->files->delete($resourceQueryFile);
                }
            }
        }
    }

    /**
     * Get the resource query stub file.
     *
     * @return string
     */
    protected function getStub()
    {
        $stub = $this->stubPath() . '/query.stub';

        return $this->files->get($stub);
    }

    /**
     * Populate the place-holders in the resource query stub.
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
     * Get the class name of a resource query name.
     *
     * @param  string  $name
     * @return string
     */
    protected function getClassName($name)
    {
        return Str::studly($name);
    }

    /**
     * Get the full path to the resource query.
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
