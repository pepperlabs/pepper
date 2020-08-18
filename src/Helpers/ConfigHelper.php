<?php

namespace Pepper\Helpers;

use Illuminate\Config\Repository;

/**
 * Update configuration of GraphQL file.
 *
 * Example usage:
 *
 * $config = new ConfigHelper($GraphQLConfigPath);
 *
 * $config = new ConfigHelper(); // fallback to default graphql.php file
 *
 * $config->addType('key', 'class');
 *
 * $config->addQuery('key', 'class');
 *
 * $config->addMutation('key', 'class');
 *
 * @author Amirmasoud Sheydaei <amirmasoud.sheydaei@gmail.com>
 * @since 1.0.0
 */
class ConfigHelper
{
    /** @var string */
    public $path;

    /** @var Illuminate\Config\Repository */
    public $repository;

    /**
     * Inititate config helper.
     *
     * @param string|null $path
     *
     * @throws ErrorException
     */
    public function __construct(?string $path)
    {
        $this->path = $path ?? config_path('graphql.php');
        $this->repository = new Repository(include $this->path);
    }

    /**
     * Add a new key to types array.
     *
     * @param  string $key
     * @param  string $class
     * @param  string $namespace default: App\GraphQL\Types\Pepper\\
     * @return void
     */
    public function addType(string $key, string $class, string $namespace = 'App\GraphQL\Types\Pepper\\'): void
    {
        if ($this->canAdd('types.' . $key)) {
            $pattern = '/[^\/]{2,}\s*["\']types["\']\s*=>\s*\[\s*/';
            $class = strval($namespace . $class . '::class');
            $update = preg_replace($pattern, "$0'$key' => $class,\n        ", file_get_contents($this->path));
            file_put_contents($this->path, $update);
        }
    }

    /**
     * Add a new key to query array.
     *
     * @param  string $key
     * @param  string $class
     * @return void
     */
    public function addQuery(string $key, string $class): void
    {
        if ($this->canAdd('schemas.default.query.' . $key)) {
            $pattern = '/\s*["\']schemas["\']\s*=>\s*\[\s*["\']default["\']\s*=>\s*\[\s*["\']query["\']\s*=>\s*\[\s*/';
            $class = strval('App\GraphQL\Queries\Pepper\\' . $class . '::class');
            $replace = "$0 '$key' => $class,\n              ";
            $update = preg_replace($pattern, $replace, file_get_contents($this->path));
            file_put_contents($this->path, $update);
        }
    }

    /**
     * Add a new key to mutation array.
     *
     * @param  string $key
     * @param  string $class
     * @return void
     */
    public function addMutation(string $key, string $class): void
    {
        if ($this->canAdd('schemas.default.mutation.' . $key)) {
            $pattern = '/(\s*["\']schemas["\']\s*=>\s*\[\s*["\']default["\']\s*=>\s*\[\s*["\']query["\']\s*=>\s*\[\s*[^"]+?(?=["\']mutation["\'])["\']mutation["\']\s*=>\s*\[\s*)/';
            $class = strval('App\GraphQL\Mutations\Pepper\\' . $class . '::class');
            $replace = "$0 '$key' => $class,\n              ";
            $update = preg_replace($pattern, $replace, file_get_contents($this->path));
            file_put_contents($this->path, $update);
        }
    }

    /**
     * Check if we can add the given key by checking the nullability of the
     * given key in the config file.
     *
     * @param  string $key
     * @return bool
     */
    protected function canAdd($key): bool
    {
        return !$this->repository->has($key);
    }
}
