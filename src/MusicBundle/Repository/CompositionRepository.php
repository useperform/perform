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

    public function findNestedByCategory()
    {
        $results = $this->findBy([], ['publishDate' => 'DESC']);
        $nested = [];
        foreach ($results as $result) {
            $cat = $result->getCategory();
            if (!$cat) {
                continue;
            }

            if (!isset($nested[$cat])) {
                $nested[$cat] = [];
            }

            $nested[$cat][] = $result;
        }

        ksort($nested);

        return $nested;
    }
}
