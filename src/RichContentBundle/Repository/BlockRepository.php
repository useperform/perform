<?php

namespace Perform\RichContentBundle\Repository;

use Perform\RichContentBundle\Entity\Block;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class BlockRepository extends EntityRepository
{
    public function createFromDefinitions(array $definitions)
    {
        $blocks = [];

        foreach ($definitions as $def) {
            $this->checkDefinition($def);
            $block = new Block();
            $block->setType($def['type']);
            $block->setValue($def['value']);
            $this->_em->persist($block);
            $blocks[] = $block;
        }

        $this->_em->flush();

        return $blocks;
    }

    public function updateFromDefinitions(array $definitions)
    {
        $blocks = $this->findByIds(array_keys($definitions));

        foreach ($blocks as $block) {
            $def = $definitions[$block->getId()];
            $this->checkDefinition($def);
            $block->setType($def['type']);
            $block->setValue($def['value']);
            $this->_em->persist($block);
        }

        $this->_em->flush();

        return $blocks;
    }

    public function findByIds(array $ids)
    {
        $blocks = $this->createQueryBuilder('b')
                ->where('b.id IN (:ids)')
                ->getQuery()
                ->setParameter('ids', $ids)
                ->getResult();

        if (count($blocks) !== count($ids)) {
            throw new EntityNotFoundException(sprintf('Unable to find all requested blocks with ids %s.', implode($ids, ', ')));
        }

        return $blocks;
    }

    protected function checkDefinition(array $def)
    {
        if (!isset($def['type'])) {
            throw new \InvalidArgumentException('A block definition must define a type.');
        }
        if (!isset($def['value'])) {
            throw new \InvalidArgumentException('A block definition must define a value.');
        }
    }
}
