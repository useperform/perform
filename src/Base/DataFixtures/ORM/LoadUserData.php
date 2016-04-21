<?php

namespace Admin\Base\DataFixtures\ORM;

use Faker;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Admin\Base\Entity\User;

/**
 * LoadUserData
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();
        $user = new User();
        $user->setForename('Glynn');
        $user->setSurname('Forrest');
        $user->setEmail('me@glynnforrest.com');
        $user->setPlainPassword('glynn');
        $manager->persist($user);

        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setForename($faker->firstName);
            $user->setSurname($faker->lastName);
            $user->setEmail($user->getForename().'.'.$user->getSurname().'@example.com');
            $user->setPlainPassword(strtolower($user->getForename()));
            $manager->persist($user);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
