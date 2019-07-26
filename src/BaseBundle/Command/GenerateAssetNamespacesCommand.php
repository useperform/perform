<?php

namespace Perform\BaseBundle\Command;

use Perform\BaseBundle\Asset\Dumper\Dumper;
use Perform\BaseBundle\Asset\Dumper\NamespacesTarget;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateAssetNamespacesCommand extends Command
{
    private $dumper;
    private $projectDir;
    private $namespaces;

    public function __construct(Dumper $dumper, string $projectDir, array $namespaces)
    {
        $this->dumper = $dumper;
        $this->projectDir = $projectDir;
        $this->namespaces = $namespaces;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('perform:assets:namespaces')
            ->setDescription('Generate assets/namespaces.js')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $this->projectDir.'/assets/namespaces.js';
        $this->dumper->dump(new NamespacesTarget($file, $this->namespaces));

        $output->writeln(sprintf('Generated <info>%s</info>.', $file));
    }
}
