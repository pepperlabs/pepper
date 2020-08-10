<?php

namespace Pepper\Helpers;

use Illuminate\Support\Str;

class ResourceMutationDeleteCreator extends ResourceCreator
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

        $this->path = app_path('GraphQL/Mutations/Pepper');
        $this->stub = '/mutationDelete.stub';
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
        $name = Str::of($name)->snake();
        $key = 'delete_' . $name;
        if ($this->configKeyExists('graphql.schemas.default.mutation.' . $key)) {
            $class = strval('App\GraphQL\Mutations\Pepper\\' . $name . 'DeleteMutation::class');
            $pattern = '/(\s*["\']schemas["\']\s*=>\s*\[\s*["\']default["\']\s*=>\s*\[\s*["\']query["\']\s*=>\s*\[\s*[^"]+?(?=["\']mutation["\'])["\']mutation["\']\s*=>\s*\[\s*)/';
            $replace = "$0 '$key' => $class,\n                ";
            $update = preg_replace($pattern, $replace, file_get_contents($this->config));
            file_put_contents($this->config, $update);
        }
    }
}
