<?php

declare(strict_types=1);

namespace Pepper\Console;

use Illuminate\Console\GeneratorCommand;
use Pepper\Helpers\ConfigHelper as Config;
use Symfony\Component\Console\Input\InputArgument;

abstract class BaseMakeCommand extends GeneratorCommand
{
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'class';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the class'],
            ['class', InputArgument::REQUIRED, 'The name of the GraphQL class'],
            ['description', InputArgument::REQUIRED, 'The description of the GraphQL class'],
            ['model', InputArgument::REQUIRED, 'The model of the GraphQL class'],
        ];
    }

    /**
     * Build the class with the given arguments.
     *
     * @param  string  $name
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        return $this->replaceName($stub, $this->argument('class'))
            ->replaceDescription($stub, $this->argument('description'))
            ->replaceModel($stub, $this->argument('model'));
    }

    /**
     * Replace the name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return $this
     */
    protected function replaceName(&$stub, $name)
    {
        $stub = str_replace(['DummyName', '{{ name }}', '{{name}}'], $name, $stub);

        return $this;
    }

    /**
     * Replace the description for the given stub.
     *
     * @param  string  $stub
     * @param  string  $description
     * @return $this
     */
    protected function replaceDescription(&$stub, $description)
    {
        $stub = str_replace(['DummyDescription', '{{ description }}', '{{description}}'], $description, $stub);

        return $this;
    }

    /**
     * Replace the class model for the given stub.
     *
     * @param  string  $stub
     * @param  string  $model
     * @return string
     */
    protected function replaceModel($stub, $model)
    {
        return str_replace(['DummyModel', '{{ model }}', '{{model}}'], $model, $stub);
    }

    /**
     * Execute the console command.
     *
     * @return bool|null
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        parent::handle();

        if (!$this->hasOption('--no-config') || !$this->option('--no-config')) {
            $this->ensureGraphQLConfigExists();
            $config = new Config(null);
            $gql = strtolower($this->gql);

            if ($gql == 'type') {
                $config->addType($this->argument('name'), $this->argument('class'));
            } elseif ($gql == 'input') {
                $config->addType($this->argument('name'), $this->argument('class'), 'App\GraphQL\Inputs\Pepper\\');
            } elseif ($gql == 'query') {
            } elseif ($gql == 'mutation') {
            }
        }
    }

    /**
     * Ensure GraphQL config file exists, otherwise we would publish a new one.
     *
     * @return void
     */
    private function ensureGraphQLConfigExists(): void
    {
        if (!file_exists(config_path('graphql.php'))) {
            $this->info('Publishing default graphql config...');
            $this->call('vendor:publish', [
                '--provider' => 'Rebing\GraphQL\GraphQLServiceProvider'
            ]);
        }
    }
}
