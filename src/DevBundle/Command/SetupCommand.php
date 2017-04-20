<?php

namespace Perform\DevBundle\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Perform\DevBundle\File\ComposerConfig;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * SetupCommand.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SetupCommand extends ContainerAwareCommand
{
    protected $input;
    protected $output;

    protected function configure()
    {
        $this->setName('perform-dev:setup')
            ->setDescription('Run the initial development setup for this application.')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->configureComposer($input, $output);
        //remove files from base project
        //create files not in base project
        //install and configure other perform bundles
        //create a frontend bundle
        //composer update
    }

    protected function configureComposer(InputInterface $input, OutputInterface $output)
    {
        if ($output->isVerbose()) {
            $output->writeln('Configuring composer.json...');
        }

        $config = new ComposerConfig($this->getContainer()->get('kernel')->getRootDir().'/../composer.json');
        $name = $config->getProperty('name');

        if (!$name || $name === 'perform/project-base') {
            $q = new Question('Composer project name: ');
            $config->update(['name' => $this->getHelper('question')->ask($input, $output, $q)]);
        }

        $q = new ConfirmationQuestion('Do you want to use the incenteev parameter handler to interactively update parameters.yml? (y/N) ');
        $cmd = 'Incenteev\\ParameterHandler\\ScriptHandler::buildParameters';

        if ($this->getHelper('question')->ask($input, $output, $q)) {
            $config->update([
                'require' => [
                    'incenteev/composer-parameter-handler' => '^2.0',
                ],
                'extra' => [
                    'incenteev-parameters' => ['file' => 'app/config/parameters.yml'],
                ],
            ]);

            $scripts = $config->getConfig()['scripts'];
            foreach (['post-install-cmd', 'post-update-cmd'] as $type) {
                $s = $scripts[$type];
                $key = array_search($cmd, $s);
                if ($key === false) {
                    $s[] = $cmd;
                }
                //array_values required so json will output an array, not a dict
                $scripts[$type] = array_values($s);
            }
            $config->replace('scripts', $scripts);
        } else {
            $config->update([
                'require' => [
                    'incenteev/composer-parameter-handler' => null,
                ],
                'extra' => [
                    'incenteev-parameters' => null,
                ],
            ]);

            $scripts = $config->getConfig()['scripts'];
            foreach (['post-install-cmd', 'post-update-cmd'] as $type) {
                $s = $scripts[$type];
                $key = array_search($cmd, $s);
                if ($key !== false) {
                    unset($s[$key]);
                }
                //array_values required so json will output an array, not a dict
                $scripts[$type] = array_values($s);
            }
            $config->replace('scripts', $scripts);
        }
    }
}
