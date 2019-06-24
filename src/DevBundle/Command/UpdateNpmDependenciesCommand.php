<?php

namespace Perform\DevBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Perform\DevBundle\Npm\DependenciesMerger;

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
        $targetFile = $this->projectDir.'/package.json';
        if (!file_exists($targetFile)) {
            throw new \Exception(sprintf('%s does not exist. You can create a new package.json file quickly with the perform-dev:create:asset-config command.', $targetFile));
        }

        $merger = DependenciesMerger::createFromPackageFile($targetFile);

        foreach ($this->deps as $deps) {
            $packages = $deps->getDependencies();
            if ($output->isVeryVerbose()) {
                $output->writeln(sprintf('<comment>%d</comment> dependencies returned from <comment>%s</comment>', count($packages), get_class($deps)));
            }

            $merger->mergeDependencies($packages, get_class($deps));
        }

        $this->showUpdates($output, $merger);
        $this->showConflicts($output, $merger);

        if ($merger->hasConflicts() && !$input->getOption('force')) {
            $output->writeln('Not writing to <comment>package.json</comment>. Use the --force argument for a best-effort attempt.');

            return;
        }

        if (!$merger->hasUpdates()) {
            $output->writeln('No dependency updates required - your package.json is up to date.');

            return;
        }
        if ($input->getOption('dry-run')) {
            return;
        }

        $merger->writeToPackageFile($targetFile);
        $output->writeln('Updated <comment>package.json</comment>. Please confirm changes and commit the result to version control.');
    }

    private function showUpdates(OutputInterface $output, DependenciesMerger $merger)
    {
        if (!$merger->hasUpdates()) {
            return;
        }

        $output->writeln(['', '<info>Package updates:</info>', '']);

        foreach ($merger->getUpdates() as $package => $update) {
            $msg = sprintf('<comment>%s</comment>', $package);
            $msg .= $output->isVerbose() ? sprintf(' from <comment>%s</comment>: ', $update->getSource()) : ': ';
            $msg .= $update->isNew() ?
                sprintf('Adding %s', $update->getNewVersion()) :
                sprintf('Updating from %s to %s', $update->getExistingVersion(), $update->getNewVersion());

            $output->writeln($msg);
        }
        $output->writeln('');
    }

    private function showConflicts(OutputInterface $output, DependenciesMerger $merger)
    {
        if (!$merger->hasConflicts()) {
            return;
        }
        $output->writeln(['', '<error>Package conflicts:</error>', '']);
        foreach ($merger->getConflicts() as $package => $conflict) {
            $output->writeln(sprintf('<comment>%s</comment> from <comment>%s</comment>: Versions %s and %s are not compatible', $package, $conflict->getSource(), $conflict->getExistingVersion(), $conflict->getConflictingVersion()));
        }
        $output->writeln('');
    }
}
