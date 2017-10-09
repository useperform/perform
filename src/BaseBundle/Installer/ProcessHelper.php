<?php

namespace Perform\BaseBundle\Installer;

use Symfony\Component\Process\Process;
use Psr\Log\LoggerInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ProcessHelper
{
    /**
     * Run a command and return the exit status code.
     *
     * @param string                   $command
     * @param LoggerInterface|callable $logger
     * @param string|null              $cwd
     * @param int|null                 $timeout
     *
     * @return int
     */
    public static function run($command, $logger, $cwd = null, $timeout = 60)
    {
        $process = new Process($command, $cwd);
        $process->setTimeout($timeout);

        return $process->run(static::getCallback($logger, __METHOD__));
    }

    /**
     * Run a command and throw an exception if it fails.
     *
     * @param string                   $command
     * @param LoggerInterface|callable $logger
     * @param string|null              $cwd
     * @param int|null                 $timeout
     *
     * @return Process
     */
    public static function mustRun($command, $logger, $cwd = null, $timeout = 60)
    {
        $process = new Process($command, $cwd);
        $process->setTimeout($timeout);

        return $process->mustRun(static::getCallback($logger, __METHOD__));
    }

    private static function getCallback($logger, $method)
    {
        return is_callable($logger) ? $logger : function ($type, $buffer) use ($logger, $method) {
            if (!$logger instanceof LoggerInterface) {
                throw new \InvalidArgumentException(sprintf('Argument 2 passed to %s must be an instance of %s or a callable function.', $method, LoggerInterface::class));
            }

            if (Process::ERR === $type) {
                $logger->warning($buffer);
            } else {
                $logger->info($buffer);
            }
        };
    }
}
