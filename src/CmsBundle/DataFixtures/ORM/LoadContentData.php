<?php

namespace Perform\CmsBundle\DataFixtures\ORM;

use Perform\CmsBundle\Entity\Block;
use Perform\CmsBundle\Entity\Version;
use Perform\CmsBundle\Entity\Section;
use Doctrine\Common\Persistence\ObjectManager;
use Perform\BaseBundle\DataFixtures\ORM\EntityDeclaringFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Perform\CmsBundle\Annotation\Page;
use Perform\BaseBundle\Util\BundleSearcher;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * LoadContentData.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LoadContentData implements EntityDeclaringFixtureInterface, ContainerAwareInterface
{
    protected $container;
    protected $faker;
    protected $blockTypes;

    public function load(ObjectManager $manager)
    {
        $this->faker = \Faker\Factory::create();
        $this->loadBlockTypes();
        $locator = $this->container->get('perform_cms.page_locator');
        foreach ($locator->getPageNames() as $page => $sections) {
            $count = rand(2, 5);
            for ($i = 0; $i < $count; ++$i) {
                $this->createVersion($manager, $page, $i, $sections);
            }
        }

        $manager->flush();

        if (!$this->container) {
            return;
        }
        $publisher = $this->container->get('perform_cms.publisher');

        //publish the first version of each page
        $firstVersions = $manager->createQuery(
            'SELECT v FROM PerformCmsBundle:Version v GROUP BY v.page'
        )->getResult();

        foreach ($firstVersions as $version) {
            $publisher->publishVersion($version);
        }
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    protected function loadBlockTypes()
    {
        if (!$this->container) {
            return;
        }

        $this->blockTypes = $this->container
                          ->get('perform_cms.block_type_registry')
                          ->getTypes();

        if (empty($this->blockTypes)) {
            throw new InvalidConfigurationException('At least one cms block type must be configured to run fixtures.');
        }
    }

    protected function createVersion(ObjectManager $manager, $page, $number, array $sections)
    {
        $version = new Version();
        $version->setTitle('Version number '.$number);
        $version->setPage($page);

        foreach ($sections as $sectionName) {
            $section = new Section();
            $section->setName($sectionName);
            $version->addSection($section);
            $manager->persist($section);

            $count = rand(2, 5);
            for ($k = 0; $k < $count; ++$k) {
                $block = $this->createBlock($section);
                $manager->persist($block);
            }
        }
        $manager->persist($version);
    }

    protected function createBlock(Section $section)
    {
        $type = array_rand($this->blockTypes);
        $block = new Block();
        $block->setType($type);

        // the block types should be responsible for this in the future
        switch ($type) {
        case 'html':
            $block->setValue(['content' => '<p>'.$this->faker->sentence.'</p>']);
        case 'text':
            $block->setValue(['content' => $this->faker->sentence]);
        }

        $section->addBlock($block);

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
