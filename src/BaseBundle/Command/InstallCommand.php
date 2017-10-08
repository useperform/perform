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
                'no-config',
                'c',
                InputOption::VALUE_NONE,
                "Only run installers that don't require configuration"
            )
            ->addOption(
                'dry-run',
                'd',
                InputOption::VALUE_NONE,
                'Only print the installers that would have been run'
            )
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
        foreach ($this->getInstallers($input, $output) as $installer) {
            $output->writeln(sprintf('Running <info>%s</info>', get_class($installer)));

            if ($input->getOption('dry-run')) {
                continue;
            }

            $installer->install($this->getContainer(), new ConsoleLogger($output));
        }
    }

    protected function getInstallers(InputInterface $input, OutputInterface $output)
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

        $installers = $this->filterNames($installers, $input->getOption('only-installers'));

        return $this->filterNoConfig($output, $installers, $input->getOption('no-config'));
    }

    protected function filterNames(array $installers, array $only)
    {
        if (empty($only)) {
            return $installers;
        }

        return array_filter($installers, function ($installer) use ($only) {
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

    protected function filterNoConfig(OutputInterface $output, array $installers, $noConfig)
    {
        if (!$noConfig) {
            return $installers;
        }

        return array_filter($installers, function ($installer) use ($output) {
            if (!$installer->requiresConfiguration()) {
                return true;
            }
            if ($output->isVerbose()) {
                $output->writeln(sprintf('Skipping <info>%s</info> as it requires configuration', get_class($installer)));
            }

            return false;
        });
    }
}
