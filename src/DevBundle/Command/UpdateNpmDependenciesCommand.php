<?php

namespace Perform\DevBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Perform\DevBundle\Packaging\NpmMergeResultCollection;
use Perform\DevBundle\Packaging\NpmMerger;

class UpdateNpmDependenciesCommand extends Command
{
    protected $projectDir;
    protected $deps;

    public function __construct(string $projectDir, array $deps)
    {
        $this->projectDir = $projectDir;
        $this->deps = $deps;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('perform:update-npm-dependencies')
            ->setDescription('Automatically find npm dependencies from enabled bundles and add them to this project\'s package.json')
            ->addOption('dry-run', '', InputOption::VALUE_NONE)
            ->addOption('force', '', InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $merger = new NpmMerger();
        $targetFile = $this->projectDir.'/package.json';
        $collection = new NpmMergeResultCollection($merger->loadRequirements($targetFile));

        foreach ($this->deps as $deps) {
            $packages = $deps->getDependencies();
            if ($output->isVeryVerbose()) {
                $output->writeln(sprintf('<comment>%d</comment> dependencies returned from <comment>%s</comment>', count($packages), get_class($deps)));
            }

            $result = $merger->mergeRequirements($collection->getResolvedRequirements(), $packages);
            $collection->addResult($result);
        }

        $this->writeNew($output, $collection);
        $this->writeUnresolved($output, $collection);

        if ($collection->hasUnresolved() && !$input->getOption('force')) {
            $output->writeln('Not writing to <comment>package.json</comment>. Use the --force argument for a best-effort attempt.');

            return;
        }

        if (!$collection->hasNew()) {
            $output->writeln('No new dependencies detected - your package.json is up to date.');

            return;
        }
        if ($input->getOption('dry-run')) {
            return;
        }

        $merger->writeRequirements($targetFile, $collection->getResolvedRequirements());
        $output->writeln('Updated <comment>package.json</comment>. Please confirm changes and commit the result to version control.');
    }

    private function writeNew(OutputInterface $output, NpmMergeResultCollection $collection)
    {
        if (!$collection->hasNew()) {
            return;
        }

        $new = $collection->getNewRequirements();
        $output->writeln(['', '<info>Package updates:</info>', '']);

        foreach ($new as $package => $versions) {
            $msg = $versions[0] ?
                 sprintf('<comment>%s</comment>: Updating from %s to %s', $package, $versions[0], $versions[1]) :
                 sprintf('<comment>%s</comment>: Adding %s', $package, $versions[1]);
            $output->writeln($msg);
        }
        $output->writeln('');
    }

    private function writeUnresolved(OutputInterface $output, NpmMergeResultCollection $collection)
    {
        if (!$collection->hasUnresolved()) {
            return;
        }
        $unresolved = $collection->getUnresolvedRequirements();
        $output->writeln(['', '<error>Package conflicts:</error>', '']);
        foreach ($unresolved as $package => $conflict) {
            $output->writeln(sprintf('<comment>%s</comment>: Versions %s and %s are not compatible', $package, $conflict[0], $conflict[1]));
        }
        $output->writeln('');
    }
}
