<?php

namespace Admin\CmsBundle\DataFixtures\ORM;

use Admin\CmsBundle\Entity\Block;
use Admin\CmsBundle\Entity\Version;
use Admin\CmsBundle\Entity\Section;
use Doctrine\Common\Persistence\ObjectManager;
use Admin\Base\DataFixtures\ORM\EntityDeclaringFixtureInterface;

/**
 * LoadContentData.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LoadContentData implements EntityDeclaringFixtureInterface
{
    protected $faker;

    public function load(ObjectManager $manager)
    {
        $this->faker = \Faker\Factory::create();
        $pages = [
            'home',
            'about',
        ];
        foreach ($pages as $page) {
            $count = rand(1, 5);
            for ($i = 0; $i < $count; ++$i) {
                $this->createVersion($manager, $page, $i);
            }
        }

        $manager->flush();
    }

    protected function createVersion(ObjectManager $manager, $page, $number)
    {
        $version = new Version();
        $version->setTitle('Version number '.$number);
        $version->setPage($page);
        $manager->persist($version);

        foreach (['main', 'aside'] as $sectionName) {
            $section = new Section();
            $section->setName($sectionName);
            $section->setVersion($version);
            $manager->persist($section);

            $count = rand(1, 4);
            for ($k = 0; $k < $count; ++$k) {
                $block = $this->createBlock($section, $k);
                $manager->persist($block);
            }
        }
    }

    protected function createBlock(Section $section, $sortOrder)
    {
        $block = new Block();
        $block->setType('html');
        $block->setSection($section);
        $block->setSortOrder($sortOrder);
        $block->setValue(['content' => '<p>'.$this->faker->sentence.'</p>']);

        return $block;
    }

    public function getOrder()
    {
        return 1;
    }

    public function getEntityClasses()
    {
        return [
            Version::class,
            Section::class,
            Block::class,
        ];
    }
}
