<?php

namespace Perform\CmsBundle\Publisher;

use Doctrine\ORM\EntityManagerInterface;
use Perform\CmsBundle\Entity\Version;
use Perform\CmsBundle\Entity\PublishedSection;
use Perform\CmsBundle\Block\BlockTypeRegistry;

/**
 * Publisher.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Publisher
{
    protected $entityManager;
    protected $connection;
    protected $registry;

    public function __construct(EntityManagerInterface $entityManager, BlockTypeRegistry $registry)
    {
        $this->entityManager = $entityManager;
        $this->connection = $entityManager->getConnection();
        $this->registry = $registry;
    }

    public function publishVersion(Version $version)
    {
        $this->connection->beginTransaction();

        try {
            $this->entityManager->getRepository('PerformCmsBundle:PublishedSection')
                ->deletePage($version->getPage());

            $this->entityManager->getRepository('PerformCmsBundle:Version')
                ->markPublished($version);

            foreach ($this->createPublishedSections($version) as $section) {
                $this->entityManager->persist($section);
            }
            $this->entityManager->flush();

            $this->connection->commit();
        } catch (\Exception $e) {
            $this->connection->rollback();
            throw $e;
        }
    }

    /**
     * Create PublishedSection entities for a given version.
     *
     * @param Version $version
     *
     * @return []PublishedSection
     */
    public function createPublishedSections(Version $version)
    {
        $publishedSections = [];

        foreach ($version->getSections() as $section) {
            $published = new PublishedSection();
            $published->setName($section->getName());
            $published->setPage($version->getPage());
            $html = '';
            foreach ($section->getBlocks() as $block) {
                $html .= $this->registry->renderBlock($block);
            }
            $published->setContent($html);

            $publishedSections[] = $published;
        }

        return $publishedSections;
    }
}
