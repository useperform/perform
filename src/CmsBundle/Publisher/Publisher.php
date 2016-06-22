<?php

namespace Admin\CmsBundle\Publisher;

use Doctrine\ORM\EntityManagerInterface;
use Admin\CmsBundle\Entity\Version;
use Admin\CmsBundle\Entity\PublishedSection;
use Admin\CmsBundle\Block\HtmlBlockType;

/**
 * Publisher.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Publisher
{
    protected $entityManager;
    protected $connection;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->connection = $entityManager->getConnection();
    }

    public function publishVersion(Version $version)
    {
        $this->connection->beginTransaction();

        try {
            $this->entityManager->getRepository('AdminCmsBundle:PublishedSection')
                ->deletePage($version->getPage());

            $this->entityManager->getRepository('AdminCmsBundle:Version')
                ->markPublished($version);

            foreach ($this->createPublishedSections($version) as $section) {
                $this->entityManager->persist($section);
            }
            $this->entityManager->flush();

            // throw new \Exception('oh bother');
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
            $blockType = new HtmlBlockType();
            foreach ($section->getBlocks() as $block) {
                $html .= $blockType->render($block);
            }
            $published->setContent($html);

            $publishedSections[] = $published;
        }

        return $publishedSections;
    }
}
