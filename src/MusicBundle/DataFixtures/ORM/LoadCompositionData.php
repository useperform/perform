<?php

namespace Perform\MusicBundle\DataFixtures\ORM;

use Faker;
use Doctrine\Common\Persistence\ObjectManager;
use Perform\MusicBundle\Entity\Composition;
use Perform\BaseBundle\DataFixtures\ORM\EntityDeclaringFixtureInterface;

/**
 * LoadCompositionData.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LoadCompositionData implements EntityDeclaringFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('en_GB');
        for ($i = 0; $i < 10; ++$i) {
            $c = new Composition();
            $c->setTitle($faker->sentence);
            $c->setPublishDate($faker->dateTimeThisDecade);
            $c->setDuration(floor(rand(30, 4000) / 60) * 60);
            $c->setDescription(implode("\n\n", $faker->paragraphs(3)));
            $manager->persist($c);
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
            Composition::class,
        ];
    }
}
