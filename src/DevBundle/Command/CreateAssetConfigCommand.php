<?php

namespace Perform\DevBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Perform\DevBundle\File\FileCreator;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CreateAssetConfigCommand extends Command
{
    protected $creator;
    protected $projectDir;

    public function __construct(FileCreator $creator, $projectDir)
    {
        $this->creator = $creator;
        $this->projectDir = $projectDir;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('perform-dev:create:asset-config')
            ->setDescription('Create a webpack configuration for building project assets.');

        FileCreator::addInputOptions($this);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $files = [
            'package.json' => 'package.json.twig',
            'webpack.config.js' => 'webpack.config.js.twig',
            '.babelrc' => '.babelrc.twig',
        ];
        foreach ($files as $target => $source) {
            $this->creator->create($this->projectDir.'/'.$target, $this->creator->render($source));
        }
    }
}
