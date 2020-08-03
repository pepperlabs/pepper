<?php

namespace Pepper\Helpers;

use Illuminate\Filesystem\Filesystem;
use InvalidArgumentException;
use Illuminate\Support\Str;

abstract class ResourceCreator
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    protected $config;

    /**
     * Create a new resource query creator instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  string  $customStubPath
     * @return void
     */
    public function __construct()
    {
        $this->files = new Filesystem;
        $this->config = config_path('graphql.php');
    }

    abstract protected function updateConfig($name);

    protected function configKeyExists($key)
    {
        return file_exists($this->config) && is_null(config($key));
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
    protected function resetResourceClass($name, $resourcePath = null)
    {
        if (!empty($resourcePath)) {
            $resourcePaths = $this->files->glob($resourcePath . '/*.php');

            foreach ($resourcePaths as $resourceFile) {
                $this->files->requireOnce($resourceFile);
                if (class_exists($this->getClassName($name))) {
                    $this->files->delete($resourceFile);
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
        $stub = $this->stubPath() . $this->stub;

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
