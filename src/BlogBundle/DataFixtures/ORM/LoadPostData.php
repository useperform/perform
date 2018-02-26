<?php

namespace Perform\BlogBundle\DataFixtures\ORM;

use Faker;
use Doctrine\Common\Persistence\ObjectManager;
use Perform\BlogBundle\Entity\MarkdownPost;
use Perform\BaseBundle\DataFixtures\ORM\EntityDeclaringFixtureInterface;
use Perform\BaseBundle\Entity\Tag;

/**
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
        ] as $title) {
            $tag = new Tag();
            $tag->setTitle($title);
            $tag->setDiscriminator('blog');
            $tags[] = $tag;
        }

        for ($i = 0; $i < 10; ++$i) {
            $post = new MarkdownPost();
            $post->setTitle($faker->sentence);
            $post->setSlug(strtolower(str_replace(' ', '-', $post->getTitle())));
            $post->setPublishDate($faker->dateTimeThisYear);
            $post->setMarkdown(implode("\n\n", $faker->paragraphs(5)));
            $post->setStatus(MarkdownPost::STATUS_PUBLISHED);
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
            MarkdownPost::class,
            Tag::class,
        ];
    }
}
