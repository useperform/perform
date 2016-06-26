<?php

namespace Admin\BlogBundle\DataFixtures\ORM;

use Faker;
use Doctrine\Common\Persistence\ObjectManager;
use Admin\BlogBundle\Entity\Post;
use Admin\Base\DataFixtures\ORM\EntityDeclaringFixtureInterface;

/**
 * LoadTeamMemberData.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LoadPostData implements EntityDeclaringFixtureInterface
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

    public function getEntityClasses()
    {
        return [
            Post::class,
        ];
    }
}
