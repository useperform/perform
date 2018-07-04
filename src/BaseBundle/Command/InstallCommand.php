<?php

namespace Perform\BaseBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputOption;
use Perform\BaseBundle\Installer\InstallerInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Perform\BaseBundle\Installer\BundleAwareInstallerInterface;
use Perform\BaseBundle\DependencyInjection\LoopableServiceLocator;

class InstallCommand extends ContainerAwareCommand
{
    protected $installers;

    public function __construct(LoopableServiceLocator $installers)
    {
        $this->installers = $installers;
        parent::__construct();
    }

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
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = new ConsoleLogger($output);

        foreach ($this->getInstallers($input, $output) as $installer) {
            $msg = sprintf($installer instanceof BundleAwareInstallerInterface ?
                           'Running bundle-aware <info>%s</info>' :
                           'Running <info>%s</info>',
                           get_class($installer));
            $output->writeln($msg);

            if ($input->getOption('dry-run')) {
                continue;
            }

            if ($installer instanceof BundleAwareInstallerInterface) {
                $installer->installBundles($usedBundles, $logger);
                continue;
            }

            $installer->install($logger);
        }
    }

    protected function getInstallers(InputInterface $input, OutputInterface $output)
    {
        $filteredNames = $input->getOption('only-installers');
        $noConfig = $input->getOption('no-config');

        $installers = [];
        foreach ($this->installers as $installer) {
            if (!$this->nameMatches($installer, $filteredNames)) {
                continue;
            }
            if (!$this->canRun($output, $installer, $noConfig)) {
                continue;
            }

            $installers[] = $installer;
        }

        return $installers;
    }

    protected function nameMatches(InstallerInterface $installer, array $only)
    {
        if (empty($only)) {
            return true;
        }

        $class = get_class($installer);
        $pieces = explode('\\', $class);
        $end = strtolower(end($pieces));
        foreach ($only as $item) {
            if ($item === $class || strtolower($item) === $end || strtolower($item).'installer' === $end) {
                return true;
            }
        }

        return false;
    }

    protected function canRun(OutputInterface $output, InstallerInterface $installer, $noConfig)
    {
        if (!$noConfig || !$installer->requiresConfiguration()) {
            return true;
        }
        if ($output->isVerbose()) {
            $output->writeln(sprintf('Skipping <info>%s</info> as it requires configuration', get_class($installer)));
        }

        return false;
    }
}
