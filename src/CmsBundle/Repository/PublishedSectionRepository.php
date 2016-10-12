<?php

namespace Perform\CmsBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * PublishedSectionRepository
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PublishedSectionRepository extends EntityRepository
{
    /**
     * Remove all published sections for a page.
     *
     * @param string $page
     */
    public function deletePage($page)
    {
        $query = $this->_em->createQuery(
            'DELETE FROM PerformCmsBundle:PublishedSection s WHERE s.page = :page'
        );
        $query->setParameter('page', $page);
        $query->execute();
    }
}