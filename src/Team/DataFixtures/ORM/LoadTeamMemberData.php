<?php

namespace Admin\Team\DataFixtures\ORM;

use Faker;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Admin\Team\Entity\TeamMember;

/**
 * LoadTeamMemberData
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LoadTeamMemberData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();
        $roles = [
            'Conductor',
            'Violin',
            'Cello',
            'Trumpet',
            'Trombone',
            'Percussion',
        ];
        for ($i = 0; $i < 10; $i++) {
            $member = new TeamMember();
            $member->setName($faker->firstName.' '.$faker->lastName);
            $member->setRole($roles[array_rand($roles)]);
            $member->setDescription($faker->paragraph);
            $manager->persist($member);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
