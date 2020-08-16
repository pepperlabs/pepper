<?php

namespace Pepper\Helpers;

class ResourceHttpCreator extends ResourceCreator
{
    protected $path;
    protected $stub;

    /**
     * Create a new resource mutation creator instance.
     *
     * @param  string  $customStubPath
     * @return void
     */
    public function __construct()
    {
        Parent::__construct();

        $this->path = app_path('Http/Pepper');
        $this->stub = '/http.stub';
    }

    /**
     * Create a new resource input at the given path.
     *
     * @param  string  $class
     *
     * @return string
     */
    public function create(string $class)
    {
        if ($this->files->exists($this->getPath($class, $this->path))) {
            return;
        }

        $this->resetResourceClass($class, $this->path, false);

        $stub = $this->getStub();

        $this->files->ensureDirectoryExists($this->path);

        $stub = $this->populateStub('class', $class, $stub);

        $this->files->replace(
            $class = $this->getPath($class, $this->path),
            $stub
        );
    }

    protected function updateConfig($name)
    {
        //
    }
}
