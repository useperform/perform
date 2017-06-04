<?php

namespace Perform\MailingListBundle\DataFixtures\ORM;

use Faker;
use Doctrine\Common\Persistence\ObjectManager;
use Perform\BaseBundle\DataFixtures\ORM\EntityDeclaringFixtureInterface;
use Perform\MailingListBundle\Entity\LocalSubscriber;
use Perform\MailingListBundle\Entity\LocalList;

/**
 * LoadLocalData.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LoadLocalData implements EntityDeclaringFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('en_GB');

        for ($i = 1; $i < 6; $i++) {
            $list = new LocalList();
            $list->setName('Test list '.$i);
            $list->setSlug('test-list-'.$i);
            $manager->persist($list);

            for ($j = 0; $j < 10; ++$j) {
                $subscriber = new LocalSubscriber();
                if (rand(0, 1)) {
                    $subscriber->setFirstName($faker->firstName);
                }

                $subscriber->setEmail($faker->safeEmail);
                $subscriber->setList($list);
                $manager->persist($subscriber);
            }
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
            LocalList::class,
            LocalSubscriber::class,
        ];
    }
}
