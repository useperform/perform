<?php

namespace Perform\PageEditorBundle\Persister;

use Doctrine\ORM\EntityManagerInterface;
use Perform\PageEditorBundle\Entity\Version;
use Perform\RichContentBundle\Persister\Persister as ContentPersister;
use Perform\PageEditorBundle\Entity\Section;

/**
 * Updates a version and its content.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Persister
{
    protected $contentPersister;
    protected $em;

    /**
     * @param ContentPersister       $contentPersister
     * @param EntityManagerInterface $em
     */
    public function __construct(ContentPersister $contentPersister, EntityManagerInterface $em)
    {
        $this->contentPersister = $contentPersister;
        $this->em = $em;
    }

    /**
     * Save a version with content operations, ensuring the content
     * is linked to the version.
     *
     * @param VersionUpdate $update
     */
    public function save(VersionUpdate $update)
    {
        // only new content, or content already linked to this version, is allowed.
        $version = $update->getVersion();
        $versionContent = $version->getAllContent();
        foreach ($update->getContentOperations() as $operation) {
            if ($operation instanceof UpdateOperation && !in_array($operation->getContent(), $versionContent, true)) {
                throw new MismatchedContentException(sprintf('Attempting to update a rich content entity with id "%s", but it is not linked to any section in the version with id "%s"', $operation->getContent()->getId(), $version->getId()));
            }
        }

        $this->em->beginTransaction();
        try {
            // save all rich content first, ensuring they all have database ids for setting on sections
            $results = $this->contentPersister->saveMany($update->getContentOperations());

            // set the content entities on each section, ensuring the sections exist
            $sectionResults = [];
            $sectionNames = $update->getSectionNames();
            foreach ($results as $index => $result) {
                $sectionName = $sectionNames[$index];
                $section = $version->getSection($sectionName);
                if (!$section) {
                    $section = new Section();
                    $section->setName($sectionName);
                    $section->setVersion($version);
                }
                $section->setContent($result->getContent());
                $this->em->persist($section);
                $sectionResults[$sectionName] = $result;
            }

            $version->setUpdatedAt(new \DateTime());
            $this->em->persist($version);
            $this->em->flush();
            $this->em->commit();

            return $sectionResults;
        } catch (\Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }
}
