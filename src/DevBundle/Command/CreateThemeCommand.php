<?php

namespace Perform\DevBundle\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Perform\DevBundle\File\FileCreator;
use Symfony\Component\Console\Question\Question;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CreateThemeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('perform-dev:create:theme')
            ->setDescription('Create scss theme files')
            ->addArgument('theme', InputArgument::OPTIONAL, 'The namespaced theme name, e.g. AppBundle:my_theme');

        FileCreator::addInputOptions($this);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        list($bundle, $theme) = $this->getTheme($input, $output);

        $creator = $this->getContainer()->get('perform_dev.file_creator');
        $vars = ['theme' => $theme];
        $creator->createInBundle($bundle, sprintf('Resources/scss/themes/%s/variables.scss', $theme), 'theme_variables.scss.twig', $vars);
        $creator->createInBundle($bundle, sprintf('Resources/scss/themes/%s/theme.scss', $theme), 'theme.scss.twig', $vars);

        $output->writeln(['', sprintf('To use this theme, set the <comment>perform_base.theme</comment> configuration option to <info>%s:%s</info>.', $bundle->getName(), $theme)]);
    }

    protected function getTheme(InputInterface $input, OutputInterface $output)
    {
        $kernel = $this->getContainer()->get('kernel');

        if ($input->getArgument('theme')) {
            $full = $input->getArgument('theme');
            $pieces = explode(':', $full, 2);
            if (count($pieces) !== 2) {
                throw new \RuntimeException('Theme name must be of the form <Bundle>:<theme>, e.g. AppBundle:my_theme');
            }
            $bundle = $kernel->getBundle($pieces[0]);

            return [$bundle, $pieces[1]];
        }

        $q = new Question('Bundle to create the theme in: ');
        $q->setAutocompleterValues(array_keys($kernel->getBundles()));
        $bundle = $kernel->getBundle($this->getHelper('question')->ask($input, $output, $q));

        $q = new Question('Name of the theme, e.g. <info>my_theme</info>: ');
        $theme = $this->getHelper('question')->ask($input, $output, $q);

        return [$bundle, $theme];
    }
}
