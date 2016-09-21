<?php

namespace Perform\EventsBundle\DataFixtures\ORM;

use Faker;
use Doctrine\Common\Persistence\ObjectManager;
use Perform\EventsBundle\Entity\Event;
use Perform\BaseBundle\DataFixtures\ORM\EntityDeclaringFixtureInterface;

/**
 * LoadEventData.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LoadEventData implements EntityDeclaringFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('en_GB');
        for ($i = 0; $i < 10; ++$i) {
            $event = new Event();
            $event->setTitle($faker->sentence);
            $event->setDescription(implode("\n\n", $faker->paragraphs(3)));
            $event->setLocation($faker->address);
            $event->setStartTime($faker->dateTimeBetween('-1 year', '1 year'));
            $event->setEnabled(true);
            $manager->persist($event);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }

    public function getEntityClasses()
    {
        return [
            Event::class,
        ];
    }
}
