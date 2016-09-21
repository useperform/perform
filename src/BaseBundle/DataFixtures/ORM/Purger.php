<?php

namespace Perform\BaseBundle\DataFixtures\ORM;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\Internal\CommitOrderCalculator;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Purger that only deletes entities that are explicitly declared from fixtures.
 *
 * If no entities are declared, all entities will be deleted.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Purger extends ORMPurger
{
    protected $manager;
    protected $declaredClasses = [];

    /**
     * @param EntityManagerInterface            $manager
     * @param []EntityDeclaringFixtureInterface $fixtures
     */
    public function __construct(EntityManagerInterface $manager, array $declaredClasses)
    {
        $this->manager = $manager;
        $this->declaredClasses = $declaredClasses;
    }

    public function purge()
    {
        $metadatas = [];
        foreach ($this->getDeclaredMetadata() as $metadata) {
            if (!$metadata->isMappedSuperclass && !(isset($metadata->isEmbeddedClass) && $metadata->isEmbeddedClass)) {
                $metadatas[] = $metadata;
            }
        }

        $commitOrder = $this->getCommitOrder($this->manager, $metadatas);

        // Get platform parameters
        $platform = $this->manager->getConnection()->getDatabasePlatform();

        // Drop association tables first
        $orderedTables = $this->getAssociationTables($commitOrder, $platform);

        // Drop tables in reverse commit order
        for ($i = count($commitOrder) - 1; $i >= 0; --$i) {
            $class = $commitOrder[$i];

            if (
                ($class->isInheritanceTypeSingleTable() && $class->name != $class->rootEntityName) ||
                (isset($class->isEmbeddedClass) && $class->isEmbeddedClass) ||
                $class->isMappedSuperclass
            ) {
                continue;
            }

            $orderedTables[] = $class->getQuotedTableName($platform);
        }

        foreach ($orderedTables as $tbl) {
            $this->manager->getConnection()->executeUpdate('DELETE FROM '.$tbl);
        }
    }

    protected function getDeclaredMetadata()
    {
        if (empty($this->declaredClasses)) {
            return $this->manager->getMetadataFactory()->getAllMetadata();
        }

        $metadatas = [];
        foreach ($this->declaredClasses as $class) {
            $metadatas[] = $this->manager->getClassMetadata($class);
        }

        return $metadatas;
    }

    private function getCommitOrder(EntityManagerInterface $em, array $classes)
    {
        $calc = new CommitOrderCalculator();

        foreach ($classes as $class) {
            $calc->addClass($class);

            // $class before its parents
            foreach ($class->parentClasses as $parentClass) {
                $parentClass = $em->getClassMetadata($parentClass);

                if (!$calc->hasClass($parentClass->name)) {
                    $calc->addClass($parentClass);
                }

                $calc->addDependency($class, $parentClass);
            }

            foreach ($class->associationMappings as $assoc) {
                if ($assoc['isOwningSide']) {
                    $targetClass = $em->getClassMetadata($assoc['targetEntity']);

                    //only include this entity if it has been declared
                    if (!empty($this->declaredClasses) && !in_array($targetClass->name, $this->declaredClasses)) {
                        continue;
                    }

                    if (!$calc->hasClass($targetClass->name)) {
                        $calc->addClass($targetClass);
                    }

                    // add dependency ($targetClass before $class)
                    $calc->addDependency($targetClass, $class);

                    // parents of $targetClass before $class, too
                    foreach ($targetClass->parentClasses as $parentClass) {
                        $parentClass = $em->getClassMetadata($parentClass);

                        if (!$calc->hasClass($parentClass->name)) {
                            $calc->addClass($parentClass);
                        }

                        $calc->addDependency($parentClass, $class);
                    }
                }
            }
        }

        return $calc->getCommitOrder();
    }

    /**
     * @param array                                     $classes
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     *
     * @return array
     */
    private function getAssociationTables(array $classes, AbstractPlatform $platform)
    {
        $associationTables = array();

        foreach ($classes as $class) {
            foreach ($class->associationMappings as $assoc) {
                if ($assoc['isOwningSide'] && $assoc['type'] == ClassMetadata::MANY_TO_MANY) {
                    if (isset($assoc['joinTable']['schema'])) {
                        $associationTables[] = $assoc['joinTable']['schema'].'.'.$class->getQuotedJoinTableName($assoc, $platform);
                    } else {
                        $associationTables[] = $class->getQuotedJoinTableName($assoc, $platform);
                    }
                }
            }
        }

        return $associationTables;
    }
}
