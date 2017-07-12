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

        foreach ($definitions as $key => $def) {
            $this->checkDefinition($def);
            $block = new Block();
            $block->setType($def['type']);
            $block->setValue($def['value']);
            $this->_em->persist($block);
            $blocks[$key] = $block;
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

    /**
     * Get the number of times a given block is used.
     *
     * @return int
     */
    public function getUsageCount(Block $block)
    {
        $count = $this->_em->createQueryBuilder('c')
               ->select('COUNT (b)')
               ->from('PerformRichContentBundle:Content', 'c')
               ->join('c.blocks', 'b')
               ->where('b = :block')
               ->getQuery()
               ->setParameter('block', $block)
               ->getSingleScalarResult();

        return (int) $count;
    }

    /**
     * Get the number of times different blocks are used.
     *
     * [
     *     'some_guid' => [
     *         'count' => 1,
     *         'block' => $block1
     *     ],
     *     'some_other_guid' => [
     *         'count' => 3,
     *         'block' => $block2
     *     ]
     * ];
     *
     * @return array An array indexed by block id, where each value is
     * a 2-member array containing the usage count and the block
     * entity itself.
     *
     * $repository->getUsageCount([$block1, $block2])
     */
    public function getUsageCounts(array $blocks)
    {
        $blocksById = [];
        foreach ($blocks as $block) {
            $blocksById[$block->getId()] = $block;
        }

        $results = $this->_em->createQueryBuilder('c')
                 ->select('b.id, COUNT(1) AS used')
                 ->from('PerformRichContentBundle:Content', 'c')
                 ->innerJoin('c.blocks', 'b')
                 ->where('b IN (:blocks)')
                 ->groupBy('b')
                 ->getQuery()
                 ->setParameter('blocks', $blocks)
                 ->getScalarResult();

        $counts = [];
        foreach ($results as $result) {
            $counts[$result['id']] = [
                'count' => $result['used'],
                'block' => $blocksById[$result['id']]
            ];
        }

        // the usage of a block is 0 if it wasn't in the result set
        foreach ($blocksById as $id => $block) {
            if (!isset($counts[$id])) {
                $counts[$id] = [
                    'count' => 0,
                    'block' => $block
                ];
            }
        }

        return $counts;
    }

    public function removeIfUnused(array $blocks)
    {
        foreach ($this->getUsageCounts($blocks) as $result) {
            if ($result['count'] === 0) {
                $this->_em->remove($result['block']);
            }
        }

        $this->_em->flush();
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
