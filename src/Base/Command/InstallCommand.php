<?php

namespace Admin\Base\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Admin\Base\Util\BundleSearcher;

class InstallCommand extends ContainerAwareCommand
{
    protected $name = 'admin:base:install';
    protected $description = 'Install required settings and configuration.';

    protected function configure()
    {
        $this->setName($this->name)
            ->setDescription($this->description)
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->getInstallers() as $installer) {
            $output->writeln(sprintf('Running <info>%s</info>', get_class($installer)));
            $installer->install($this->getContainer(), new ConsoleLogger($output));
        }
    }

    protected function getInstallers()
    {
        $searcher = new BundleSearcher($this->getContainer());
        $classes = $searcher->findClassesInNamespaceSegment('Installer');
        $installers = [];

        foreach ($classes as $class) {
            $r = new \ReflectionClass($class);
            if (!$r->isSubclassOf('Admin\\Base\\Installer\\InstallerInterface') || $r->isAbstract()) {
                continue;
            }
            $installers[] = $r->newInstance();
        }

        return $installers;
    }
}
