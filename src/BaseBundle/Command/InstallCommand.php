<?php

namespace Perform\BaseBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputOption;
use Perform\BaseBundle\Installer\InstallerInterface;

class InstallCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('perform:install')
            ->setDescription('Install required settings and configuration.')
            ->setHelp('This command should be run <info>after</info> the cache has been warmed up.')
            ->addOption(
                'only-installers',
                'i',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'Only run installers matching the given names'
            )
            ;
        BundleFilter::addOptions($this);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->getInstallers($input) as $installer) {
            $output->writeln(sprintf('Running <info>%s</info>', get_class($installer)));
            $installer->install($this->getContainer(), new ConsoleLogger($output));
        }
    }

    protected function getInstallers(InputInterface $input)
    {
        $classes = $this->getContainer()->get('perform_base.bundle_searcher')
                 ->findClassesWithNamespaceSegment(
                     'Installer',
                     null,
                     BundleFilter::filterBundleNames($input, $this->getApplication()->getKernel()->getBundles()));
        $installers = [];

        foreach ($classes as $class) {
            $r = new \ReflectionClass($class);
            if (!$r->isSubclassOf(InstallerInterface::class) || $r->isAbstract()) {
                continue;
            }
            $installers[] = $r->newInstance();
        }

        $only = $input->getOption('only-installers');
        if (!empty($only)) {
            $installers = array_filter($installers, function ($installer) use ($only) {
                $class = get_class($installer);
                $pieces = explode('\\', $class);
                $end = strtolower(end($pieces));
                foreach ($only as $item) {
                    if ($item === $class || strtolower($item) === $end || strtolower($item).'installer' === $end) {
                        return true;
                    }
                }

                return false;
            });
        }

        return $installers;
    }
}
