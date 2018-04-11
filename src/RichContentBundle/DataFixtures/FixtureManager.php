<?php

namespace Perform\RichContentBundle\DataFixtures;

use Doctrine\ORM\EntityManagerInterface;
use Perform\RichContentBundle\DataFixtures\Profile\ProfileRegistry;
use Perform\RichContentBundle\Entity\Content;
use Perform\RichContentBundle\BlockType\BlockTypeRegistry;
use Perform\RichContentBundle\Exception\BlockTypeNotFoundException;

/**
 * A helper service to generate sample rich content with data that matches a variety of 'profiles'.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FixtureManager
{
    protected $profileRegistry;
    protected $blockTypeRegistry;
    protected $em;

    public function __construct(ProfileRegistry $profileRegistry, BlockTypeRegistry $blockTypeRegistry, EntityManagerInterface $em)
    {
        $this->profileRegistry = $profileRegistry;
        $this->blockTypeRegistry = $blockTypeRegistry;
        $this->em = $em;
    }

    public function generate($profileName = null)
    {
        $content = new Content();
        $content->setTitle('Sample content');
        $profile = $profileName ? $this->profileRegistry->get($profileName) : $this->profileRegistry->getRandom();
        foreach ($profile->getRequiredBlockTypes() as $blockTypeName) {
            if (!$this->blockTypeRegistry->has($blockTypeName)) {
                throw new BlockTypeNotFoundException(sprintf('The rich content fixture profile "%s" requires the "%s" block type, but this block type was not found.', $profileName, $blockTypeName));
            }
        }

        foreach ($blocks = $profile->generateBlocks() as $block) {
            $this->em->persist($block);
        }
        $this->em->flush();
        foreach ($blocks as $block) {
            $content->addBlock($block);
        }
        $this->em->persist($content);
        $this->em->flush();

        return $content;
    }
}
