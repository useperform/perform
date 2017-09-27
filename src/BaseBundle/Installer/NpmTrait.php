<?php

namespace Perform\BaseBundle\Installer;

use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;

/**
 * Shortcuts for npm-related tasks in installers.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
trait NpmTrait
{
    /**
     * Install npm packages in a given directory.
     *
     * @param LoggerInterface $logger
     * @param string          $directory The directory path
     */
    public function npmInstall(LoggerInterface $logger, $dir)
    {
        if (!is_dir($dir)) {
            throw new \RuntimeException(sprintf('The directory "%s" does not exist.', $dir));
        }

        $logger->info('Installing npm packages in '.realpath($dir));
        $executable = $this->getNpmExecutable($logger);

        $process = new Process($executable.' install', $dir);
        $process->setTimeout(300);
        $process->mustRun(function ($type, $buffer) use ($logger) {
            if (Process::ERR === $type) {
                $logger->warning($buffer);
            } else {
                $logger->info($buffer);
            }
        });
    }

    /**
     * Get the name of the executable to use when installing packages.
     *
     * @return string npm or yarn
     */
    public function getNpmExecutable(LoggerInterface $logger)
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
