<?php

namespace Perform\RichContentBundle\BlockType;

use Perform\RichContentBundle\Entity\Block;
use Perform\MediaBundle\Importer\FileImporter;
use Perform\MediaBundle\Repository\FileRepository;

/**
 * Block type for displaying images.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ImageBlockType implements BlockTypeInterface
{
    protected $manager;
    protected $repository;

    public function __construct(FileImporter $manager, FileRepository $repository)
    {
        $this->manager = $manager;
        $this->repository = $repository;
    }

    public function render(Block $block)
    {
        $file = $this->getFile($block);
        if (!$file) {
            return '';
        }

        return sprintf('<img src="%s" />', $this->manager->getUrl($file));
    }

    public function getDescription()
    {
        return 'Images from the media library.';
    }

    public function getDefaults()
    {
        return [];
    }

    private function getFile(Block $block)
    {
        $value = $block->getValue();
        if (!isset($value['id'])) {
            return;
        }

        return $this->repository->find($value['id']);
    }

    public function getComponentInfo(Block $block)
    {
        $file = $this->getFile($block);
        if (!$file) {
            return [
                'missing' => true,
            ];
        }

        return [
            'src' => $this->manager->getUrl($file),
        ];
    }
}
