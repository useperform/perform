<?php

namespace Perform\BaseBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;

/**
 * EntityDeclaringFixtureInterface.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface EntityDeclaringFixtureInterface extends OrderedFixtureInterface, FixtureInterface
{
    /**
     * Get the entity classes this fixture affects.
     *
     * @return array
     */
    public function getEntityClasses();
}
