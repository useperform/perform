<?php

namespace Perform\CmsBundle\DataFixtures\ORM;

use Perform\CmsBundle\Entity\Block;
use Perform\CmsBundle\Entity\Version;
use Perform\CmsBundle\Entity\Section;
use Doctrine\Common\Persistence\ObjectManager;
use Perform\Base\DataFixtures\ORM\EntityDeclaringFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Perform\CmsBundle\Annotation\Page;
use Perform\Base\Util\BundleSearcher;

/**
 * LoadContentData.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LoadContentData implements EntityDeclaringFixtureInterface, ContainerAwareInterface
{
    protected $container;
    protected $faker;

    public function load(ObjectManager $manager)
    {
        $this->faker = \Faker\Factory::create();
        foreach ($this->locatePages() as $page => $sections) {
            $count = rand(2, 5);
            for ($i = 0; $i < $count; ++$i) {
                $this->createVersion($manager, $page, $i, $sections);
            }
        }

        $manager->flush();
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    protected function locatePages()
    {
        if (!$this->container) {
            return [];
        }
        $searcher = new BundleSearcher($this->container);
        $controllers = $searcher->findClassesInNamespaceSegment('Controller');
        $reader = $this->container->get('annotation_reader');
        $pages = [];

        foreach ($controllers as $class) {
            $r = new \ReflectionClass($class);
            foreach ($r->getMethods() as $method) {
                $annotation = $reader->getMethodAnnotation($method, Page::class);
                if (!$annotation) {
                    continue;
                }

                $page = $annotation->getPage();
                if (!isset($pages[$page])) {
                    $pages[$page] = [];
                }

                $pages[$page] = array_merge($pages[$page], $annotation->getSections());
            }
        }

        return $pages;
    }

    protected function createVersion(ObjectManager $manager, $page, $number, array $sections)
    {
        $version = new Version();
        $version->setTitle('Version number '.$number);
        $version->setPage($page);
        $manager->persist($version);

        foreach ($sections as $sectionName) {
            $section = new Section();
            $section->setName($sectionName);
            $section->setVersion($version);
            $manager->persist($section);

            $count = rand(2, 5);
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
