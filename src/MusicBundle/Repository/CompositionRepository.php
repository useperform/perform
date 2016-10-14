<?php

namespace Perform\MusicBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * CompositionRepository
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CompositionRepository extends EntityRepository
{
    public function findNestedByYear()
    {
        $results = $this->findBy([], ['publishDate' => 'DESC']);
        $nested = [];
        foreach ($results as $result) {
            $year = $result->getPublishDate()->format('Y');

            if (!isset($nested[$year])) {
                $nested[$year] = [];
            }

            $nested[$year][] = $result;
        }

        return $nested;
    }
}
