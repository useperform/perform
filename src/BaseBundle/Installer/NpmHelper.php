<?php

namespace Perform\BaseBundle\Installer;

use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;

/**
 * Shortcuts for npm-related tasks in installers.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class NpmHelper
{
    /**
     * Install npm packages in a given directory.
     *
     * @param string          $directory The directory path
     * @param LoggerInterface $logger
     */
    public static function install($dir, LoggerInterface $logger)
    {
        if (!is_dir($dir)) {
            throw new \RuntimeException(sprintf('The directory "%s" does not exist.', $dir));
        }

        $logger->info('Installing npm packages in '.realpath($dir));
        $executable = static::getNpmExecutable($logger);

        ProcessHelper::mustRun($executable.' install', $logger, $dir);
    }

    /**
     * Get the name of the executable to use when installing packages.
     *
     * @return string npm or yarn
     */
    public static function getNpmExecutable(LoggerInterface $logger)
    {
        $process = new Process('which npm');
        if ($process->run() !== 0) {
            throw new \RuntimeException('Npm executable not found. Please install nodejs and npm before continuing.');
        }

        $process = new Process('which yarn');
        if ($process->run() !== 0) {
            $logger->warning('Yarn executable not found. Consider installing yarn with "npm install -g yarn" to speed up asset building.'.PHP_EOL);

            return 'npm';
        }

        return 'yarn';
    }
}
