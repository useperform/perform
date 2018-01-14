<?php

namespace Perform\DevBundle\File;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;

/**
 * FileCreator.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FileCreator
{
    protected $fs;
    protected $twig;
    protected $input;
    protected $output;
    protected $helperSet;

    const OPTION_SKIP_EXISTING = 'skip-existing';
    const OPTION_FORCE = 'force';

    public function __construct(Filesystem $fs, \Twig_Environment $twig)
    {
        $this->fs = $fs;
        $this->twig = $twig;
    }

    public function onConsoleCommand(ConsoleCommandEvent $event)
    {
        $input = $event->getInput();
        $output = $event->getOutput();
        $helperSet = $event->getCommand()->getHelperSet();

        $this->setConsoleEnvironment($input, $output, $helperSet);
    }

    public function setConsoleEnvironment(InputInterface $input, OutputInterface $output, HelperSet $helperSet)
    {
        $this->input = $input;
        $this->output = $output;
        $this->helperSet = $helperSet;
    }

    public function create($file, $contents)
    {
        if ($this->fs->exists($file)) {
            if ($this->input->getOption(static::OPTION_SKIP_EXISTING)) {
                return;
            }

            if (!$this->input->getOption(static::OPTION_FORCE)) {
                $question = new ConfirmationQuestion("<info>$file</info> exists. Overwrite? (y/N) ", false);
                //add another option - view a diff by creating a temp file and comparing
                if (!$this->helperSet->get('question')->ask($this->input, $this->output, $question)) {
                    return;
                }
            }
        }

        $this->forceCreate($file, $contents);
    }

    public function forceCreate($file, $contents)
    {
        $this->fs->dumpFile($file, $contents);
        $this->output->writeln(sprintf('Created <info>%s</info>', $file));
    }

    public function createInBundle(BundleInterface $bundle, $relativeFile, $template, array $vars = [])
    {
        $file = $bundle->getPath().'/'.trim($relativeFile, '/');

        return $this->create($file, $this->render($template, $vars));
    }

    public function render($template, array $vars = [])
    {
        return $this->twig->render('@PerformDev/skeletons/'.$template, $vars);
    }

    public function createBundleClass(BundleInterface $bundle, $relativeClass, $template, array $vars = [])
    {
        list($file, $vars) = $this->resolveBundleClass($bundle, $relativeClass, $vars);

        return $this->create($file, $this->render($template, $vars));
    }

    public function resolveBundleClass(BundleInterface $bundle, $relativeClass, array $vars = [])
    {
        $relativeClass = trim($relativeClass, '\\');
        $classname = sprintf('%s\\%s', $bundle->getNamespace(), $relativeClass);
        $file = sprintf('%s/%s.php', $bundle->getPath(), str_replace('\\', '/', $relativeClass));
        $classBasename = substr(basename($file), 0, -4);
        $namespace = sprintf('%s\\%s', $bundle->getNamespace(), str_replace('/', '\\', dirname(str_replace('\\', '/', $relativeClass))));

        $vars['classname'] = $classBasename;
        $vars['namespace'] = $namespace;

        return [$file, $vars];
    }

    public function chmod($file, $mode = 0644)
    {
        $this->fs->chmod($file, $mode);
    }

    public function chmodInBundle(BundleInterface $bundle, $relativeFile, $mode = 0644)
    {
        $file = $bundle->getPath().'/'.trim($relativeFile, '/');

        $this->fs->chmod($file, $mode);
    }

    public static function addInputOptions(Command $command)
    {
        $command->addOption(static::OPTION_SKIP_EXISTING, 's', InputOption::VALUE_NONE, 'Don\'t prompt to overwrite files that already exist.')
            ->addOption(static::OPTION_FORCE, '', InputOption::VALUE_NONE, 'Always overwrite existing files.');
    }
}
