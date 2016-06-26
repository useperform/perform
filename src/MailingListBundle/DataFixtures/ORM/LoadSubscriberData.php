<?php

namespace Admin\MailingListBundle\DataFixtures\ORM;

use Faker;
use Doctrine\Common\Persistence\ObjectManager;
use Admin\MailingListBundle\Entity\Subscriber;
use Admin\Base\DataFixtures\ORM\EntityDeclaringFixtureInterface;

/**
 * LoadSubscriberData.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LoadSubscriberData implements EntityDeclaringFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();
        for ($i = 0; $i < 20; ++$i) {
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

    public function getEntityClasses()
    {
        return [
            Subscriber::class,
        ];
    }
}
