<?php

namespace interactivesolutions\honeycombcore\commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class HCCommand extends Command
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new controller creator command instance.
     *
     * @param \Illuminate\Filesystem\Filesystem|Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Creating directory if not exists
     *
     * @param $path
     */
    public function createDirectory($path)
    {
        $path = str_replace('\\', '/', $path);
        $path = explode('/', $path);

        $finalDirectory = '';

        foreach($path as $directory)
        {
            $finalDirectory .= $directory;

            if (!file_exists($finalDirectory))
            {
                print_r($finalDirectory . ' created');
                mkdir($finalDirectory);
            }

            $finalDirectory .= '/';
        }
    }

    /**
     * Deleting existing directory
     *
     * @param $path
     * @param bool $withFiles
     */
    public function deleteDirectory($path, $withFiles = false)
    {
        if ($path == '*') {
            $this->info('Can not delete "*", please specify directory');
            return;
        }

        if ($withFiles)
            $withFiles = ' -R ';
        else
            $withFiles = ' ';

        if (file_exists($path))
            shell_exec('rm' . $withFiles . $path);
        else
            $this->info($path . ' directory does not exists');
    }

    /**
     * Replace file
     * @param $configuration
     * @internal param $destination
     * @internal param $templateDestination
     * @internal param array $content
     */
    public function createFileFromTemplate($configuration)
    {
        $destination = $configuration['destination'];
        $templateDestination = $configuration['templateDestination'];

        if (!isset($destination))
            $this->error('File creation failed, destination not set');

        if (!isset($templateDestination))
            $this->error('File creation failed, template destination not set');

        $destination = str_replace('\\', '/', $destination);

        $template = $this->files->get($templateDestination);
        $template = replaceBrackets($template, $configuration['content']);

        $this->files->put($destination, $template);

    }
}