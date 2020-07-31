<?php

namespace Amirmasoud\Pepper\Support;

use Illuminate\Filesystem\Filesystem;

class FileWriter
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The default configuration path.
     *
     * @var string
     */
    protected $defaultPath;

    /**
     * The config rewriter object.
     *
     * @var \October\Rain\Config\DataWriter\Rewrite
     */
    protected $rewriter;

    /**
     * Create a new file configuration loader.
     *
     * @param  \Illuminate\Filesystem\Filesystem $files
     * @param  string $defaultPath
     * @return void
     */
    public function __construct(Filesystem $files, string $defaultPath)
    {
        $this->files = $files;
        $this->defaultPath = $defaultPath;
        $this->rewriter = new Rewrite;
    }

    /**
     * Write an item value in a file.
     *
     * @param  string $item
     * @param  mixed $value
     * @param  string $filename
     * @return bool
     */
    public function write(string $item, $value, string $filename): bool
    {
        $path = config_path($filename) . '.php';

        $contents = $this->files->get($path);
        $contents = $this->rewriter->toContent($contents, [$item => $value]);

        return !($this->files->put($path, $contents) === false);
    }
}
