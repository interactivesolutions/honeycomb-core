<?php

namespace interactivesolutions\honeycombcore\commands;

use Illuminate\Console\Command;

class HCCommand extends Command
{
    /**
     * Creating directory if not exists
     *
     * @param $path
     */
    public function createDirectory($path)
    {
        if (!file_exists($path))
            mkdir($path);
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
}