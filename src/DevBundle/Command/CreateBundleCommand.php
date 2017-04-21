<?php

namespace Perform\DevBundle\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputOption;

/**
 * CreateBundleCommand.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CreateBundleCommand extends CreateCommand
{
    protected function configure()
    {
        $this->setName('perform-dev:create:bundle')
            ->setDescription('Create a new bundle.')
            ->addArgument(
                'namespace',
                InputArgument::OPTIONAL,
                'Namespace of the new bundle.'
            )
            ->addOption(
                'app-bundle',
                'a',
                InputOption::VALUE_NONE,
                'Create a simplified AppBundle local to this app only.'
            )
            ->setHelp(<<<EOF
Create a new bundle.

Pass the --app-bundle option to create a local bundle in src/AppBundle.

With this option, css, javascript, and other asset files will be created in the root of this project, instead of inside the bundle's Resources/ folder.

EOF
            )
            ;
        parent::configure();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $namespace = $this->getNamespace($input, $output);
        $dir = sprintf('%s/../src/%s', $this->getContainer()->getParameter('kernel.root_dir'), str_replace('\\', '/', $namespace));
        $bundleName = str_replace('\\', '', $namespace);
        $file = sprintf('%s/%s.php', $dir, $bundleName);
        $vars = [
            'namespace' => $namespace,
            'classname' => $bundleName,
        ];

        $this->createFile($input, $output, $file, 'Bundle.php.twig', $vars);
    }

    protected function getNamespace(InputInterface $input, OutputInterface $output)
    {
        if ($input->hasOption('app-bundle')) {
            if ($input->getArgument('namespace')) {
                $output->writeln('<error>Warning</error> --app-bundle option passed. Ignoring supplied bundle namespace.');
            }

            return 'AppBundle';
        }

        $namespace = $input->getArgument('namespace');
        if (!$namespace) {
            $question = new Question('Bundle namespace, e.g. <info>MyCo/SiteBundle</info> or <info>AppBundle</info>: (AppBundle) ', 'AppBundle');
            $namespace = $this->getHelper('question')->ask($input, $output, $question);
        }

        return trim(str_replace('/', '\\', $namespace), '\\');
    }
}
