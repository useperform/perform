<?php

namespace Perform\UserBundle\DataFixtures\ORM;

use Faker;
use Doctrine\Common\Persistence\ObjectManager;
use Perform\UserBundle\Entity\User;
use Perform\BaseBundle\DataFixtures\ORM\EntityDeclaringFixtureInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LoadUserData implements EntityDeclaringFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();
        $user = new User();
        $user->setForename('Glynn');
        $user->setSurname('Forrest');
        $user->setEmail('me@glynnforrest.com');
        $user->setPlainPassword('glynn');
        $user->addRole('ROLE_ADMIN');
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

    public function getEntityClasses()
    {
        return [
            User::class,
        ];
    }
}
