<?php

namespace Perform\ContactBundle\DataFixtures\ORM;

use Faker;
use Perform\ContactBundle\Entity\Message;
use Doctrine\Common\Persistence\ObjectManager;
use Perform\BaseBundle\DataFixtures\ORM\EntityDeclaringFixtureInterface;

/**
 * LoadMessageData.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LoadMessageData implements EntityDeclaringFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();
        $statuses = [
            Message::STATUS_READ,
            Message::STATUS_UNREAD,
            Message::STATUS_SPAM,
        ];
        for ($i = 0; $i < 50; ++$i) {
            $message = new Message();
            $message->setName($faker->name);
            $message->setEmail($faker->safeEmail);
            $message->setMessage($faker->paragraph);
            $message->setStatus($statuses[array_rand($statuses)]);
            $manager->persist($message);
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
            Message::class,
        ];
    }
}
