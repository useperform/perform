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
            ->setDescription('Create a scss theme in a given directory')
            ->addArgument('filename', InputArgument::REQUIRED, 'The filename, e.g. assets/scss/themes/my-theme.scss');

        FileCreator::addInputOptions($this);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getArgument('filename');

        $this->creator->create($filename, $this->creator->render('theme.scss.twig'));

        $withoutExtension = pathinfo($filename, PATHINFO_FILENAME);

        $output->writeln(['', 'To use this theme, import it at the top of a scss file:', '']);
        $output->write(<<<EOF
<info>---</info>
@import "~bootstrap/scss/functions";

@import "path/to/themes/{$withoutExtension}";
<info>---</info>

EOF
        );
        $output->writeln(['', 'Or if you have an asset namespace configured:', '']);
        $output->write(<<<EOF
<info>---</info>
@import "~bootstrap/scss/functions";

@import "~myapp/scss/themes/{$withoutExtension}";
<info>---</info>

EOF
        );
        $output->writeln(['', 'Import paths are relative to the file they are imported from.', 'Remember to start a namespaced import with a tilde (~).']);
    }
}
