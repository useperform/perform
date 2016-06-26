<?php

namespace Admin\Team\DataFixtures\ORM;

use Faker;
use Doctrine\Common\Persistence\ObjectManager;
use Admin\Team\Entity\TeamMember;
use Admin\Base\DataFixtures\ORM\EntityDeclaringFixtureInterface;

/**
 * LoadTeamMemberData.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LoadTeamMemberData implements EntityDeclaringFixtureInterface
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
        for ($i = 0; $i < 10; ++$i) {
            $member = new TeamMember();
            $member->setName($faker->firstName.' '.$faker->lastName);
            $member->setRole($roles[array_rand($roles)]);
            $member->setDescription(implode("\n\n", $faker->paragraphs(5)));
            $member->setSortOrder($i);
            $manager->persist($member);
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
            TeamMember::class,
        ];
    }
}
