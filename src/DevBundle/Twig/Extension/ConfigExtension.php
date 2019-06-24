<?php

namespace Perform\DevBundle\Twig\Extension;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Grab perform_dev configuration for use in skeleton templates, or
 * prompt for a value if they are missing.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ConfigExtension extends \Twig_Extension
{
    protected $input;
    protected $output;
    protected $helper;
    protected $tree;
    protected $config = [];
    protected $configFile;
    protected $newVars = [];
    protected $introduced;

    public function __construct(ConfigurationInterface $configuration, array $config, $configFile)
    {
        $this->tree = $configuration->getConfigTreeBuilder()
                    ->buildTree()
                    ->getChildren()['skeleton_vars'];
        $this->config = $config;
        $this->configFile = $configFile;
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

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('perform_dev', [$this, 'getConfig']),
        ];
    }

    private function veryVerbose($lines)
    {
        if (!$this->output->isVeryVerbose()) {
            return;
        }

        $this->output->writeln($lines);
    }

    public function getConfig($key)
    {
        if (!isset($this->tree->getChildren()[$key])) {
            throw new \Exception(sprintf('Unknown perform_dev skeleton_var: "%s"', $key));
        }

        $node = $this->tree->getChildren()[$key];

        if (isset($this->config['skeleton_vars'][$key])) {
            $this->veryVerbose(sprintf('Fetching <info>%s</info> from perform_dev configuration.', $key));

            return $this->config['skeleton_vars'][$key];
        }

        if (isset($this->newVars[$key])) {
            return $this->newVars[$key];
        }

        $this->introduceConfigPrompts();

        $this->output->writeln('');
        if ($node->getInfo()) {
            $this->output->writeln(sprintf('<info>%s</info> - %s', $key, $node->getInfo()));
        }

        $question = new Question(sprintf('<info>%s</info>: ', $key));
        $value = $this->helperSet->get('question')->ask($this->input, $this->output, $question);
        $this->newVars[$key] = $value;
        $this->output->writeln('');

        return $value;
    }

    protected function introduceConfigPrompts()
    {
        if ($this->introduced) {
            return;
        }

        $this->output->writeln([
            '',
            'Some <info>perform_dev</info> configuration values are required to render the skeleton templates.',
        ]);

        $this->introduced = true;
    }
}
