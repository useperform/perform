<?php

namespace Perform\DevBundle\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Perform\DevBundle\File\FileCreator;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CreateThemeCommand extends ContainerAwareCommand
{
    protected $creator;

    public function __construct(FileCreator $creator)
    {
        $this->creator = $creator;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('perform-dev:create:theme')
            ->setDescription('Create scss theme files in a given directory')
            ->addArgument('directory', InputArgument::REQUIRED, 'The directory to store the theme files, e.g. src/Resources/scss/themes/my-theme');

        FileCreator::addInputOptions($this);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = trim($input->getArgument('directory'), '/').'/';
        $this->creator->create($dir.'variables.scss', $this->creator->render('theme_variables.scss.twig'));
        $this->creator->create($dir.'theme.scss', $this->creator->render('theme.scss.twig'));

        $output->writeln(['', 'To use this theme, ensure an asset namespace exists for a parent directory (e.g. app -> src/Resources), then set the <comment>perform_base.assets.theme</comment> configuration node to the theme directory (e.g. "~app/scss/themes/my-theme).', 'Remember to start the reference with a tilde (~).']);
    }
}
