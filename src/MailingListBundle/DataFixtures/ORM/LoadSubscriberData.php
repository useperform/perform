<?php

namespace Admin\MailingListBundle\DataFixtures\ORM;

use Faker;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Admin\MailingListBundle\Entity\Subscriber;

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
        for ($i = 0; $i < 20; $i++) {
            $subscriber = new Subscriber();
            $subscriber->setForename($faker->firstName);
            $subscriber->setSurname($faker->lastName);
            $subscriber->setEmail($subscriber->getForename().'.'.$subscriber->getSurname().'@example.com');
            $subscriber->setEnabled(true);
            $manager->persist($subscriber);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
