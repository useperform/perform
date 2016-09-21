<?php

namespace Perform\CmsBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * PublishVersionCommand.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 */
class PublishVersionCommand extends ContainerAwareCommand
{
    protected $name = 'admin:cms:publish-version';
    protected $description = 'Publish a version of a page.';

    protected function configure()
    {
        $this->setName($this->name)
            ->setDescription($this->description)
            ->addArgument(
                'page',
                InputArgument::OPTIONAL,
                'The page to publish.'
            )
            ->addArgument(
                'version',
                InputArgument::OPTIONAL,
                'The version to publish.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $page = $input->getArgument('page');
        $repo = $this->getContainer()
              ->get('doctrine.orm.entity_manager')
              ->getRepository('PerformCmsBundle:Version');
        $helper = $this->getHelper('question');
        if (!$page) {
            $pages = $repo->getPageNames();

            $question = new ChoiceQuestion('Select page', $pages);
            $page = $helper->ask($input, $output, $question);
        }

        $title = $input->getArgument('version');
        if (!$title) {
            $titles = $repo->getTitlesForPage($page);

            //change to autocomplete if number of versions is above 15 or so.
            $question = new ChoiceQuestion('Select version', $titles);
            $title = $helper->ask($input, $output, $question);
        }

        $version = $repo->findOneBy([
            'page' => $page,
            'title' => $title,
        ]);

        if (!$version) {
            throw new \Exception(sprintf('Version not found for page "%s" with title "%s"', $page, $title));
        }

        $this->getContainer()->get('admin_cms.publisher')->publishVersion($version);
        $output->writeln(sprintf('Published page <info>%s</info>, title <info>%s</info>', $page, $title));
    }
}
