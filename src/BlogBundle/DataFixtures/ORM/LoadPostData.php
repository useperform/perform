<?php

namespace Admin\Team\DataFixtures\ORM;

use Faker;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Admin\BlogBundle\Entity\Post;

/**
 * LoadTeamMemberData.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LoadPostData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();
        for ($i = 0; $i < 10; ++$i) {
            $post = new Post();
            $post->setTitle($faker->sentence);
            $post->setPublishDate($faker->dateTimeThisYear);
            $post->setContent(implode("\n\n", $faker->paragraphs(5)));
            $post->setEnabled(rand(0, 2) ? true : false);
            $manager->persist($post);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
