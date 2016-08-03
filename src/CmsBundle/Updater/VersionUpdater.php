<?php

namespace Admin\CmsBundle\Updater;

use Doctrine\ORM\EntityManagerInterface;
use Admin\CmsBundle\Entity\Block;
use Admin\CmsBundle\Entity\Version;

/**
 * VersionUpdater
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class VersionUpdater
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function update(Version $version, array $data)
    {
        foreach ($data as $sectionName => $sectionData) {
            $section = $version->getOrCreateSection($sectionName);
            foreach ($blocks = $section->getBlocks() as $block) {
                $blocks->removeElement($block);
                $this->entityManager->remove($block);
            }

            foreach ($sectionData as $index => $blockData) {
                //move to block type registry, which validates incoming block data
                //for each type
                $block = new Block();
                $block->setType($blockData['type']);
                $block->setValue($blockData['value']);
                $section->addBlock($block, $index);
            }
            $this->entityManager->persist($section);
        }

        $this->entityManager->flush();
    }
}
