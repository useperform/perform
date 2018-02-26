<?php

namespace Perform\MailingListBundle\DataFixtures\ORM;

use Faker;
use Doctrine\Common\Persistence\ObjectManager;
use Perform\BaseBundle\DataFixtures\ORM\EntityDeclaringFixtureInterface;
use Perform\MailingListBundle\Entity\LocalSubscriber;
use Perform\MailingListBundle\Entity\LocalList;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LoadLocalData implements EntityDeclaringFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('en_GB');

        $lists = [];
        for ($i = 1; $i < 6; $i++) {
            $list = new LocalList();
            $list->setName('Test list '.$i);
            $list->setSlug('test-list-'.$i);
            $manager->persist($list);
            $lists[] = $list;
        }

        for ($i = 0; $i < 10; ++$i) {
            $subscriber = new LocalSubscriber();
            if (rand(0, 1)) {
                $subscriber->setFirstName($faker->firstName);
            }

            $subscriber->setEmail($faker->safeEmail);
            $listsToAdd = rand(0, count($lists));
            for ($j = 0; $j < $listsToAdd; $j++) {
                $subscriber->addList($lists[array_rand($lists)]);
            }

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
            LocalList::class,
            LocalSubscriber::class,
        ];
    }
}
