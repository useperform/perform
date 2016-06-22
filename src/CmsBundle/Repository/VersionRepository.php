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
}
