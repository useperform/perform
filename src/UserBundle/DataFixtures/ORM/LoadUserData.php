<?php

namespace Perform\UserBundle\DataFixtures\ORM;

use Faker;
use Doctrine\Common\Persistence\ObjectManager;
use Perform\UserBundle\Entity\User;
use Perform\BaseBundle\DataFixtures\ORM\EntityDeclaringFixtureInterface;
use Psr\Log\NullLogger;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Perform\UserBundle\Installer\UsersInstaller;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LoadUserData implements EntityDeclaringFixtureInterface, ContainerAwareInterface
{
    protected $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();
        $installer = new UsersInstaller();
        $installer->install($this->container, new NullLogger());

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
