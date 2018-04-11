<?php

namespace Perform\PageEditorBundle\Command;

use Perform\PageEditorBundle\Repository\VersionRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 */
class PublishVersionCommand extends Command
{
    protected $repo;

    public function __construct(VersionRepository $repo)
    {
        parent::__construct();
        $this->repo = $repo;
    }

    protected function configure()
    {
        $this->setName('perform:page-editor:publish-version')
            ->setDescription('Publish a version of a page.')
            ->addArgument(
                'version-id',
                InputArgument::OPTIONAL,
                'The database id of the version to publish.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getArgument('version-id') ?: $this->selectVersion($input, $output);
        $version = $this->repo->find($id);
        if (!$version) {
            throw new \Exception(sprintf('Version with id of "%s" was not found.', $id));
        }

        $this->repo->markPublished($version);
        $output->writeln(sprintf('Published page <info>%s</info>, title <info>%s</info>', $version->getPage(), $version->getTitle()));
    }

    protected function selectVersion(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $pages = $this->repo->getPageNames();
        if (empty($pages)) {
            throw new \Exception('No pages found.');
        }

        $question = new ChoiceQuestion('Select page', $pages);
        $page = $helper->ask($input, $output, $question);
        $availableVersions = $this->repo->findBy(['page' => $page], ['updatedAt' => 'DESC']);
        $choices = array_map(function ($version) {
            return $version->getTitle();
        }, $availableVersions);

        //change to autocomplete if number of versions is above 15 or so.
        $choice = $helper->ask($input, $output, new ChoiceQuestion('Select version', $choices));

        return $availableVersions[array_search($choice, $choices)]->getId();
    }
}
