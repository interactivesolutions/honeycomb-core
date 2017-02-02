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
    protected $file;

    /**
     * Create a new controller creator command instance.
     *
     * @param \Illuminate\Filesystem\Filesystem|Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->file = $files;
    }

    /**
     * Creating directory if not exists
     *
     * @param $path
     */
    public function createDirectory($path)
    {
        if(!$this->file->exists($path))
            mkdir($path, 0755, true);
    }

    /**
     * Deleting existing directory
     *
     * @param $path
     * @param bool $withFiles
     */
    public function deleteDirectory($path, $withFiles = false)
    {
        //TODO: Get rid of exec command here by using rmdir construct.
        if ($path == '*')
        {
            $this->info('Can not delete "*", please specify directory');

            return;
        }

        if ($withFiles)
            $withFiles = ' -R ';
        else
            $withFiles = ' ';

        if (file_exists($path))
        {
            shell_exec('rm' . $withFiles . $path);
            $this->info($path . ' directory deleted');
        } else
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

        $template = $this->file->get($templateDestination);

        if (isset($configuration['content']))
            $template = replaceBrackets($template, $configuration['content']);

        $directory = array_filter(explode('/', $destination));
        array_pop($directory);
        $directory = implode('/', $directory);

        $this->createDirectory($directory);
        $this->file->put($destination, $template);

        $this->info('Created: ' . $destination);
    }

    /**
     * Get lower string
     *
     * @param $string
     * @return string
     */
    protected function stringToLower($string)
    {
        return strtolower(trim($string, '/'));
    }

    /**
     * Make string in dot from slashes
     *
     * @param $string
     * @return mixed
     */
    protected function stringWithDots($string)
    {
        return str_replace(['_', '/', ' ', '-'], '.', $string);
    }

    /**
     * Get string in underscore
     *
     * @param $string
     * @return mixed
     */
    protected function stringWithUnderscore($string)
    {
        return str_replace(['.', '/', ' ', '-'], '_', trim($string, '/'));
    }

    /**
     * Get string in dash
     *
     * @param $string
     * @return mixed
     */
    protected function stringWithDash($string)
    {
        return str_replace(['.', '/', ' ', '_'], '-', trim($string, '/'));
    }

    /**
     * Remove all items from string
     *
     * @param $string
     * @return mixed
     */
    protected function stringOnly($string)
    {
        return str_replace(['.', ' ', '_', '-'], '', trim($string, '/'));
    }

    /**
     * Aborting the command sequence
     *
     * @param $message
     */
    protected function abort($message)
    {
        $this->error($message);
        $this->executeAfterAbort();
        dd();
    }

    /**
     * Function can be overridden by subclass to restore initial data
     */
    protected function executeAfterAbort()
    {

    }

    /**
     * Scan folders for honeycomb configuration files
     *
     * @return array
     */
    protected function getConfigFiles()
    {
        $projectFiles = $this->file->glob(app_path('honeycomb/config.json'));
        $packageFiles = $this->file->glob(__DIR__ . '/../../../../*/*/*/*/honeycomb/config.json');
        
        return array_merge($packageFiles, $projectFiles);
    }
}