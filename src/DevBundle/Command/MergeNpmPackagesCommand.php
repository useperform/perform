<?php

namespace Perform\DevBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Perform\DevBundle\Packaging\NpmMergeResultCollection;
use Perform\DevBundle\Packaging\NpmMerger;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MergeNpmPackagesCommand extends Command
{
    protected $projectDir;
    protected $npmConfigs;

    public function __construct($projectDir, array $npmConfigs)
    {
        $this->projectDir = $projectDir;
        $this->npmConfigs = $npmConfigs;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('perform-dev:merge-npm-packages')
            ->setDescription('Fetch package.json dependencies from bundles and add them to this project\'s package.json')
            ->addOption('dry-run', '', InputOption::VALUE_NONE)
            ->addOption('force', '', InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $merger = new NpmMerger();
        $existingFile = $this->projectDir.'/package.json';
        $collection = new NpmMergeResultCollection($merger->loadRequirements($existingFile));

        foreach ($this->npmConfigs as $file) {
            $result = $merger->mergeRequirements($collection->getResolvedRequirements(), $merger->loadRequirements($file));
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

        $merger->writeRequirements($existingFile, $collection->getResolvedRequirements());
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
