<?php

namespace interactivesolutions\honeycombcore\commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class HCCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hc:command';

    /**
     * Replaceable symbols
     *
     * @var array
     */
    protected $toReplace = ['.', '_', '/', ' ', '-', ':'];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Create folder recursively if not exists.
     *
     * @param string $path
     * @return bool
     */
    public function createDirectory(string $path)
    {
        if( ! is_dir($path) )
            return mkdir($path, 0755, true);
    }

    /**
     * Deleting existing folder
     *
     * @param string $path
     * @param bool $withFiles
     */
    public function deleteDirectory(string $path, bool $withFiles = false)
    {
        if( $path == '*' )
            $this->abort('Can not delete "*", please specify folder or file.');

        $files = glob($path . '/*');

        foreach ( $files as $file ) {
            if( is_file($file) && ! $withFiles ) return;
            is_dir($file) ? $this->deleteDirectory($file, $withFiles) : unlink($file);
        }
        if( is_dir($path) )
            try {
                rmdir($path);
                $this->info('Deleting folder: ' . $path);
            } catch ( \Exception $e ) {
                $this->comment('Can not delete ' . $path . ', it might contain hidden files, such as .gitignore');
            }
    }

    /**
     * Replace file
     * @param $configuration
     * @internal param $destination
     * @internal param $templateDestination
     * @internal param array $content
     */
    public function createFileFromTemplate(array $configuration)
    {
        $destination = $configuration['destination'];
        $templateDestination = $configuration['templateDestination'];

        if( $destination[0] == '/' )
            $preserveSlash = '/';
        else
            $preserveSlash = '';

        if( ! isset($destination) )
            $this->error('File creation failed, destination not set');

        if( ! isset($templateDestination) )
            $this->error('File creation failed, template destination not set');

        $destination = str_replace('\\', '/', $destination);

        $template = file_get_contents($templateDestination);

        if( isset($configuration['content']) )
            $template = replaceBrackets($template, $configuration['content']);

        $directory = array_filter(explode('/', $destination));
        array_pop($directory);
        $directory = $preserveSlash . implode('/', $directory);

        $this->createDirectory($directory);
        file_put_contents($destination, $template);

        $this->info('Created: ' . $destination);
    }

    /**
     * Get replaceable symbols
     *
     * @param array $ignoreSymbols
     * @return array
     */
    public function getToReplace(array $ignoreSymbols): array
    {
        if( empty($ignoreSymbols) )
            return $this->toReplace;

        return array_diff($this->toReplace, $ignoreSymbols);
    }

    /**
     * Get lower string
     *
     * @param $string
     * @return string
     */
    protected function stringToLower(string $string)
    {
        return strtolower(trim($string, '/'));
    }

    /**
     * Make string in dot from slashes
     *
     * @param string $string
     * @param array $ignoreToReplace
     * @return mixed
     */
    protected function stringWithDots(string $string, array $ignoreToReplace = [])
    {
        return str_replace($this->getToReplace($ignoreToReplace), '.', $string);
    }

    /**
     * Get string in underscore
     *
     * @param string $string
     * @param array $ignoreToReplace
     * @return mixed
     */
    protected function stringWithUnderscore(string $string, array $ignoreToReplace = [])
    {
        return str_replace($this->getToReplace($ignoreToReplace), '_', trim($string, '/'));
    }

    /**
     * Get string in dash
     *
     * @param string $string
     * @param array $ignoreToReplace
     * @return mixed
     */
    protected function stringWithDash(string $string, array $ignoreToReplace = [])
    {
        return str_replace($this->getToReplace($ignoreToReplace), '-', trim($string, '/'));
    }

    /**
     * Remove all items from string
     *
     * @param string $string
     * @param array $ignoreToReplace
     * @return mixed
     */
    protected function stringOnly(string $string, array $ignoreToReplace = [])
    {
        return str_replace($this->getToReplace($ignoreToReplace), '', trim($string, '/'));
    }

    /**
     * Aborting the command sequence
     *
     * @param string $message
     */
    protected function abort(string $message)
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
        $file = new Filesystem();

        $projectFiles = $file->glob(app_path('honeycomb/config.json'));
        $packageFiles = $file->glob(__DIR__ . '/../../../../*/*/*/*/honeycomb/config.json');

        return array_merge($packageFiles, $projectFiles);
    }
}