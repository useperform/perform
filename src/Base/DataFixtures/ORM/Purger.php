<?php

namespace Admin\Base\DataFixtures\ORM;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

/**
 * Purger that only deletes entities that are explicitly declared from fixtures.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Purger extends ORMPurger
{
    /**
     * @param []EntityDeclaringFixtureInterface $fixtures
     * @param EntityManagerInterface            $manager
     */
    public function __construct(EntityManagerInterface $manager, array $fixtures)
    {
        $declaredClasses = [];
        foreach ($fixtures as $fixture) {
            if (!$fixture instanceof EntityDeclaringFixtureInterface) {
                throw new \InvalidArgumentException(sprintf('Fixtures must implement %s to use %s', EntityDeclaringFixtureInterface::class, __CLASS__));
            }

            $declaredClasses = array_merge($declaredClasses, $fixture->getEntityClasses());
        }

        //replace the metadata driver with one that only returns the given classes
        $driver = new ExplicitMappingDriver($manager->getConfiguration()->getMetadataDriverImpl(), $declaredClasses);
        $manager->getConfiguration()->setMetadataDriverImpl($driver);

        parent::__construct($manager);
    }
}
