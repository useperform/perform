<?php

namespace Perform\MediaPlayerBundle\DataFixtures\ORM;

use Faker;
use Doctrine\Common\Persistence\ObjectManager;
use Perform\MediaPlayerBundle\Entity\Playlist;
use Perform\MediaPlayerBundle\Entity\PlaylistItem;
use Perform\BaseBundle\DataFixtures\ORM\EntityDeclaringFixtureInterface;

/**
 * LoadPlaylistData.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LoadPlaylistData implements EntityDeclaringFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();
        $mediaRepo = $manager->getRepository('PerformMediaBundle:File');
        $audio = $mediaRepo->findByType('audio');

        for ($i = 0; $i < 10; ++$i) {
            $playlist = new Playlist();
            $playlist->setTitle($faker->sentence);

            $items = rand(0, count($audio));
            for ($j = 0; $j < $items; $j++) {
                $item = new PlaylistItem();
                $item->setFile($audio[array_rand($audio)]);
                $item->setSortOrder($j);
                $playlist->addItem($item);
            }

            $manager->persist($playlist);
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
            Playlist::class,
            PlaylistItem::class,
        ];
    }
}
