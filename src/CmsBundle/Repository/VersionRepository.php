<?php

namespace Admin\CmsBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Admin\CmsBundle\Entity\Version;

/**
 * VersionRepository
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class VersionRepository extends EntityRepository
{
    /**
     * Mark a version as published, marking all other versions for that page as
     * unpublished.
     */
    public function markPublished(Version $version)
    {
        $query = $this->_em->createQuery(
            'UPDATE AdminCmsBundle:Version v SET v.published = false WHERE v.page = :page'
        );
        $query->setParameter('page', $version->getPage());
        $query->execute();

        $version->setPublished(true);
        $this->_em->persist($version);
        $this->_em->flush();
    }

    /**
     * Get the names of all pages.
     */
    public function getPageNames()
    {
        $query = $this->_em->createQuery(
            'SELECT DISTINCT v.page FROM AdminCmsBundle:Version v ORDER BY v.page ASC'
        );

        return array_map(function($result) {
            return $result['page'];
        }, $query->getScalarResult());
    }

    /**
     * Get all version titles for a page.
     */
    public function getTitlesForPage($page)
    {
        $query = $this->_em->createQuery(
            'SELECT v.title FROM AdminCmsBundle:Version v WHERE v.page = :page'
        );
        $query->setParameter('page', $page);

        return array_map(function($result) {
            return $result['title'];
        }, $query->getScalarResult());
    }
}
