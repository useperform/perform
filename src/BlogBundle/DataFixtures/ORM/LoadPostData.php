<?php

namespace Perform\BlogBundle\DataFixtures\ORM;

use Faker;
use Doctrine\Common\Persistence\ObjectManager;
use Perform\BlogBundle\Entity\Post;
use Perform\BaseBundle\DataFixtures\ORM\EntityDeclaringFixtureInterface;
use Perform\BlogBundle\Entity\Tag;

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
        $tags = [];
        foreach (['Music',
                  'Performances',
                  'Members',
                  'Funding',
                  'Teaching',
                  'Tour',
        ] as $name) {
            $tag = new Tag();
            $tag->setName($name);
            $tags[] = $tag;
        }

        for ($i = 0; $i < 10; ++$i) {
            $post = new Post();
            $post->setTitle($faker->sentence);
            $post->setPublishDate($faker->dateTimeThisYear);
            $post->setContent(implode("\n\n", $faker->paragraphs(5)));
            $post->setEnabled(rand(0, 2) ? true : false);
            $tagAmount = rand(0, 5);
            for ($j = 0; $j < $tagAmount; $j++) {
                $post->addTag($tags[array_rand($tags)]);
            }

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
            Tag::class,
        ];
    }
}
